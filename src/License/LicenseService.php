<?php

declare(strict_types=1);

namespace Neo\BasicBundle\License;

use Composer\InstalledVersions;
use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Cache\PageCacheTags;
use Neo\BasicBundle\Security\SecretCipher;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Licence of a paid extension on this installation.
 *
 * - Saving the key activates it at the licence service and stores the signed
 *   certificate (tl_neo_license, one row per extension).
 * - The certificate is checked locally on every request: signature, extension,
 *   host and date. The licence service is not asked for that.
 * - The cron job refreshes the certificate when the service says so (daily).
 *   When the service cannot be reached, the stored certificate keeps working
 *   until its grace period ends.
 *
 * Only key, extension code, the identifier of the installation, its domains and
 * versions are sent; no visitor data. The licence service counts installations per
 * licence and domains per installation; its answer says per domain whether it is
 * covered and why not (LicenseState::$reasons).
 */
final class LicenseService
{
    public const DEFAULT_URL = 'https://license.neo-digital.at';

    private const MAX_DOMAINS = 20;
    private const RETRY_AFTER = 3600;
    private const DEFAULT_INTERVAL = 86400;
    private const UNKNOWN_KEY = 'E601';

    /**
     * Front-end pages carry this tag (LicenseCacheTagListener). When what a page
     * shows changes (licensed or not, edition, domains), the tag is invalidated,
     * so no cached page keeps the credit line after a licence was entered.
     */
    public const CACHE_TAG = 'contao.db.tl_neo_license';

    /**
     * @var array<string, LicenseState>
     */
    private array $states = [];

    private readonly Installation $installation;

    public function __construct(
        private readonly Connection $db,
        private readonly SecretCipher $cipher,
        private readonly HttpClientInterface $httpClient,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger,
        private readonly string $serviceUrl,
        private readonly PageCacheTags|null $cacheTags = null,
        Installation|null $installation = null,
    ) {
        $this->installation = $installation ?? new Installation($db);
    }

    /**
     * State of the licence for a host (default: the host of the current request).
     * An empty host skips the domain check, e.g. in the back end, which may run
     * under another address than the website.
     */
    public function state(string $module, string|null $host = null): LicenseState
    {
        $host = Certificate::normalizeHost($host ?? $this->requestHost());

        return $this->states[$module.'|'.$host] ??= $this->evaluate($module, $this->row($module), $host);
    }

    /**
     * Saves a new key and activates it. An empty key removes the licence from
     * this installation (the service is told so). A key the service does not
     * know for this extension is not kept.
     */
    public function saveKey(string $module, string $package, string $key): LicenseState
    {
        $key = self::normalizeKey($key);
        $row = $this->row($module);
        $oldKey = null !== $row ? $this->cipher->decrypt((string) $row['license_key']) : '';
        $before = $this->visible($module);

        if ('' === $key) {
            if ('' !== $oldKey && null !== $row) {
                // The domains of now and those reported before: a licence service without
                // installations releases per domain.
                $stored = array_map(static fn ($d): string => Certificate::normalizeHost((string) $d), (array) json_decode((string) ($row['domains'] ?? ''), true));
                $this->deactivate($oldKey, array_values(array_unique(array_filter([...$this->domains($row), ...$stored]))));
            }

            $this->db->delete('tl_neo_license', ['module' => $module]);

            $this->refreshPagesIfChanged($module, $before);

            return $this->fresh($module);
        }

        $values = [
            'tstamp' => time(),
            'package' => $package,
            'license_key' => $this->cipher->encrypt($key),
            'next_check_at' => 0,
        ];

        if ($key !== $oldKey) {
            $values += ['payload' => null, 'signature' => '', 'key_id' => '', 'coverage' => null, 'allotment' => null, 'checked_at' => 0, 'last_error' => ''];
        }

        if (null === $row) {
            $this->db->insert('tl_neo_license', $values + ['module' => $module]);
        } else {
            $this->db->update('tl_neo_license', $values, ['module' => $module]);
        }

        $state = $this->check('activate', $module);

        if (LicenseState::INVALID_KEY === $state->reason) {
            $this->db->delete('tl_neo_license', ['module' => $module]);

            $this->fresh($module);
        }

        $this->refreshPagesIfChanged($module, $before);

        return $state;
    }

