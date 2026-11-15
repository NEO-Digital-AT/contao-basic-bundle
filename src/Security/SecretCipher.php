<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Security;

/**
 * Symmetric encryption for secrets stored at rest (API keys, licence keys).
 * libsodium's authenticated secretbox with a 32-byte key derived from the
 * application secret (kernel.secret / APP_SECRET) and a scope.
 *
 * The scope keeps the derivation each extension used before it moved to this
 * bundle ("neo-superagent", "neo-websitecheck", …), so values stored back then
 * can still be decrypted. New values of this bundle use SCOPE.
 *
 * Values carry the prefix "enc:"; a value without it (plain text, e.g. from an
 * environment variable) is returned unchanged by decrypt(). Rotating APP_SECRET
 * makes stored values unreadable; the keys then have to be entered again.
 */
final class SecretCipher
{
    public const SCOPE = 'neo-basic';

    private const PREFIX = 'enc:';

    public function __construct(private readonly string $secret)
    {
    }

    public function encrypt(string $plain, string $scope = self::SCOPE): string
    {
        if ('' === $plain) {
            return '';
        }

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = sodium_crypto_secretbox($plain, $nonce, $this->deriveKey($scope));

        return self::PREFIX.base64_encode($nonce.$cipher);
    }

    /**
     * The plain value, '' when the value is empty or cannot be decrypted.
     */
    public function decrypt(string $stored, string $scope = self::SCOPE): string
    {
        if ('' === $stored) {
            return '';
        }

        if (!$this->isEncrypted($stored)) {
            return $stored;
        }

        $raw = base64_decode(substr($stored, \strlen(self::PREFIX)), true);
        $min = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES;

        if (false === $raw || \strlen($raw) < $min) {
            return '';
        }

        $nonce = substr($raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = substr($raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $plain = sodium_crypto_secretbox_open($cipher, $nonce, $this->deriveKey($scope));

        return false === $plain ? '' : $plain;
    }

    public function isEncrypted(string $value): bool
    {
        return str_starts_with($value, self::PREFIX);
    }

    private function deriveKey(string $scope): string
    {
        return hash('sha256', $scope.':'.$this->secret, true);
    }
}
