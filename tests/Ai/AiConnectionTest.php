<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Ai;

use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\BasicBundle\Tests\Support\Database;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AiConnectionTest extends TestCase
{
    private Connection $db;

    private SecretCipher $cipher;

    protected function setUp(): void
    {
        $this->db = Database::connection();
        $this->cipher = new SecretCipher('app-secret-for-tests');
    }

    public function testWithoutRecordTheDefaultRouteIsUsedAndNothingIsConfigured(): void
    {
        $connection = $this->connection();

        $this->assertSame(AiConnection::DEFAULT_ROUTE, $connection->route());
        $this->assertSame('https://router.eu.requesty.ai/v1', $connection->baseUrl());
        $this->assertSame('', $connection->apiKey());
        $this->assertFalse($connection->hasKey());
        $this->assertFalse($connection->isConfigured());
    }

    public function testAKnownRouteUsesItsAddress(): void
    {
        $this->db->insert('tl_neo_access', ['ai_route' => 'openai', 'ai_base_url' => 'https://ignored.example.com', 'ai_api_key' => $this->cipher->encrypt('sk-test')]);
        $connection = $this->connection();

        $this->assertSame('openai', $connection->route());
        $this->assertSame('https://api.openai.com/v1', $connection->baseUrl());
        $this->assertSame('sk-test', $connection->apiKey());
        $this->assertTrue($connection->isConfigured());
    }

    public function testAnUnknownRouteFallsBackToTheDefault(): void
    {
        $this->db->insert('tl_neo_access', ['ai_route' => 'removed_route']);

        $this->assertSame(AiConnection::DEFAULT_ROUTE, $this->connection()->route());
    }

    public function testTheCustomRouteUsesTheAddressEnteredWithoutTrailingSlash(): void
    {
        $this->db->insert('tl_neo_access', ['ai_route' => 'custom', 'ai_base_url' => ' https://llm.example.com/v1/?a=1&amp;b=2/ ', 'ai_api_key' => $this->cipher->encrypt('sk-test')]);

        $this->assertSame('https://llm.example.com/v1/?a=1&b=2', $this->connection()->baseUrl());
    }

    public function testTheCustomRouteWithoutAddressIsNotConfigured(): void
    {
        $this->db->insert('tl_neo_access', ['ai_route' => 'custom', 'ai_api_key' => $this->cipher->encrypt('sk-test')]);
        $connection = $this->connection();

        $this->assertSame('', $connection->baseUrl());
        $this->assertTrue($connection->hasKey());
        $this->assertFalse($connection->isConfigured());
    }

    #[DataProvider('formerAddresses')]
    public function testRouteForAnAddressAnExtensionUsedBefore(string $baseUrl, array $expected): void
    {
        $this->assertSame($expected, AiConnection::routeFor($baseUrl));
    }

    /**
     * @return iterable<string, array{string, array{route: string, base_url: string}}>
     */
    public static function formerAddresses(): iterable
    {
        yield 'Requesty EU' => ['https://router.eu.requesty.ai/v1', ['route' => 'requesty_eu', 'base_url' => '']];
        yield 'Requesty global with slash and capitals' => [' HTTPS://Router.Requesty.ai/v1/ ', ['route' => 'requesty_global', 'base_url' => '']];
        yield 'OpenAI' => ['https://api.openai.com/v1', ['route' => 'openai', 'base_url' => '']];
        yield 'own address' => ['https://llm.example.com/v1/', ['route' => 'custom', 'base_url' => 'https://llm.example.com/v1']];
        yield 'none' => ['', ['route' => 'requesty_eu', 'base_url' => '']];
    }

    public function testStoreEncryptsTheKeyAndKeepsTheAddressOnlyForTheCustomRoute(): void
    {
        $connection = $this->connection();

        $connection->store('openai', 'https://llm.example.com', 'sk-one');
        $this->assertSame('', $this->db->fetchOne('SELECT ai_base_url FROM tl_neo_access'));
        $this->assertStringStartsWith('enc:', (string) $this->db->fetchOne('SELECT ai_api_key FROM tl_neo_access'));
        $this->assertSame('sk-one', $connection->apiKey());

        $connection->store('custom', 'https://llm.example.com', 'sk-two');
        $this->assertSame('https://llm.example.com', $connection->baseUrl());

        $connection->store('not_a_route', 'https://llm.example.com', 'sk-three');
        $this->assertSame(AiConnection::DEFAULT_ROUTE, $connection->route());
        $this->assertSame('', $this->db->fetchOne('SELECT ai_base_url FROM tl_neo_access'));
    }

    public function testTakeoverBookkeeping(): void
    {
        $connection = $this->connection();

        $this->assertTrue($connection->isStorable());
        $this->assertFalse($connection->isAdopted('websitecheck'));

        $connection->markAdopted('websitecheck');

        $this->assertTrue($connection->isAdopted('websitecheck'));
        $this->assertFalse((new AiConnection(new AccessSettings(Database::connection(Database::PAGE), $this->cipher)))->isStorable());
    }

    private function connection(): AiConnection
    {
        return new AiConnection(new AccessSettings($this->db, $this->cipher));
    }
}
