<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\License;

use Neo\BasicBundle\License\Certificate;
use Neo\BasicBundle\License\LicenseState;
use Neo\BasicBundle\License\PublicKeys;
use Neo\BasicBundle\Tests\Support\TestKeys;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CertificateTest extends TestCase
{
    public function testAGenuineCertificateIsRead(): void
    {
        $signed = TestKeys::certificate(['module' => 'SuperAgent', 'edition' => 'PLUS', 'domains' => [' Example.COM ', 'example.org'], 'status' => 'Active']);

        $certificate = $this->verify($signed);

        $this->assertNotNull($certificate);
        $this->assertSame('lic_0001', $certificate->licenseId);
        $this->assertSame('superagent', $certificate->module);
        $this->assertSame('plus', $certificate->edition);
        $this->assertSame(['example.com', 'example.org'], $certificate->domains);
        $this->assertSame('active', $certificate->status);
        $this->assertSame(strtotime('2099-12-31T23:59:59+00:00'), $certificate->paidUntil);
        $this->assertSame(strtotime('2100-01-31T23:59:59+00:00'), $certificate->graceUntil);
        $this->assertSame(strtotime('2026-11-01T10:00:00+00:00'), $certificate->issuedAt);
    }

    public function testAnUnknownKeyIdIsRejected(): void
    {
        $signed = TestKeys::certificate();

        $this->assertNull(Certificate::verify($signed['payload'], $signed['signature'], 'other-key', TestKeys::publicKeys()));
    }

    public function testACertificateOfTheTestKeyIsNotAcceptedWithTheKeysOfTheLicenceService(): void
    {
        $signed = TestKeys::certificate();

        $this->assertNull(Certificate::verify($signed['payload'], $signed['signature'], $signed['keyId'], PublicKeys::KEYS + [TestKeys::KEY_ID => (string) current(PublicKeys::KEYS)]));
    }

    public function testAChangedPayloadIsRejected(): void
    {
        $signed = TestKeys::certificate();
        $forged = TestKeys::base64Url((string) json_encode(['edition' => 'pro'] + TestKeys::fields(), JSON_UNESCAPED_SLASHES));

        $this->assertNull(Certificate::verify($forged, $signed['signature'], $signed['keyId'], TestKeys::publicKeys()));
    }

    public function testTheUnsignedCopyIsNeverRead(): void
    {
        $signed = TestKeys::certificate(['edition' => 'basic']);

        $certificate = $this->verify($signed);

        $this->assertSame('basic', $certificate?->edition);
    }

    #[DataProvider('incompleteCertificates')]
    public function testAnIncompleteCertificateIsRejectedEvenWithAGenuineSignature(array $changes): void
    {
        $this->assertNull($this->verify(TestKeys::certificate($changes)));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function incompleteCertificates(): iterable
    {
        yield 'other format version' => [['version' => 2]];
        yield 'version as text' => [['version' => '1']];
        yield 'key id of another key in the payload' => [['keyId' => 'other-key']];
        yield 'no paid-until date' => [['paidUntil' => null]];
        yield 'invalid grace date' => [['graceUntil' => 'next year sometime']];
        yield 'empty issue date' => [['issuedAt' => '']];
        yield 'domains not a list' => [['domains' => 'example.com']];
    }

    public function testAPayloadThatIsNotJsonIsRejected(): void
    {
        $this->assertNull($this->verify(TestKeys::sign('not json')));
    }

    #[DataProvider('brokenEncodings')]
    public function testBrokenEncodingsAreRejected(string $payload, string $signature): void
    {
        $this->assertNull(Certificate::verify($payload, $signature, TestKeys::KEY_ID, TestKeys::publicKeys()));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function brokenEncodings(): iterable
    {
        $signed = TestKeys::certificate();

        yield 'empty signature' => [$signed['payload'], ''];
        yield 'empty payload' => ['', $signed['signature']];
        yield 'signature not base64' => [$signed['payload'], '***'];
        yield 'signature of another payload' => [$signed['payload'], TestKeys::sign('other')['signature']];
    }

    #[DataProvider('hosts')]
    public function testCoversTheDomainAndItsSubdomainsOnly(string $host, bool $covered): void
    {
        $certificate = $this->verify(TestKeys::certificate(['domains' => ['example.com', '']]));

        $this->assertSame($covered, $certificate?->covers($host));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function hosts(): iterable
    {
        yield 'the domain' => ['example.com', true];
        yield 'subdomain' => ['www.example.com', true];
        yield 'deeper subdomain' => ['shop.eu.example.com', true];
        yield 'upper case, port and trailing dot' => ['WWW.Example.com.:8443', true];
        yield 'same ending without a dot' => ['otherexample.com', false];
        yield 'domain as subdomain of another' => ['example.com.example.net', false];
        yield 'other domain' => ['example.org', false];
        yield 'empty host' => ['', false];
    }

    public function testIsValidUntilTheEndOfTheGracePeriod(): void
    {
        $certificate = $this->verify(TestKeys::certificate(['graceUntil' => '2030-01-31T00:00:00+00:00']));
        $end = strtotime('2030-01-31T00:00:00+00:00');

        $this->assertTrue($certificate?->isValidAt($end));
        $this->assertFalse($certificate?->isValidAt($end + 1));
    }

    #[DataProvider('withdrawn')]
    public function testIsNotValidWhenExpiredOrRevoked(string $status): void
    {
        $certificate = $this->verify(TestKeys::certificate(['status' => $status]));

        $this->assertFalse($certificate?->isValidAt(time()));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function withdrawn(): iterable
    {
        yield 'expired' => ['expired'];
        yield 'revoked' => ['revoked'];
        yield 'revoked in capitals' => ['REVOKED'];
    }

    #[DataProvider('reasons')]
    public function testReasonChecksExtensionWithdrawalDateAndHostInThisOrder(array $changes, string $module, string $host, string $reason): void
    {
        $certificate = $this->verify(TestKeys::certificate($changes));

        $this->assertSame($reason, $certificate?->reason($module, $host, strtotime('2026-11-12T12:00:00+00:00')));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, string, string, string}>
     */
    public static function reasons(): iterable
    {
        yield 'valid on a covered host' => [[], 'superagent', 'www.example.com', LicenseState::OK];
        yield 'valid in the back end without host' => [[], 'superagent', '', LicenseState::OK];
        yield 'host not covered' => [[], 'superagent', 'example.net', LicenseState::DOMAIN];
        yield 'certificate of another extension' => [['status' => 'revoked'], 'websitecheck', 'example.com', LicenseState::WRONG_MODULE];
        yield 'revoked, before the date' => [['status' => 'revoked', 'graceUntil' => '2020-01-01T00:00:00+00:00'], 'superagent', 'example.net', LicenseState::REVOKED];
        yield 'grace period over, before the host' => [['graceUntil' => '2026-11-12T11:59:59+00:00'], 'superagent', 'example.net', LicenseState::EXPIRED];
        yield 'expired by the service' => [['status' => 'expired'], 'superagent', 'example.com', LicenseState::EXPIRED];
        yield 'paid period over, grace period running' => [['paidUntil' => '2026-11-01T00:00:00+00:00', 'status' => 'grace'], 'superagent', 'example.com', LicenseState::OK];
    }

    #[DataProvider('normalizedHosts')]
    public function testNormalizeHost(string $host, string $normalized): void
    {
        $this->assertSame($normalized, Certificate::normalizeHost($host));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function normalizedHosts(): iterable
    {
        yield 'plain' => ['example.com', 'example.com'];
        yield 'case and spaces' => [' WWW.Example.COM ', 'www.example.com'];
        yield 'port' => ['example.com:8080', 'example.com'];
        yield 'trailing dot' => ['example.com.', 'example.com'];
        yield 'empty' => ['', ''];
    }

    /**
     * The key of the licence service is a valid P-256 public key, and the
     * browser script of the credit line lists the same keys.
     */
    public function testTheKeysOfTheLicenceServiceAreP256AndTheSameAsInTheCreditScript(): void
    {
        $script = (string) file_get_contents(\dirname(__DIR__, 2).'/public/credit.js');
        $this->assertSame(1, preg_match('/var KEYS = \{(.*?)\};/s', $script, $block));
        preg_match_all("/'([^']+)':\\s*'([A-Za-z0-9+\\/=]+)'/", $block[1], $entries, PREG_SET_ORDER);

        $scriptKeys = [];

        foreach ($entries as [, $id, $spki]) {
            $scriptKeys[$id] = $spki;
        }

        $this->assertNotEmpty(PublicKeys::KEYS);
        $this->assertSame(array_keys(PublicKeys::KEYS), array_keys($scriptKeys));

        foreach (PublicKeys::KEYS as $id => $pem) {
            $key = openssl_pkey_get_public($pem);
            $this->assertNotFalse($key, $id);

            $details = openssl_pkey_get_details($key);
            $this->assertSame(OPENSSL_KEYTYPE_EC, $details['type']);
            $this->assertSame('prime256v1', $details['ec']['curve_name']);

            $der = base64_decode(preg_replace('/-----[^-]+-----|\s+/', '', $pem), true);
            $this->assertSame(base64_encode((string) $der), $scriptKeys[$id], $id);
        }
    }

    /**
     * @param array{payload: string, signature: string, keyId: string} $signed
     */
    private function verify(array $signed): Certificate|null
    {
        return Certificate::verify($signed['payload'], $signed['signature'], $signed['keyId'], TestKeys::publicKeys());
    }
}
