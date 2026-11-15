<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\License;

use Neo\BasicBundle\License\Installation;
use Neo\BasicBundle\Tests\Support\Database;
use PHPUnit\Framework\TestCase;

final class InstallationTest extends TestCase
{
    public function testTheIdentifierIsCreatedOnceAndKept(): void
    {
        $db = Database::connection();

        $id = (new Installation($db))->id();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $id);
        $this->assertSame($id, (new Installation($db))->id(), 'a new service (next request, cleared cache) reads the same identifier');
        $this->assertSame($id, $db->fetchOne('SELECT installation_id FROM tl_neo_access'));
        $this->assertSame(1, (int) $db->fetchOne('SELECT COUNT(*) FROM tl_neo_access'), 'the record of the page is created once');
    }

    public function testTheIdentifierLivesInTheExistingRecordOfThePage(): void
    {
        $db = Database::connection();
        $db->insert('tl_neo_access', ['ai_route' => 'openai']);

        $id = (new Installation($db))->id();

        $this->assertSame(1, (int) $db->fetchOne('SELECT COUNT(*) FROM tl_neo_access'));
        $this->assertSame(['openai', $id], array_values($db->fetchAssociative('SELECT ai_route, installation_id FROM tl_neo_access')));
    }

    public function testAStoredIdentifierIsNeverReplaced(): void
    {
        $db = Database::connection();
        $db->insert('tl_neo_access', ['installation_id' => 'staging-copy-of-the-live-site']);

        $this->assertSame('staging-copy-of-the-live-site', (new Installation($db))->id(), 'a copy of the database keeps the identifier');
    }

    public function testABrokenValueIsReplacedByANewIdentifier(): void
    {
        $db = Database::connection();
        $db->insert('tl_neo_access', ['installation_id' => 'bad id!']);

        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', (new Installation($db))->id());
    }

    public function testBeforeTheDatabaseUpdateThereIsNoIdentifier(): void
    {
        $db = Database::connection(Database::LICENSE);
        $db->executeStatement('CREATE TABLE tl_neo_access (id INTEGER PRIMARY KEY AUTOINCREMENT, tstamp INTEGER NOT NULL DEFAULT 0)');

        $installation = new Installation($db);

        $this->assertSame('', $installation->id());
        $this->assertSame('', $installation->host());
        $installation->rememberHost('www.example.com');
    }

    public function testTheHostForRootPagesWithoutDomainIsRemembered(): void
    {
        $db = Database::connection();
        $installation = new Installation($db);

        $this->assertSame('', $installation->host());

        $installation->rememberHost('www.example.com');

        $this->assertSame('www.example.com', (new Installation($db))->host());
    }

    public function testValidIdentifiers(): void
    {
        $this->assertTrue(Installation::isValid(str_repeat('a', 8)));
        $this->assertTrue(Installation::isValid(str_repeat('Z', 64)));
        $this->assertTrue(Installation::isValid('abc_DEF-123'));
        $this->assertFalse(Installation::isValid(str_repeat('a', 7)));
        $this->assertFalse(Installation::isValid(str_repeat('a', 65)));
        $this->assertFalse(Installation::isValid('with space'));
        $this->assertFalse(Installation::isValid(''));
    }
}
