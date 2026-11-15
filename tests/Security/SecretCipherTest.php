<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Security;

use Neo\BasicBundle\Security\SecretCipher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SecretCipherTest extends TestCase
{
    private const SECRET = 'app-secret-for-tests';

    public function testEncryptedValueCarriesThePrefixAndDecryptsToThePlainValue(): void
    {
        $cipher = new SecretCipher(self::SECRET);

        $stored = $cipher->encrypt('sk-test-1234567890');

        $this->assertStringStartsWith('enc:', $stored);
        $this->assertStringNotContainsString('sk-test-1234567890', $stored);
        $this->assertTrue($cipher->isEncrypted($stored));
        $this->assertSame('sk-test-1234567890', $cipher->decrypt($stored));
    }

    public function testEveryEncryptionUsesANewNonce(): void
    {
        $cipher = new SecretCipher(self::SECRET);

        $this->assertNotSame($cipher->encrypt('same value'), $cipher->encrypt('same value'));
    }

    public function testEmptyValuesStayEmpty(): void
    {
        $cipher = new SecretCipher(self::SECRET);

        $this->assertSame('', $cipher->encrypt(''));
        $this->assertSame('', $cipher->decrypt(''));
    }

    public function testAValueWithoutPrefixIsReturnedUnchanged(): void
    {
        $cipher = new SecretCipher(self::SECRET);

        $this->assertFalse($cipher->isEncrypted('plain-key'));
        $this->assertSame('plain-key', $cipher->decrypt('plain-key'));
    }

    public function testAnotherApplicationSecretCannotDecrypt(): void
    {
        $stored = (new SecretCipher(self::SECRET))->encrypt('sk-test');

        $this->assertSame('', (new SecretCipher('another-secret'))->decrypt($stored));
    }

    public function testTheScopeIsPartOfTheKey(): void
    {
        $cipher = new SecretCipher(self::SECRET);
        $stored = $cipher->encrypt('sk-test', 'neo-superagent');

        $this->assertSame('', $cipher->decrypt($stored));
        $this->assertSame('sk-test', $cipher->decrypt($stored, 'neo-superagent'));
    }

    /**
     * Values stored by the extensions before they moved to this bundle: secretbox
     * with the key sha256("<scope>:<secret>"), nonce in front, base64, prefix "enc:".
     */
    public function testValuesOfTheFormerDerivationStayReadable(): void
    {
        $nonce = str_repeat("\x01", SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $key = hash('sha256', 'neo-websitecheck:'.self::SECRET, true);
        $stored = 'enc:'.base64_encode($nonce.sodium_crypto_secretbox('pagespeed-key', $nonce, $key));

        $this->assertSame('pagespeed-key', (new SecretCipher(self::SECRET))->decrypt($stored, 'neo-websitecheck'));
    }

    #[DataProvider('brokenValues')]
    public function testBrokenValuesDecryptToAnEmptyString(string $stored): void
    {
        $this->assertSame('', (new SecretCipher(self::SECRET))->decrypt($stored));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function brokenValues(): iterable
    {
        yield 'not base64' => ['enc:***'];
        yield 'too short for nonce and tag' => ['enc:'.base64_encode('short')];
        yield 'random bytes' => ['enc:'.base64_encode(random_bytes(64))];
        yield 'prefix only' => ['enc:'];
    }

    public function testAChangedCipherTextIsRejected(): void
    {
        $cipher = new SecretCipher(self::SECRET);
        $raw = base64_decode(substr($cipher->encrypt('sk-test'), 4), true);
        $raw[\strlen($raw) - 1] = \chr(\ord($raw[\strlen($raw) - 1]) ^ 1);

        $this->assertSame('', $cipher->decrypt('enc:'.base64_encode($raw)));
    }
}