    /**
     * "Check now" in the back end.
     */
    public function refresh(string $module): LicenseState
    {
        $before = $this->visible($module);
        $state = $this->check('refresh', $module);

        $this->refreshPagesIfChanged($module, $before);

        return $state;
    }

    /**
     * Cron: refresh every certificate whose next check is due.
     */
    public function refreshDue(): void
    {
        try {
            $modules = $this->db->fetchFirstColumn("SELECT module FROM tl_neo_license WHERE license_key <> '' AND next_check_at <= ?", [time()]);
        } catch (\Throwable) {
            return;
        }

        foreach ($modules as $module) {
            $before = $this->visible((string) $module);

            $this->check('refresh', (string) $module);
            $this->refreshPagesIfChanged((string) $module, $before);
        }
    }

    /**
     * The stored key shortened to prefix and last four characters, e.g. "SA-••••-••••-••••-AB12"; '' without key.
     */
    public function keyHint(string $module): string
    {
        return $this->hintFor($this->row($module));
    }

    /**
     * @param array<string, mixed>|null $row
     */
    private function hintFor(array|null $row): string
    {
        $key = null !== $row ? $this->cipher->decrypt((string) $row['license_key']) : '';

        if ('' === $key) {
            return '';
        }

        $parts = explode('-', $key);

        return \count($parts) > 1 ? $parts[0].'-••••-••••-••••-'.substr($key, -4) : '••••'.substr($key, -4);
    }

