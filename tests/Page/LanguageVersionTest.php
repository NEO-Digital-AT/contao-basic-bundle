<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Page;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Neo\BasicBundle\Page\LanguageVersion;
use PHPUnit\Framework\TestCase;

/**
 * Two websites: German (root 1, fallback) and English (root 2). The privacy
 * policy exists in both (10 and 20, linked), the imprint only in German (11).
 */
final class LanguageVersionTest extends TestCase
{
    /** page id => [root, languageMain] */
    private const PAGES = [
        10 => [1, 0],
        11 => [1, 0],
        20 => [2, 10],
    ];

    public function testReturnsTheLinkedPageInTheRootOfTheVisitor(): void
    {
        self::assertSame(20, $this->version()->in(10, 2));
    }

    public function testReturnsThePageOfTheFallbackLanguageFromAnotherLanguage(): void
    {
        self::assertSame(10, $this->version()->in(20, 1));
    }

    public function testKeepsAPageThatAlreadyBelongsToTheRoot(): void
    {
        self::assertSame(10, $this->version()->in(10, 1));
    }

    public function testKeepsThePageWhenTheRootHasNoLinkedPage(): void
    {
        self::assertSame(11, $this->version()->in(11, 2));
    }

    public function testKeepsThePageWithoutTheColumnOfTheLanguageExtension(): void
    {
        self::assertSame(10, $this->version(false)->in(10, 2));
    }

    public function testKeepsAnEmptySettingOrUnknownRoot(): void
    {
        self::assertSame(0, $this->version()->in(0, 2));
        self::assertSame(10, $this->version()->in(10, 0));
    }

    private function version(bool $linked = true): LanguageVersion
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $db->executeStatement('CREATE TABLE tl_page (id INTEGER PRIMARY KEY, pid INTEGER NOT NULL DEFAULT 0'.($linked ? ', languageMain INTEGER NOT NULL DEFAULT 0' : '').')');

        foreach (self::PAGES as $id => [$root, $main]) {
            $db->insert('tl_page', ['id' => $id, 'pid' => $root] + ($linked ? ['languageMain' => $main] : []));
        }

        $framework = $this->createStub(ContaoFramework::class);
        $framework->method('getAdapter')->willReturnMap([[PageModel::class, $this->pages($db)]]);

        return new LanguageVersion($db, $framework);
    }

    /** Adapter of PageModel: findWithDetails() with the id, the root and languageMain of the row. */
    private function pages(Connection $db): Adapter
    {
        return new class($db) extends Adapter {
            public function __construct(private readonly Connection $db)
            {
                parent::__construct(PageModel::class);
            }

            public function __call(string $name, array $arguments): mixed
            {
                $row = $this->db->fetchAssociative('SELECT * FROM tl_page WHERE id = ?', [(int) $arguments[0]]);

                if ('findWithDetails' !== $name || false === $row) {
                    return null;
                }

                return (object) ['id' => (int) $row['id'], 'rootId' => (int) $row['pid'], 'languageMain' => (int) ($row['languageMain'] ?? 0)];
            }
        };
    }
}
