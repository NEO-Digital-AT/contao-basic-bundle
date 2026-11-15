<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Terms;

use Neo\BasicBundle\Terms\TermsService;
use Neo\BasicBundle\Tests\Support\Database;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TermsServiceTest extends TestCase
{
    private const PASSAGES = ['all' => 1, 'basic' => 2, 'plus' => 1, 'pro' => 1];

    #[DataProvider('editions')]
    public function testRequiredPassagesGrowWithTheEdition(string $edition, array $required): void
    {
        $this->assertSame($required, TermsService::required(self::PASSAGES, $edition));
    }

    /**
     * @return iterable<string, array{string, array<string, int>}>
     */
    public static function editions(): iterable
    {
        yield 'without licence' => ['', ['all' => 1]];
        yield 'unknown edition' => ['enterprise', ['all' => 1]];
        yield 'basic' => ['basic', ['all' => 1, 'basic' => 2]];
        yield 'plus' => ['plus', ['all' => 1, 'basic' => 2, 'plus' => 1]];
        yield 'pro' => ['pro', self::PASSAGES];
    }

    public function testNothingIsAcceptedBeforeTheTableExists(): void
    {
        $terms = new TermsService(Database::connection(Database::ACCESS));

        $this->assertNull($terms->acceptance('superagent', 'all'));
        $this->assertNull($terms->acceptedUpTo('superagent', self::PASSAGES, ''));
        $this->assertFalse($terms->allAccepted('superagent', self::PASSAGES, ''));
    }

    public function testAnAcceptanceIsRecordedWithUserVersionAndLanguage(): void
    {
        $db = Database::connection();
        $terms = new TermsService($db);

        $terms->accept('superagent', ['all' => 1], 7, 'max.mustermann', 'en');

        $acceptance = $terms->acceptance('superagent', 'all');
        $this->assertSame(1, $acceptance['version']);
        $this->assertSame('max.mustermann', $acceptance['username']);
        $this->assertSame('en', $acceptance['language']);
        $this->assertEqualsWithDelta(time(), $acceptance['time'], 5);
        $this->assertSame(7, (int) $db->fetchOne('SELECT user_id FROM tl_neo_terms'));
        $this->assertTrue($terms->allAccepted('superagent', self::PASSAGES, ''));
        $this->assertFalse($terms->allAccepted('websitecheck', self::PASSAGES, ''), 'per extension');
    }

    public function testAHigherVersionAsksForANewConfirmationAndEveryConfirmationIsKept(): void
    {
        $db = Database::connection();
        $terms = new TermsService($db);
        $terms->accept('superagent', ['all' => 1, 'basic' => 1], 7, 'max.mustermann', 'de');

        $this->assertTrue($terms->isAccepted('superagent', 'basic', 1));
        $this->assertFalse($terms->isAccepted('superagent', 'basic', 2));
        $this->assertSame('all', $terms->acceptedUpTo('superagent', self::PASSAGES, 'basic'));
        $this->assertFalse($terms->allAccepted('superagent', self::PASSAGES, 'basic'));

        $terms->accept('superagent', ['basic' => 2], 8, 'erika.musterfrau', 'en');

        $this->assertTrue($terms->allAccepted('superagent', self::PASSAGES, 'basic'));
        $this->assertSame('erika.musterfrau', $terms->acceptance('superagent', 'basic')['username']);
        $this->assertSame(3, (int) $db->fetchOne('SELECT COUNT(*) FROM tl_neo_terms'));
    }

    public function testAcceptedUpToStopsAtTheFirstOpenPassage(): void
    {
        $terms = new TermsService(Database::connection());
        $terms->accept('superagent', ['all' => 1, 'plus' => 1], 7, 'max.mustermann', 'de');

        $this->assertSame('all', $terms->acceptedUpTo('superagent', self::PASSAGES, 'pro'));
        $this->assertFalse($terms->allAccepted('superagent', self::PASSAGES, 'plus'));
    }

    public function testUserNameAndLanguageAreCutToTheirColumns(): void
    {
        $terms = new TermsService(Database::connection());
        $terms->accept('superagent', ['all' => 1], 7, str_repeat('ä', 80), 'de-AT-x-long');

        $acceptance = $terms->acceptance('superagent', 'all');
        $this->assertSame(str_repeat('ä', 64), $acceptance['username']);
        $this->assertSame('de-AT-x-', $acceptance['language']);
    }
}
