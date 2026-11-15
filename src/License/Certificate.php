<?php

declare(strict_types=1);

namespace Neo\BasicBundle\License;

/**
 * A licence certificate as signed by the licence service: the payload is the
 * JSON of the certificate, the signature ECDSA P-256 with SHA-256 in DER form
 * (both base64url). Only the signed payload is read; the unsigned copy the
 * service sends alongside is ignored.
 */
final class Certificate
{
    private const VERSION = 1;

    /**
     * @param list<string> $domains registrable domains; each covers its subdomains
     */
    private function __construct(
        public readonly string $licenseId,
        public readonly string $module,
        public readonly string $edition,
        public readonly array $domains,
        public readonly int $paidUntil,
        public readonly int $graceUntil,
        public readonly string $status,
        public readonly int $issuedAt,
    ) {
    }

    /**
     * The certificate when the signature is genuine and the content is complete,
     * otherwise null.
     *
     * @param array<string, string> $publicKeys key id => PEM
     */
    public static function verify(string $payload, string $signature, string $keyId, array $publicKeys): self|null
    {
        if (!isset($publicKeys[$keyId])) {
            return null;
        }

        $data = self::base64UrlDecode($payload);
        $der = self::base64UrlDecode($signature);

        if (null === $data || null === $der || 1 !== openssl_verify($data, $der, $publicKeys[$keyId], OPENSSL_ALGO_SHA256)) {
            return null;
        }

        $json = json_decode($data, true);

        if (!\is_array($json) || self::VERSION !== ($json['version'] ?? null) || $keyId !== ($json['keyId'] ?? null)) {
            return null;
        }

        $paidUntil = self::timestamp($json['paidUntil'] ?? null);
        $graceUntil = self::timestamp($json['graceUntil'] ?? null);
        $issuedAt = self::timestamp($json['issuedAt'] ?? null);

        if (null === $paidUntil || null === $graceUntil || null === $issuedAt || !\is_array($json['domains'] ?? null)) {
            return null;
        }

        return new self(
            (string) ($json['licenseId'] ?? ''),
            strtolower((string) ($json['module'] ?? '')),
            strtolower((string) ($json['edition'] ?? '')),
            array_values(array_map(static fn ($d): string => strtolower(trim((string) $d)), $json['domains'])),
            $paidUntil,
            $graceUntil,
            strtolower((string) ($json['status'] ?? '')),
            $issuedAt,
        );
    }

    /**
     * The host equals a licensed domain or is one of its subdomains.
     */
    public function covers(string $host): bool
    {
        $host = self::normalizeHost($host);

        foreach ($this->domains as $domain) {
            if ('' !== $domain && ($host === $domain || str_ends_with($host, '.'.$domain))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Not withdrawn, not expired, and the grace period not over.
     */
    public function isValidAt(int $time): bool
    {
        return $time <= $this->graceUntil && !\in_array($this->status, ['expired', 'revoked'], true);
    }

    /**
     * What the certificate means for an extension on a host at a time, as a
     * reason of LicenseState, checked in this order: another extension, withdrawn,
     * expired (grace period over), host not covered, valid. An empty host skips
     * the domain check (back end).
     */
    public function reason(string $module, string $host, int $time): string
    {
        return match (true) {
            $this->module !== $module => LicenseState::WRONG_MODULE,
            'revoked' === $this->status => LicenseState::REVOKED,
            !$this->isValidAt($time) => LicenseState::EXPIRED,
            '' !== $host && !$this->covers($host) => LicenseState::DOMAIN,
            default => LicenseState::OK,
        };
    }

    /**
     * Lower case, without port and trailing dot.
     */
    public static function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));
        $host = (string) preg_replace('/:\d+$/', '', $host);

        return rtrim($host, '.');
    }

    private static function base64UrlDecode(string $value): string|null
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return false === $decoded || '' === $decoded ? null : $decoded;
    }

    private static function timestamp(mixed $value): int|null
    {
        if (!\is_string($value) || '' === $value) {
            return null;
        }

        try {
            return (new \DateTimeImmutable($value))->getTimestamp();
        } catch (\Exception) {
            return null;
        }
    }
}
