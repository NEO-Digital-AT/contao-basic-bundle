<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\License;

use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Cache\PageCacheTags;
use Neo\BasicBundle\License\LicenseService;
use Neo\BasicBundle\License\LicenseState;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\BasicBundle\Tests\Support\Database;
use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\ServiceMap;
use Neo\BasicBundle\Tests\Support\TestKeys;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class LicenseServiceTest extends TestCase
{
    private const URL = 'https://license.example.com';

    private const KEY = 'SA-AAAA-BBBB-CCCC-DD12';

    private Connection $db;

    private SecretCipher $cipher;

    private MemoryLogger $logger;

    /**
     * @var list<array{method: string, url: string, body: array<string, mixed>}>
     */
    private array $requests = [];

    /**
     * @var list<MockResponse>
     */
    private array $responses = [];

    private object $tagManager;

    protected function setUp(): void
    {
        $this->db = Database::connection();
        $this->cipher = new SecretCipher('app-secret-for-tests');
        $this->logger = new MemoryLogger();
        $this->tagManager = new class {
            /** @var list<list<string>> */
            public array $invalidated = [];

            /** @param list<string> $tags */
            public function invalidateTags(array $tags): void
            {
                $this->invalidated[] = $tags;
            }
        };
    }

    public function testWithoutAKeyTheExtensionIsUnlicensed(): void
    {
        $state = $this->service()->state('superagent');

        $this->assertSame(LicenseState::NO_KEY, $state->reason);
        $this->assertFalse($state->isLicensed());
        $this->assertSame('', $state->edition());
        $this->assertSame('', $this->service()->keyHint('superagent'));
    }

    public function testSavingAKeyActivatesItAndStoresTheSignedCertificate(): void
    {
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'WWW.Example.com']);
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => '']);
        $this->db->insert('tl_page', ['type' => 'regular', 'dns' => 'ignored.example.net']);

        $signed = TestKeys::certificate();
        $this->responses[] = $this->json([
            'certificate' => $signed,
            'domains' => [['domain' => 'www.example.com', 'covered' => true], ['domain' => 'example.org', 'covered' => false]],
            'nextCheckAfter' => '2030-01-01T00:00:00+00:00',
        ]);

        $state = $this->service('example.org')->saveKey('superagent', 'acme/not-installed', ' sa-aaaa-bbbb-cccc-dd12 ');

        $this->assertCount(1, $this->requests);
        $this->assertSame('POST', $this->requests[0]['method']);
        $this->assertSame(self::URL.'/v1/licenses/activate', $this->requests[0]['url']);
        $this->assertSame(self::KEY, $this->requests[0]['body']['licenseKey']);
        $this->assertSame('superagent', $this->requests[0]['body']['module']);
        $this->assertSame(['www.example.com', 'example.org'], $this->requests[0]['body']['domains']);
        $this->assertNull($this->requests[0]['body']['moduleVersion']);
        $this->assertSame('contao', $this->requests[0]['body']['system']);
        $this->assertSame(PHP_VERSION, $this->requests[0]['body']['phpVersion']);
        $this->assertSame(['licenseKey', 'module', 'domains', 'moduleVersion', 'system', 'systemVersion', 'phpVersion', 'installation'], array_keys($this->requests[0]['body']), 'only key, extension, domains, versions and the installation');
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $this->requests[0]['body']['installation']);
        $this->assertSame($this->requests[0]['body']['installation'], $this->db->fetchOne('SELECT installation_id FROM tl_neo_access'));
        $this->assertSame('example.org', $this->db->fetchOne('SELECT installation_host FROM tl_neo_access'), 'the host that stands for the root page without domain');

        $row = $this->row('superagent');
        $this->assertStringStartsWith('enc:', $row['license_key']);
        $this->assertSame(self::KEY, $this->cipher->decrypt($row['license_key']));
        $this->assertSame('acme/not-installed', $row['package']);
        $this->assertSame($signed['payload'], $row['payload']);
        $this->assertSame($signed['signature'], $row['signature']);
        $this->assertSame(TestKeys::KEY_ID, $row['key_id']);
        $this->assertSame(strtotime('2030-01-01T00:00:00+00:00'), (int) $row['next_check_at']);
        $this->assertSame('', $row['last_error']);
        $this->assertSame(['www.example.com', 'example.org'], json_decode((string) $row['domains'], true));

        // Signed with a key that is not one of the licence service.
        $this->assertSame(LicenseState::UNVERIFIABLE, $state->reason);
        $this->assertSame($signed, $state->signed);
        $this->assertSame(['www.example.com' => true, 'example.org' => false], $state->coverage);
        $this->assertSame('SA-••••-••••-••••-DD12', $state->keyHint);
    }

    public function testAKeyTheServiceDoesNotKnowIsNotKept(): void
    {
        $this->responses[] = $this->json(['errorCode' => 'E601', 'message' => 'Unknown key'], 404);

        $state = $this->service()->saveKey('superagent', 'acme/not-installed', self::KEY);

        $this->assertSame(LicenseState::INVALID_KEY, $state->reason);
        $this->assertNull($this->row('superagent'));
        $this->assertSame(LicenseState::NO_KEY, $this->service()->state('superagent')->reason);
    }

    public function testAWithdrawnKeyIsReportedAsInvalidAtTheNextCheck(): void
    {
        $this->storeLicense(['payload' => 'old', 'signature' => 'old', 'key_id' => 'old']);
        $this->responses[] = $this->json(['errorCode' => 'E601'], 404);

        $state = $this->service()->refresh('superagent');

        $this->assertSame(LicenseState::INVALID_KEY, $state->reason);
        $this->assertNull($state->signed);
        $this->assertSame('E601', $this->row('superagent')['last_error']);
    }

    public function testSavingTheSameKeyAgainKeepsTheCertificateANewKeyDropsIt(): void
    {
        $this->storeLicense(['payload' => 'stored-payload', 'signature' => 'sig', 'key_id' => 'kid']);
        $this->responses[] = $this->json([], 503);
        $this->responses[] = $this->json([], 503);

        $this->service()->saveKey('superagent', 'acme/not-installed', strtolower(self::KEY));
        $this->assertSame('stored-payload', $this->row('superagent')['payload']);

        $this->service()->saveKey('superagent', 'acme/not-installed', 'SA-EEEE-FFFF-GGGG-HH34');
        $this->assertNull($this->row('superagent')['payload']);
        $this->assertSame('', $this->row('superagent')['key_id']);
    }

    public function testEmptyingTheKeyTellsTheServiceForEveryDomainAndRemovesTheLicence(): void
    {
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'example.com']);
        $this->storeLicense(['domains' => json_encode(['example.org'])]);
        $this->responses[] = $this->json(['ok' => true]);
        $this->responses[] = new MockResponse('', ['error' => 'connection refused']);

        $state = $this->service('')->saveKey('superagent', 'acme/not-installed', '   ');

        $this->assertSame(LicenseState::NO_KEY, $state->reason);
        $this->assertNull($this->row('superagent'));
        $this->assertSame([self::URL.'/v1/licenses/deactivate', self::URL.'/v1/licenses/deactivate'], array_column($this->requests, 'url'));
        $installation = $this->db->fetchOne('SELECT installation_id FROM tl_neo_access');
        $this->assertSame([
            ['licenseKey' => self::KEY, 'domain' => 'example.com', 'installation' => $installation],
            ['licenseKey' => self::KEY, 'domain' => 'example.org', 'installation' => $installation],
        ], array_column($this->requests, 'body'), 'the domains of now and those reported before, each with the installation');
        $this->assertStringContainsString('licence service not told', implode("\n", $this->logger->records));
    }

    public function testEveryRequestOfThisInstallationCarriesTheSameIdentifier(): void
    {
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'www.example.com']);
        $this->responses[] = $this->json([], 503);
        $this->responses[] = $this->json([], 503);
        $this->responses[] = $this->json([], 503);

        $this->service()->saveKey('superagent', 'acme/not-installed', self::KEY);
        $this->service()->refresh('superagent');
        $this->storeLicense([], 'websitecheck');
        $this->service()->refresh('websitecheck');

        $ids = array_unique(array_column(array_column($this->requests, 'body'), 'installation'));
        $this->assertCount(1, $ids, 'one installation for all extensions and all checks');
        $this->assertSame(['activate', 'refresh', 'refresh'], array_map(static fn (string $url): string => basename($url), array_column($this->requests, 'url')));
    }

    public function testOnlyTheDomainsOfTheRootPagesWhenEveryRootPageHasOne(): void
    {
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'www.example.com']);
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'EXAMPLE.org.']);
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'www.example.com']);
        $this->storeLicense(['domains' => json_encode(['old.example.net'])]);
        $this->responses[] = $this->json([], 503);

        // The back end runs under another address; it is no domain of the website.
        $this->service('backend.example.net')->refresh('superagent');

        $this->assertSame(['www.example.com', 'example.org'], $this->requests[0]['body']['domains']);
    }

    public function testARootPageWithoutDomainStandsForTheHostSeenLastWhenThereIsNoRequest(): void
    {
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'www.example.com']);
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => '']);
        $this->storeLicense();
        $this->responses[] = $this->json([], 503);
        $this->responses[] = $this->json([], 503);

        $this->service('shop.example.org')->refresh('superagent');
        $this->service('')->refresh('superagent');

        $this->assertSame(['www.example.com', 'shop.example.org'], $this->requests[0]['body']['domains']);
        $this->assertSame(['www.example.com', 'shop.example.org'], $this->requests[1]['body']['domains'], 'the cron reports the host seen last');
    }

    public function testTheAnswerSaysPerDomainWhyAndHowManyInstallationsAreInUse(): void
    {
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'www.example.com']);
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'example.org']);
        $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'example.net']);
        $this->storeLicense();
        $this->responses[] = $this->json([
            'certificate' => TestKeys::certificate(),
            'domains' => [
                ['domain' => 'www.example.com', 'covered' => true, 'reason' => null],
                ['domain' => 'example.org', 'covered' => false, 'reason' => 'notLicensed'],
                ['domain' => 'example.net', 'covered' => false, 'reason' => 'domainsPerInstallationExceeded'],
            ],
            'installations' => ['allowed' => 3, 'used' => 2],
            'domainsPerInstallation' => 2,
            'nextCheckAfter' => '2030-01-01T00:00:00+00:00',
        ]);

        $state = $this->service()->refresh('superagent');

        $this->assertSame(['www.example.com' => true, 'example.org' => false, 'example.net' => false], $state->coverage);
        $this->assertSame(['www.example.com' => null, 'example.org' => LicenseState::NOT_LICENSED, 'example.net' => LicenseState::DOMAINS_PER_INSTALLATION_EXCEEDED], $state->reasons);
        $this->assertSame(['allowed' => 3, 'used' => 2], $state->installations);
        $this->assertSame(2, $state->domainsPerInstallation);
        $this->assertSame(['installations' => ['allowed' => 3, 'used' => 2], 'domainsPerInstallation' => 2], json_decode((string) $this->row('superagent')['allotment'], true));
    }

    public function testAnAnswerWithoutTheNewFieldsWorksAsBefore(): void
    {
        $this->storeLicense();
        $this->responses[] = $this->json([
            'certificate' => TestKeys::certificate(),
            'domains' => [['domain' => 'example.com', 'covered' => true], ['domain' => 'example.org', 'covered' => false]],
        ]);

        $state = $this->service()->refresh('superagent');

        $this->assertSame(['example.com' => true, 'example.org' => false], $state->coverage);
        $this->assertSame([], $state->reasons);
        $this->assertNull($state->installations);
        $this->assertNull($state->domainsPerInstallation);
        $this->assertNull($this->row('superagent')['allotment']);
    }

    public function testOneKeyInTwoInstallationsWithALicenceForOneTheSecondIsRefused(): void
    {
        // A licence service that counts installations: one key, one installation allowed.
        $seen = [];
        $store = function (array $body) use (&$seen): MockResponse {
            $installation = (string) $body['installation'];
            $seen[$installation] ??= \count($seen) < 1;
            $covered = $seen[$installation];

            return $this->json([
                'certificate' => TestKeys::certificate(['domains' => $covered ? $body['domains'] : []]),
                'domains' => array_map(static fn (string $d): array => ['domain' => $d, 'covered' => $covered, 'reason' => $covered ? null : 'installationsExhausted'], $body['domains']),
                'installations' => ['allowed' => 1, 'used' => 1],
                'domainsPerInstallation' => 1,
            ]);
        };

        $first = Database::connection();
        $first->insert('tl_page', ['type' => 'root', 'dns' => 'www.agency-client-a.example']);
        $second = Database::connection();
        $second->insert('tl_page', ['type' => 'root', 'dns' => 'www.agency-client-b.example']);

        $stateA = $this->serviceOn($first, $store)->saveKey('superagent', 'acme/not-installed', self::KEY);
        $stateB = $this->serviceOn($second, $store)->saveKey('superagent', 'acme/not-installed', self::KEY);

        $this->assertNotSame($first->fetchOne('SELECT installation_id FROM tl_neo_access'), $second->fetchOne('SELECT installation_id FROM tl_neo_access'));
        $this->assertSame(['www.agency-client-a.example' => null], $stateA->reasons);
        $this->assertSame(['www.agency-client-b.example' => LicenseState::INSTALLATIONS_EXHAUSTED], $stateB->reasons);
        $this->assertSame(['allowed' => 1, 'used' => 1], $stateB->installations);
    }

    public function testAnUnreachableServiceKeepsTheStoredCertificateAndRetriesInAnHour(): void
    {
        $this->storeLicense(['payload' => 'stored-payload', 'signature' => 'sig', 'key_id' => 'kid', 'checked_at' => 1000]);
        $this->responses[] = new MockResponse('', ['error' => 'timeout while connecting']);

        $state = $this->service()->refresh('superagent');
        $row = $this->row('superagent');

        $this->assertSame('stored-payload', $row['payload']);
        $this->assertStringContainsString('timeout while connecting', $row['last_error']);
        $this->assertEqualsWithDelta(time() + 3600, (int) $row['next_check_at'], 5);
        $this->assertSame(1000, $state->checkedAt);
        $this->assertSame(LicenseState::UNVERIFIABLE, $state->reason);
        $this->assertStringContainsString('warning: Basic: licence check of "superagent" failed', implode("\n", $this->logger->records));
    }

    public function testAnErrorAnswerOfTheServiceIsRecordedAndRetriedInAnHour(): void
    {
        $this->storeLicense(['payload' => 'stored-payload', 'signature' => 'sig', 'key_id' => 'kid']);
        $this->responses[] = $this->json(['errorCode' => 'E500', 'message' => 'Maintenance'], 503);

        $this->service()->refresh('superagent');
        $row = $this->row('superagent');

        $this->assertSame('HTTP 503 E500 Maintenance', $row['last_error']);
        $this->assertSame('stored-payload', $row['payload']);
        $this->assertEqualsWithDelta(time() + 3600, (int) $row['next_check_at'], 5);
    }

    public function testTheCronRefreshesOnlyDueLicencesWithAKey(): void
    {
        $this->storeLicense(['next_check_at' => time() - 10], 'due');
        $this->storeLicense(['next_check_at' => time() + 3600], 'later');
        $this->db->insert('tl_neo_license', ['module' => 'nokey', 'license_key' => '', 'next_check_at' => 0]);
        $this->responses[] = $this->json([], 503);

        $this->service()->refreshDue();

        $this->assertCount(1, $this->requests);
        $this->assertSame(self::URL.'/v1/licenses/refresh', $this->requests[0]['url']);
        $this->assertSame('due', $this->requests[0]['body']['module']);
    }

    public function testTheCronDoesNothingBeforeTheTableExists(): void
    {
        $this->db = Database::connection(Database::PAGE);

        $this->service()->refreshDue();

        $this->assertSame([], $this->requests);
        $this->assertSame(LicenseState::NO_KEY, $this->service()->state('superagent')->reason);
    }

    public function testAtMostTwentyDomainsAreReported(): void
    {
        for ($i = 1; $i <= 25; ++$i) {
            $this->db->insert('tl_page', ['type' => 'root', 'dns' => 'site'.$i.'.example.com']);
        }

        $this->storeLicense();
        $this->responses[] = $this->json([], 503);

        $this->service('www.example.com')->refresh('superagent');

        $this->assertCount(20, $this->requests[0]['body']['domains']);
        $this->assertSame('site1.example.com', $this->requests[0]['body']['domains'][0]);
    }

    public function testWithoutTlPageTheRequestHostAndTheStoredDomainsAreReported(): void
    {
        $this->db = Database::connection(Database::LICENSE);
        $this->storeLicense(['domains' => json_encode(['example.org', 'www.example.com'])]);
        $this->responses[] = $this->json([], 503);

        $this->service('www.example.com:443')->refresh('superagent');

        $this->assertSame(['www.example.com', 'example.org'], $this->requests[0]['body']['domains']);
    }

    public function testStatesWithoutUsableCertificate(): void
    {
        $this->storeLicense([], 'nocert');
        $this->storeLicense(['payload' => 'garbage', 'signature' => 'garbage', 'key_id' => '2026-11-c6bc62'], 'forged');
        $signed = TestKeys::certificate();
        $this->storeLicense(['payload' => $signed['payload'], 'signature' => $signed['signature'], 'key_id' => $signed['keyId']], 'foreign');

        $service = $this->service();

        $this->assertSame(LicenseState::NO_CERTIFICATE, $service->state('nocert')->reason);
        $this->assertSame(LicenseState::UNVERIFIABLE, $service->state('forged')->reason);
        $this->assertSame(LicenseState::UNVERIFIABLE, $service->state('foreign')->reason);
        $this->assertFalse($service->state('foreign')->hasEdition('plus'));
    }

    public function testPagesAreRefreshedOnlyWhenWhatTheyShowChanges(): void
    {
        $this->storeLicense(['payload' => 'p', 'signature' => 's', 'key_id' => 'k']);
        $answer = ['certificate' => ['payload' => 'p', 'signature' => 's', 'keyId' => 'k'], 'domains' => [['domain' => 'example.com', 'covered' => true]]];
        $this->responses[] = $this->json($answer);
        $this->responses[] = $this->json($answer);

        $service = $this->service();
        $service->refresh('superagent');
        $service->refresh('superagent');

        $this->assertSame([[LicenseService::CACHE_TAG]], $this->tagManager->invalidated);
    }

    #[DataProvider('keys')]
    public function testNormalizeKey(string $typed, string $normalized): void
    {
        $this->assertSame($normalized, LicenseService::normalizeKey($typed));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function keys(): iterable
    {
        yield 'already normalized' => [self::KEY, self::KEY];
        yield 'lower case with blanks' => [" sa-aaaa-bbbb-cccc-dd12\t\n", self::KEY];
        yield 'blanks inside' => ['SA-AAAA BBBB-CCCC-DD12', 'SA-AAAABBBB-CCCC-DD12'];
        yield 'encoded by Contao' => ['SA&#45;AAAA-BBBB-CCCC-DD12', self::KEY];
        yield 'empty' => ['', ''];
    }

    public function testKeyHintShowsPrefixAndLastFourCharactersOnly(): void
    {
        $this->storeLicense([], 'dashed');
        $this->storeLicense(['license_key' => $this->cipher->encrypt('ABCDEFGHIJ1234')], 'plain');

        $this->assertSame('SA-••••-••••-••••-DD12', $this->service()->keyHint('dashed'));
        $this->assertSame('••••1234', $this->service()->keyHint('plain'));
    }

    private function service(string $host = 'example.com'): LicenseService
    {
        $requests = new RequestStack();

        if ('' !== $host) {
            $requests->push(Request::create('https://'.$host.'/'));
        }

        $client = new MockHttpClient(function (string $method, string $url, array $options): MockResponse {
            $this->requests[] = ['method' => $method, 'url' => $url, 'body' => json_decode((string) ($options['body'] ?? ''), true) ?? []];

            return array_shift($this->responses) ?? throw new \LogicException('Unexpected request to '.$url);
        });

        return new LicenseService($this->db, $this->cipher, $client, $requests, $this->logger, self::URL, new PageCacheTags(new ServiceMap(['tag_manager' => $this->tagManager])));
    }

    /**
     * A licence service on another database (another installation) whose answers come from $store.
     */
    private function serviceOn(Connection $db, callable $store): LicenseService
    {
        $client = new MockHttpClient(function (string $method, string $url, array $options) use ($store): MockResponse {
            $body = json_decode((string) ($options['body'] ?? ''), true) ?? [];
            $this->requests[] = ['method' => $method, 'url' => $url, 'body' => $body];

            return $store($body);
        });

        $requests = new RequestStack();
        $requests->push(Request::create('https://backend.example.net/'));

        return new LicenseService($db, $this->cipher, $client, $requests, $this->logger, self::URL);
    }

    /**
     * @param array<string, mixed> $values
     */
    private function storeLicense(array $values = [], string $module = 'superagent'): void
    {
        $this->db->insert('tl_neo_license', $values + [
            'module' => $module,
            'package' => 'acme/not-installed',
            'license_key' => $this->cipher->encrypt(self::KEY),
            'next_check_at' => 0,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function row(string $module): array|null
    {
        return $this->db->fetchAssociative('SELECT * FROM tl_neo_license WHERE module = ?', [$module]) ?: null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function json(array $data, int $status = 200): MockResponse
    {
        return new MockResponse((string) json_encode($data), ['http_code' => $status, 'response_headers' => ['content-type' => 'application/json']]);
    }
}
