<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Support;

/**
 * A key pair of the same kind as the licence service's (ECDSA P-256), made
 * for the tests, to sign certificates as the service does: JSON payload,
 * DER signature, both base64url. Never the key of the licence service.
 */
final class TestKeys
{
    public const KEY_ID = 'test-key';

    private static \OpenSSLAsymmetricKey|null $private = null;

    private static string|null $public = null;

    /**
     * @return array<string, string> key id => PEM, as PublicKeys::KEYS
     */
    public static function publicKeys(): array
    {
        self::create();

        return [self::KEY_ID => (string) self::$public];
    }

    /**
     * A signed certificate in the form the licence service sends it.
     *
     * @param array<string, mixed> $changes fields that differ from a valid certificate of "superagent"
     *
     * @return array{payload: string, signature: string, keyId: string}
     */
    public static function certificate(array $changes = []): array
    {
        $data = array_merge(self::fields(), $changes);

        return self::sign((string) json_encode($data, JSON_UNESCAPED_SLASHES));
    }

    /**
     * A valid certificate of the extension "superagent", edition "plus", for example.com.
     *
     * @return array<string, mixed>
     */
    public static function fields(): array
    {
        return [
            'version' => 1,
            'keyId' => self::KEY_ID,
            'licenseId' => 'lic_0001',
            'module' => 'superagent',
            'edition' => 'plus',
            'domains' => ['example.com'],
            'paidUntil' => '2099-12-31T23:59:59+00:00',
            'graceUntil' => '2100-01-31T23:59:59+00:00',
            'status' => 'active',
            'issuedAt' => '2026-11-01T10:00:00+00:00',
        ];
    }

    /**
     * @return array{payload: string, signature: string, keyId: string}
     */
    public static function sign(string $payload, string $keyId = self::KEY_ID): array
    {
        self::create();

        openssl_sign($payload, $signature, self::$private, OPENSSL_ALGO_SHA256);

        return ['payload' => self::base64Url($payload), 'signature' => self::base64Url($signature), 'keyId' => $keyId];
    }

    public static function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function create(): void
    {
        if (null !== self::$private) {
            return;
        }

        $key = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']);

        if (false === $key) {
            throw new \RuntimeException('OpenSSL cannot create an EC key: '.openssl_error_string());
        }

        self::$private = $key;
        self::$public = (string) openssl_pkey_get_details($key)['key'];
    }
}
