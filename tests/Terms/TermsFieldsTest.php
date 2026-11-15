<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Terms;

use Contao\BackendUser;
use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\License\LicenseService;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\BasicBundle\Terms\TermsFields;
use Neo\BasicBundle\Terms\TermsService;
use Neo\BasicBundle\Tests\Support\BackendFixtures;
use Neo\BasicBundle\Tests\Support\ContaoContainer;
use Neo\BasicBundle\Tests\Support\DataContainers;
use Neo\BasicBundle\Tests\Support\Database;
use Neo\BasicBundle\Tests\Support\LanguageFiles;
use Neo\BasicBundle\Tests\Support\MemoryLogger;
use Neo\BasicBundle\Tests\Support\RecordingClient;
use Neo\BasicBundle\Tests\Support\Settings;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Confirming the terms on "Access keys and licences".
 */
final class TermsFieldsTest extends TestCase
{
    use BackendFixtures;
    use ContaoContainer;
    use DataContainers;

    private Connection $db;

    protected function setUp(): void
    {
        LanguageFiles::load('tl_neo_access', 'neo_license');
        unset($GLOBALS['TL_DCA']);
        AccessPage::addTerms('superagent', 'SuperAgent', 'neo_superagent_terms', ['all' => 2, 'basic' => 1]);
        $this->db = Database::connection();
    }

    protected function tearDown(): void
    {
        $this->dropContainer();
        unset($GLOBALS['TL_DCA']);
        LanguageFiles::reset();
    }

    public function testConfirmRecordsThePassagesOfTheCurrentEdition(): void
    {
        $stack = $this->post(['neo_terms_accept' => 'superagent', 'neo_terms_ok_superagent' => '1']);

        $this->fields($stack)->confirm($this->dataContainer('terms_superagent'));

        $rows = $this->db->fetchAllAssociative('SELECT module, passage, version, user_id, username, language FROM tl_neo_terms');
        $this->assertEquals([['module' => 'superagent', 'passage' => 'all', 'version' => 2, 'user_id' => 7, 'username' => 'max.mustermann', 'language' => 'en']], $rows, 'without licence only the first passage');
        $this->assertSame(['Terms of SuperAgent confirmed.'], array_map(strip_tags(...), $this->messages($stack, 'confirm')));
    }

    public function testPassagesAlreadyConfirmedAreNotRecordedAgain(): void
    {
        (new TermsService($this->db))->accept('superagent', ['all' => 2], 3, 'erika.musterfrau', 'de');
        $stack = $this->post(['neo_terms_accept' => 'superagent', 'neo_terms_ok_superagent' => '1']);

        $this->fields($stack)->confirm($this->dataContainer('terms_superagent'));

        $this->assertSame(1, (int) $this->db->fetchOne('SELECT COUNT(*) FROM tl_neo_terms'));
    }

    public function testWithoutTheCheckboxNothingIsConfirmed(): void
    {
        $stack = $this->post(['neo_terms_accept' => 'superagent']);

        $this->fields($stack)->confirm($this->dataContainer('terms_superagent'));

        $this->assertSame(0, (int) $this->db->fetchOne('SELECT COUNT(*) FROM tl_neo_terms'));
        $this->assertSame([$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing']], $this->messages($stack, 'error'));
    }

    public function testOnlyExtensionsThatAddedTermsCanBeConfirmed(): void
    {
        $stack = $this->post(['neo_terms_accept' => 'other', 'neo_terms_ok_other' => '1']);

        $this->fields($stack)->confirm($this->dataContainer('terms_other'));

        $this->assertSame(0, (int) $this->db->fetchOne('SELECT COUNT(*) FROM tl_neo_terms'));
    }

    public function testOnlyABackEndUserCanConfirm(): void
    {
        $stack = $this->post(['neo_terms_accept' => 'superagent', 'neo_terms_ok_superagent' => '1']);

        $this->fields($stack, false)->confirm($this->dataContainer('terms_superagent'));

        $this->assertSame(0, (int) $this->db->fetchOne('SELECT COUNT(*) FROM tl_neo_terms'));
    }

    public function testWithoutTheButtonNothingHappens(): void
    {
        $stack = $this->post([]);

        $this->fields($stack)->confirm($this->dataContainer('terms_superagent'));

        $this->assertSame(0, (int) $this->db->fetchOne('SELECT COUNT(*) FROM tl_neo_terms'));
        $this->assertSame([], $this->messages($stack, 'error'));
    }

    public function testTheSectionShowsThePassagesOfTheEditionWithTheGermanTextNextToATranslation(): void
    {
        // Version 1 of "all" was confirmed; version 2 asks again.
        (new TermsService($this->db))->accept('superagent', ['all' => 1], 3, 'erika.musterfrau', 'de');
        $stack = $this->post([]);

        $html = $this->fields($stack)->render($this->dataContainer('terms_superagent'));

        $this->assertStringContainsString('<h3>SuperAgent · without licence</h3>', $html);
        $this->assertStringContainsString('<p class="tl_error">The terms have changed. Please confirm the marked passages again.</p>', $html);
        $this->assertStringContainsString('<strong>en:all.title</strong> (version 2) <strong>changed</strong>', $html);
        $this->assertStringNotContainsString('basic.title', $html, 'only the passage of the edition');
        $this->assertStringContainsString('<p><strong>Use:</strong> en text of all</p><p>Second paragraph &lt;en&gt;</p>', $html);
        $this->assertStringContainsString('<div lang="de" style="margin:6px 0 0 18px"><p><strong>Use:</strong> de text of all</p>', $html);
        $this->assertStringContainsString('The German version of the licence terms is binding.', $html);
        $this->assertStringContainsString('name="neo_terms_ok_superagent"', $html);
        $this->assertStringContainsString('<button type="submit" name="neo_terms_accept" value="superagent"', $html);
    }

    public function testNewTermsInGermanNeedNoTranslationNote(): void
    {
        $stack = $this->post([]);
        $stack->getCurrentRequest()?->setLocale('de_AT');

        $html = $this->fields($stack)->render($this->dataContainer('terms_superagent'));

        $this->assertStringContainsString('<strong>new</strong>', $html);
        $this->assertStringContainsString('Not confirmed yet.', $html);
        $this->assertStringNotContainsString('lang="de"', $html);
        $this->assertStringNotContainsString('is binding', $html);
    }

    /**
     * @param array<string, string> $post
     */
    private function post(array $post): RequestStack
    {
        $stack = $this->backendRequest('POST', [], $post);
        $stack->getCurrentRequest()?->setLocale('en_GB');
        $this->useContainer($stack);

        return $stack;
    }

    private function fields(RequestStack $stack, bool $backendUser = true): TermsFields
    {
        $user = null;

        if ($backendUser) {
            $user = $this->createStub(BackendUser::class);
            $user->method('__get')->willReturnCallback(static fn (string $key): mixed => ['id' => 7, 'username' => 'max.mustermann'][$key] ?? null);
        }

        $security = $this->createStub(Security::class);
        $security->method('getUser')->willReturn($user);

        $cipher = new SecretCipher(Settings::SECRET);
        $licenses = new LicenseService($this->db, $cipher, (new RecordingClient())->client, new RequestStack(), new MemoryLogger(), 'https://license.example.com');

        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(static function (string $id, array $parameters = [], string|null $domain = null, string|null $locale = null): string {
            [$passage, $part] = explode('.', $id);

            return 'text' === $part ? "Use: {$locale} text of {$passage}\n\nSecond paragraph <{$locale}>" : $locale.':'.$id;
        });

        return new TermsFields(new TermsService($this->db), $licenses, $translator, $stack, $security);
    }
}
