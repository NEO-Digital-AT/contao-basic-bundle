<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Tests\Access;

use Neo\BasicBundle\Access\AccessCallbacks;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\EventListener\AccessPaletteListener;
use Neo\BasicBundle\Tests\Support\LanguageFiles;
use PHPUnit\Framework\TestCase;

/**
 * What the extensions register on "Access keys and licences" and the palette
 * put together from it.
 */
final class AccessPageTest extends TestCase
{
    protected function setUp(): void
    {
        LanguageFiles::load('tl_neo_access', 'neo_license');
        unset($GLOBALS['TL_DCA']);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA']);
        LanguageFiles::reset();
    }

    public function testEveryExtensionIsNamedOncePerPart(): void
    {
        AccessPage::useAi('SuperAgent');
        AccessPage::useAi('Website-Check');
        AccessPage::useAi('SuperAgent');
        AccessPage::useDeepL('SuperAgent');

        $this->assertSame(['SuperAgent', 'Website-Check'], AccessPage::users('ai'));
        $this->assertSame(['SuperAgent'], AccessPage::users('deepl'));
        $this->assertSame([], AccessPage::users('azure_translator'));
    }

    public function testASectionIsRequiredAsSoonAsOneExtensionNeedsIt(): void
    {
        AccessPage::addSection('x_legend', ['x_api_key'], 'Extension A', true);
        $this->assertTrue($GLOBALS['TL_DCA']['tl_neo_access']['config']['neo_access']['sections']['x_legend']['optional']);

        AccessPage::addSection('x_legend', ['x_api_key', 'x_secret'], 'Extension B');
        AccessPage::addSection('x_legend', ['x_api_key'], 'Extension C', true);

        $section = $GLOBALS['TL_DCA']['tl_neo_access']['config']['neo_access']['sections']['x_legend'];
        $this->assertFalse($section['optional']);
        $this->assertSame(['x_api_key', 'x_secret'], $section['fields']);
        $this->assertSame(['Extension A', 'Extension B', 'Extension C'], AccessPage::users('x_legend'));
    }

    public function testTheSecretFieldNeverShowsOrCopiesTheSecret(): void
    {
        $field = AccessPage::secretField(128);

        $this->assertTrue($field['eval']['hideInput']);
        $this->assertTrue($field['eval']['doNotCopy']);
        $this->assertSame(128, $field['eval']['maxlength']);
        $this->assertSame([[AccessCallbacks::class, 'maskSecret']], $field['load_callback']);
        $this->assertSame([[AccessCallbacks::class, 'saveSecret']], $field['save_callback']);
    }

    public function testThePaletteStartsWithTheLicencesAndHasOnlyTheUsedParts(): void
    {
        AccessPage::addSection('x_legend', ['x_api_key', 'x_secret'], 'Extension A');
        AccessPage::useDeepL('Extension A');
        AccessPage::addLicense('superagent', 'acme/superagent-bundle', 'SuperAgent');
        AccessPage::addTerms('superagent', 'SuperAgent', 'neo_superagent_terms', ['all' => 1, 'basic' => 1]);
        AccessPage::useAi('SuperAgent');

        (new AccessPaletteListener())('tl_neo_access');

        $this->assertSame(
            '{neo_license_legend},neo_license_intro,license_superagent_key,license_superagent_status;'
            .'{neo_terms_legend},terms_superagent;'
            .'{ai_legend},ai_status,ai_route,ai_api_key;'
            .'{deepl_legend},deepl_status,deepl_key;'
            .'{x_legend},x_status,x_api_key,x_secret',
            $GLOBALS['TL_DCA']['tl_neo_access']['palettes']['default'],
        );
        $this->assertSame([AccessCallbacks::class, 'sectionStatus'], $GLOBALS['TL_DCA']['tl_neo_access']['fields']['x_status']['input_field_callback']);
        $this->assertSame('AI connection', $GLOBALS['TL_LANG']['tl_neo_access']['ai_legend']);
        $this->assertSame(['acme/superagent-bundle', 'SuperAgent'], array_values($GLOBALS['TL_DCA']['tl_neo_access']['config']['neo_license']['superagent']));
    }

    public function testTheListenerLeavesOtherTablesAlone(): void
    {
        AccessPage::useAi('SuperAgent');

        (new AccessPaletteListener())('tl_content');

        $this->assertArrayNotHasKey('palettes', $GLOBALS['TL_DCA']['tl_neo_access']);
    }

    public function testTermsAreConfirmedOnSubmitOnceForAllExtensions(): void
    {
        AccessPage::addTerms('superagent', 'SuperAgent', 'domain_a', ['all' => 1]);
        AccessPage::addTerms('websitecheck', 'Website-Check', 'domain_b', ['all' => 2]);

        $this->assertCount(1, $GLOBALS['TL_DCA']['tl_neo_access']['config']['onsubmit_callback']);
        $this->assertSame(['all' => 2], $GLOBALS['TL_DCA']['tl_neo_access']['config']['neo_terms']['websitecheck']['passages']);
    }
}
