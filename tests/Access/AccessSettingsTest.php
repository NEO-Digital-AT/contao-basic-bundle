<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Access;

use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\BasicBundle\Tests\Support\Database;
use PHPUnit\Framework\TestCase;

final class AccessSettingsTest extends TestCase
{
    public function testSecretsAreStoredEncryptedAndReadBackExactlyAsTyped(): void
    {
        $db = Database::connection();
        $cipher = new SecretCipher('app-secret-for-tests');
        $settings = new AccessSettings($db, $cipher);

        $settings->store(['ai_route' => 'openai', 'ai_api_key' => 'sk-a&b=c'], ['ai_api_key']);

        $stored = (string) $db->fetchOne('SELECT ai_api_key FROM tl_neo_access');
        $this->assertStringStartsWith('enc:', $stored);
        $this->assertStringNotContainsString('sk-a&b=c', $stored);
        $this->assertSame('sk-a&b=c', $settings->secret('ai_api_key'));
        $this->assertSame('openai', $settings->value('ai_route'));
        $this->assertTrue($settings->hasValue('ai_api_key'));
    }

    public function testSecretsEncodedByContaoAreDecoded(): void
    {
        $db = Database::connection();
        $cipher = new SecretCipher('app-secret-for-tests');
        $db->insert('tl_neo_access', ['deepl_key' => $cipher->encrypt(' key&amp;with&lt;chars:fx ')]);

        $this->assertSame('key&with<chars:fx', (new AccessSettings($db, $cipher))->secret('deepl_key'));
    }

    public function testASecretThatCannotBeDecryptedIsEmpty(): void
    {
        $db = Database::connection();
        $db->insert('tl_neo_access', ['deepl_key' => (new SecretCipher('another-secret'))->encrypt('key')]);

        $settings = new AccessSettings($db, new SecretCipher('app-secret-for-tests'));

        $this->assertSame('', $settings->secret('deepl_key'));
        $this->assertTrue($settings->hasValue('deepl_key'), 'a stored value, even if unreadable');
    }

    public function testThereIsOnlyOneRecord(): void
    {
        $db = Database::connection();
        $settings = new AccessSettings($db, new SecretCipher('app-secret-for-tests'));

        $id = $settings->ensureRow();

        $this->assertSame($id, $settings->ensureRow());
        $settings->store(['ai_route' => 'openai']);
        $settings->store(['ai_base_url' => '']);
        $this->assertSame(1, (int) $db->fetchOne('SELECT COUNT(*) FROM tl_neo_access'));
        $this->assertSame('openai', $settings->value('ai_route'));
    }

    public function testValuesAreTrimmedAndMissingColumnsAreEmpty(): void
    {
        $db = Database::connection();
        $db->insert('tl_neo_access', ['azure_translator_region' => '  westeurope ']);
        $settings = new AccessSettings($db, new SecretCipher('app-secret-for-tests'));

        $this->assertSame('westeurope', $settings->value('azure_translator_region'));
        $this->assertSame('', $settings->value('no_such_column'));
        $this->assertFalse($settings->hasValue('ai_api_key'));
    }

    public function testBeforeTheDatabaseUpdateEverythingIsEmpty(): void
    {
        $settings = new AccessSettings(Database::connection(Database::PAGE), new SecretCipher('app-secret-for-tests'));

        $this->assertSame('', $settings->value('ai_route'));
        $this->assertSame('', $settings->secret('ai_api_key'));
        $this->assertFalse($settings->hasColumns('ai_route'));
        $this->assertFalse($settings->isAdopted('superagent'));
    }

    public function testHasColumnsChecksEveryColumnRegardlessOfCase(): void
    {
        $settings = new AccessSettings(Database::connection(), new SecretCipher('app-secret-for-tests'));

        $this->assertTrue($settings->hasColumns('adopted', 'AI_ROUTE', 'ai_api_key'));
        $this->assertFalse($settings->hasColumns('adopted', 'not_yet_created'));
    }

    public function testEachTakeoverIsRecordedOnce(): void
    {
        $db = Database::connection();
        $settings = new AccessSettings($db, new SecretCipher('app-secret-for-tests'));

        $this->assertFalse($settings->isAdopted('superagent'));

        $settings->markAdopted('superagent');
        $settings->markAdopted('websitecheck');
        $settings->markAdopted('superagent');

        $this->assertTrue($settings->isAdopted('superagent'));
        $this->assertTrue($settings->isAdopted('websitecheck'));
        $this->assertFalse($settings->isAdopted('super'));
        $this->assertSame('superagent,websitecheck', $db->fetchOne('SELECT adopted FROM tl_neo_access'));
    }
}