    /**
     * Upper case without spaces, so a key copied with blanks still matches.
     */
    public static function normalizeKey(string $key): string
    {
        return strtoupper((string) preg_replace('/\s+/', '', html_entity_decode($key, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    private function check(string $action, string $module): LicenseState
    {
        $row = $this->row($module);
        $key = null !== $row ? $this->cipher->decrypt((string) $row['license_key']) : '';

        if (null === $row || '' === $key) {
            return $this->fresh($module);
        }

        $domains = $this->domains($row);
        $update = ['tstamp' => time(), 'domains' => json_encode($domains, JSON_UNESCAPED_SLASHES)];

        try {
            $response = $this->httpClient->request(
                'POST',
                $this->serviceUrl.'/v1/licenses/'.$action,
                [
                    'json' => $this->withInstallation([
                        'licenseKey' => $key,
                        'module' => $module,
                        'domains' => $domains,
                        'moduleVersion' => $this->version((string) $row['package']),
                        'system' => 'contao',
                        'systemVersion' => $this->version('contao/core-bundle'),
                        'phpVersion' => PHP_VERSION,
                    ]),
                ],
            );

            $status = $response->getStatusCode();
            $data = $response->toArray(false);
        } catch (\Throwable $e) {
            $this->logger->warning(\sprintf('Basic: licence check of "%s" failed: %s', $module, $e->getMessage()));
            $this->db->update('tl_neo_license', $update + ['last_error' => mb_substr($e->getMessage(), 0, 255), 'next_check_at' => time() + self::RETRY_AFTER], ['module' => $module]);

            return $this->fresh($module);
        }

        $signed = $data['certificate'] ?? null;

        if (200 === $status && \is_array($signed) && isset($signed['payload'], $signed['signature'], $signed['keyId'])) {
            $next = strtotime((string) ($data['nextCheckAfter'] ?? '')) ?: time() + self::DEFAULT_INTERVAL;

            $update += [
                'payload' => (string) $signed['payload'],
                'signature' => (string) $signed['signature'],
                'key_id' => (string) $signed['keyId'],
                'coverage' => json_encode($data['domains'] ?? [], JSON_UNESCAPED_SLASHES),
                'allotment' => self::allotment($data),
                'checked_at' => time(),
                'next_check_at' => $next,
                'last_error' => '',
            ];
        } elseif (404 === $status && self::UNKNOWN_KEY === ($data['errorCode'] ?? '')) {
            // The key is unknown or belongs to another extension (also after a withdrawal).
            $update += ['payload' => null, 'signature' => '', 'key_id' => '', 'coverage' => null, 'allotment' => null, 'checked_at' => time(), 'next_check_at' => time() + self::DEFAULT_INTERVAL, 'last_error' => self::UNKNOWN_KEY];
        } else {
            $message = \sprintf('HTTP %d %s %s', $status, (string) ($data['errorCode'] ?? ''), (string) ($data['message'] ?? ''));
            $update += ['last_error' => mb_substr(trim($message), 0, 255), 'next_check_at' => time() + self::RETRY_AFTER];
        }

        $this->db->update('tl_neo_license', $update, ['module' => $module]);

        return $this->fresh($module);
    }

    /**
     * @param list<string> $domains
     */
    private function deactivate(string $key, array $domains): void
    {
        foreach ($domains as $domain) {
            try {
                $this->httpClient->request('POST', $this->serviceUrl.'/v1/licenses/deactivate', ['json' => $this->withInstallation(['licenseKey' => $key, 'domain' => $domain])])->getStatusCode();
            } catch (\Throwable $e) {
                $this->logger->info(\sprintf('Basic: licence service not told about the removed key (%s): %s', $domain, $e->getMessage()));
            }
        }
    }

    /**
     * @param array<string, mixed>|null $row
     */
    private function evaluate(string $module, array|null $row, string $host): LicenseState
    {
        $key = null !== $row ? (string) $row['license_key'] : '';
        $payload = null !== $row ? (string) ($row['payload'] ?? '') : '';
        $signed = '' !== $payload ? ['payload' => $payload, 'signature' => (string) $row['signature'], 'keyId' => (string) $row['key_id']] : null;
        $hint = $this->hintFor($row);
        $checkedAt = (int) ($row['checked_at'] ?? 0);
        $lastError = (string) ($row['last_error'] ?? '');

        $coverage = [];
        $reasons = [];

        foreach ((array) json_decode((string) ($row['coverage'] ?? ''), true) as $entry) {
            if (\is_array($entry) && isset($entry['domain'])) {
                $covered = (bool) ($entry['covered'] ?? false);
                $coverage[(string) $entry['domain']] = $covered;

                // Licence services before the count of installations send no reason.
                if (\array_key_exists('reason', $entry)) {
                    $reasons[(string) $entry['domain']] = $covered || !\is_string($entry['reason']) || '' === $entry['reason'] ? null : $entry['reason'];
                }
            }
        }

        $allotment = (array) json_decode((string) ($row['allotment'] ?? ''), true);
        $installations = isset($allotment['installations']['allowed'], $allotment['installations']['used'])
            ? ['allowed' => (int) $allotment['installations']['allowed'], 'used' => (int) $allotment['installations']['used']]
            : null;
        $domainsPerInstallation = isset($allotment['domainsPerInstallation']) ? (int) $allotment['domainsPerInstallation'] : null;

        $make = static fn (string $reason, Certificate|null $certificate = null): LicenseState => new LicenseState($module, $reason, $certificate, $signed, $hint, $checkedAt, $lastError, $coverage, $reasons, $installations, $domainsPerInstallation);

        if ('' === $key) {
            return $make(LicenseState::NO_KEY);
        }

        if (null === $signed) {
            return $make(self::UNKNOWN_KEY === $lastError ? LicenseState::INVALID_KEY : LicenseState::NO_CERTIFICATE);
        }

        $certificate = Certificate::verify($signed['payload'], $signed['signature'], $signed['keyId'], PublicKeys::KEYS);

        if (null === $certificate) {
            return $make(LicenseState::UNVERIFIABLE);
        }

        return $make($certificate->reason($module, $host, time()), $certificate);
    }

    /**
     * Domains this installation serves: the domain names of its root pages; a root page
     * without one stands for the host of the request (or, without a request, the host
     * seen last). Normalised, without duplicates, at most 20. Without tl_page (or
     * without any root page) the request host and the domains reported last time.
     *
     * @param array<string, mixed> $row
     *
     * @return list<string>
     */
    private function domains(array $row): array
    {
        $domains = [];
        $withoutDomain = true;
        $hostFirst = false;

        try {
            $roots = $this->db->fetchFirstColumn("SELECT dns FROM tl_page WHERE type = 'root'");
            $withoutDomain = [] === $roots || \in_array('', array_map(static fn ($dns): string => trim((string) $dns), $roots), true);
            $domains = array_map(static fn ($dns): string => (string) $dns, $roots);
        } catch (\Throwable) {
            // tl_page missing: only the request host and the stored domains
            $hostFirst = true;

            foreach ((array) json_decode((string) ($row['domains'] ?? ''), true) as $stored) {
                $domains[] = (string) $stored;
            }
        }

        if ($withoutDomain) {
            $host = Certificate::normalizeHost($this->requestHost());

            if ('' !== $host) {
                $this->installation->rememberHost($host);
            } else {
                $host = $this->installation->host();
            }

            if ($hostFirst) {
                array_unshift($domains, $host);
            } else {
                $domains[] = $host;
            }
        }

        $domains = array_map(static fn ($d): string => Certificate::normalizeHost((string) $d), $domains);
        $domains = array_values(array_unique(array_filter($domains)));

        // Without any domain (no request, nothing seen yet) the domains reported last time.
        if ([] === $domains) {
            foreach ((array) json_decode((string) ($row['domains'] ?? ''), true) as $stored) {
                $domains[] = Certificate::normalizeHost((string) $stored);
            }

            $domains = array_values(array_unique(array_filter($domains)));
        }

        return \array_slice($domains, 0, self::MAX_DOMAINS);
    }

    /**
     * The request to the licence service with the identifier of this installation; without
     * one (database not updated yet) the request stays as before.
     *
     * @param array<string, mixed> $body
     *
     * @return array<string, mixed>
     */
    private function withInstallation(array $body): array
    {
        $installation = $this->installation->id();

        return '' !== $installation ? $body + ['installation' => $installation] : $body;
    }

    /**
     * Installations of the licence (allowed, used) and domains per installation from the
     * answer of the licence service, as stored JSON; null when the service sends neither.
     *
     * @param array<string, mixed> $data
     */
    private static function allotment(array $data): string|null
    {
        $allotment = [];

        if (\is_array($data['installations'] ?? null) && isset($data['installations']['allowed'], $data['installations']['used'])) {
            $allotment['installations'] = ['allowed' => (int) $data['installations']['allowed'], 'used' => (int) $data['installations']['used']];
        }

        if (isset($data['domainsPerInstallation']) && is_numeric($data['domainsPerInstallation'])) {
            $allotment['domainsPerInstallation'] = (int) $data['domainsPerInstallation'];
        }

        return [] !== $allotment ? (string) json_encode($allotment) : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function row(string $module): array|null
    {
        try {
            $row = $this->db->fetchAssociative('SELECT * FROM tl_neo_license WHERE module = ?', [$module]);
        } catch (\Throwable) {
            return null;
        }

        return false === $row ? null : $row;
    }

    /**
     * What pages show for a module: licensed or not, edition and covered domains (not the date of the last check).
     */
    private function visible(string $module): string
    {
        $this->forget($module);

        $state = $this->state($module, '');

        return (string) json_encode([$state->isLicensed(), $state->edition(), $state->coverage]);
    }

    private function refreshPagesIfChanged(string $module, string $before): void
    {
        if (null !== $this->cacheTags && $before !== $this->visible($module)) {
            $this->cacheTags->invalidate([self::CACHE_TAG]);
        }
    }

    private function forget(string $module): void
    {
        foreach (array_keys($this->states) as $cacheKey) {
            if (str_starts_with($cacheKey, $module.'|')) {
                unset($this->states[$cacheKey]);
            }
        }
    }

    private function fresh(string $module): LicenseState
    {
        foreach (array_keys($this->states) as $cacheKey) {
            if (str_starts_with($cacheKey, $module.'|')) {
                unset($this->states[$cacheKey]);
            }
        }

        return $this->state($module);
    }

    private function requestHost(): string
    {
        return (string) $this->requestStack->getMainRequest()?->getHost();
    }

    private function version(string $package): string|null
    {
        try {
            return '' !== $package ? InstalledVersions::getPrettyVersion($package) : null;
        } catch (\OutOfBoundsException) {
            return null;
        }
    }
}
