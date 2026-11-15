<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Support;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

/**
 * An SQLite database in memory with the tables of this bundle, as Contao's
 * database update creates them (columns and defaults of the DCA files), plus
 * the columns of tl_page that the licence check reads and the column of
 * tl_layout that the dock reads.
 */
final class Database
{
    public const ACCESS = <<<'SQL'
        CREATE TABLE tl_neo_access (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tstamp INTEGER NOT NULL DEFAULT 0,
            adopted VARCHAR(255) NOT NULL DEFAULT '',
            installation_id VARCHAR(64) NOT NULL DEFAULT '',
            installation_host VARCHAR(255) NOT NULL DEFAULT '',
            ai_route VARCHAR(32) NOT NULL DEFAULT 'requesty_eu',
            ai_base_url VARCHAR(1024) NOT NULL DEFAULT '',
            ai_api_key VARCHAR(1024) NOT NULL DEFAULT '',
            azure_translator_key VARCHAR(1024) NOT NULL DEFAULT '',
            azure_translator_location VARCHAR(16) NOT NULL DEFAULT 'eur',
            azure_translator_url VARCHAR(1024) NOT NULL DEFAULT '',
            azure_translator_region VARCHAR(64) NOT NULL DEFAULT '',
            deepl_key VARCHAR(1024) NOT NULL DEFAULT '',
            x_api_key VARCHAR(1024) NOT NULL DEFAULT '',
            x_secret VARCHAR(1024) NOT NULL DEFAULT ''
        )
        SQL;

    public const LICENSE = <<<'SQL'
        CREATE TABLE tl_neo_license (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tstamp INTEGER NOT NULL DEFAULT 0,
            module VARCHAR(32) NOT NULL DEFAULT '' UNIQUE,
            package VARCHAR(128) NOT NULL DEFAULT '',
            license_key VARCHAR(255) NOT NULL DEFAULT '',
            payload TEXT NULL,
            signature VARCHAR(255) NOT NULL DEFAULT '',
            key_id VARCHAR(64) NOT NULL DEFAULT '',
            domains TEXT NULL,
            coverage TEXT NULL,
            allotment TEXT NULL,
            checked_at INTEGER NOT NULL DEFAULT 0,
            next_check_at INTEGER NOT NULL DEFAULT 0,
            last_error VARCHAR(255) NOT NULL DEFAULT ''
        )
        SQL;

    public const TERMS = <<<'SQL'
        CREATE TABLE tl_neo_terms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tstamp INTEGER NOT NULL DEFAULT 0,
            module VARCHAR(32) NOT NULL DEFAULT '',
            passage VARCHAR(32) NOT NULL DEFAULT '',
            version INTEGER NOT NULL DEFAULT 0,
            user_id INTEGER NOT NULL DEFAULT 0,
            username VARCHAR(64) NOT NULL DEFAULT '',
            language VARCHAR(8) NOT NULL DEFAULT ''
        )
        SQL;

    public const PAGE = <<<'SQL'
        CREATE TABLE tl_page (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type VARCHAR(64) NOT NULL DEFAULT '',
            dns VARCHAR(255) NOT NULL DEFAULT ''
        )
        SQL;

    public const LAYOUT = <<<'SQL'
        CREATE TABLE tl_layout (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL DEFAULT '',
            neo_dock VARCHAR(16) NOT NULL DEFAULT 'menu'
        )
        SQL;

    /**
     * A new, empty database with the given tables (default: all of them).
     */
    public static function connection(string ...$tables): Connection
    {
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);

        foreach ([] === $tables ? [self::ACCESS, self::LICENSE, self::TERMS, self::PAGE, self::LAYOUT] : $tables as $sql) {
            $connection->executeStatement($sql);
        }

        return $connection;
    }
}
