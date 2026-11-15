<?php

declare(strict_types=1);

namespace Neo\BasicBundle\Access;

use Contao\Controller;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use Neo\BasicBundle\License\LicenseFields;
use Neo\BasicBundle\Terms\TermsFields;

/**
 * "Access keys and licences": one back-end page for everything that connects the
 * website to outside services, everything several extensions share, and the
 * licence keys. Each extension adds its own section in its DCA file for
 * tl_neo_access; a section only appears when an installed extension adds it.
 *
 * In an extension's contao/dca/tl_neo_access.php:
 *
 *     AccessPage::useAi('SuperAgent');
 *     AccessPage::useAzureTranslator('SuperAgent');
 *     AccessPage::useDeepL('SuperAgent');
 *     $GLOBALS['TL_DCA']['tl_neo_access']['fields']['x_api_key'] = AccessPage::secretField();
 *     AccessPage::addSection('x_legend', ['x_api_key'], 'SuperAgent');
 *     AccessPage::addLicense('superagent', 'vendor/package', 'SuperAgent');
 *     AccessPage::addTerms('superagent', 'SuperAgent', 'translation_domain', ['all' => 1, 'basic' => 1]);
 *
 * The palette is put together when the DCA is complete (AccessPaletteListener):
 * the licences first, then the terms, the AI connection, Azure Translator,
 * DeepL and the sections in the order of the extensions. Sections with nothing
 * left to do are collapsed (AccessCallbacks::collapseComplete()).
 */
final class AccessPage
{
    public const TABLE = 'tl_neo_access';

    /**
     * Back-end module, last entry of the menu group "neo", left out when no section exists.
     */
    public const MODULE = 'neo_access';

    public const AI_LEGEND = 'ai_legend';

    public const AZURE_TRANSLATOR_LEGEND = 'azure_translator_legend';

    public const DEEPL_LEGEND = 'deepl_legend';

    public const LICENSE_LEGEND = 'neo_license_legend';

    public const TERMS_LEGEND = 'neo_terms_legend';

    /**
     * Registers that an extension uses the shared AI connection.
     */
    public static function useAi(string $extension): void
    {
        self::register('ai', $extension);
    }

    /**
     * Registers that an extension uses the shared Azure Translator access
     * (Translation\AzureConnection, Translation\AzureTranslator).
     */
    public static function useAzureTranslator(string $extension): void
    {
        self::register('azure_translator', $extension);
    }

    /**
     * Registers that an extension uses the shared DeepL access
     * (Translation\DeepLConnection, Translation\DeepLTranslator).
     */
    public static function useDeepL(string $extension): void
    {
        self::register('deepl', $extension);
    }

    /**
     * Adds a section of an extension. An optional section is only needed for
     * some functions: while it is empty, its status line is a note, not an error
     * (text: TL_LANG.tl_neo_access.<name>_optional, else section_optional).
     *
     * @param list<string> $fields
     */
    public static function addSection(string $legend, array $fields, string $extension, bool $optional = false): void
    {
        $config = &$GLOBALS['TL_DCA'][self::TABLE]['config']['neo_access'];
        $config['sections'][$legend]['fields'] = array_values(array_unique(array_merge($config['sections'][$legend]['fields'] ?? [], $fields)));
        $config['sections'][$legend]['users'][] = $extension;

        // Required as soon as one extension needs it.
        $config['sections'][$legend]['optional'] = ($config['sections'][$legend]['optional'] ?? true) && $optional;
    }

    /**
     * Adds the licence row of a paid extension to the section "Licences".
     */
    public static function addLicense(string $module, string $package, string $extension): void
    {
        LicenseFields::addTo(self::TABLE, $module, $package, $extension);
    }

    /**
     * Adds the terms of an extension to the section "Terms of the extensions":
     * passages in order (passage => version), the first for every edition, each
     * further one for the edition of the same name and above. Texts: keys
     * "<passage>.title" and "<passage>.text" of the translation domain, German
     * binding (see TermsService, TermsFields).
     *
     * @param array<string, int> $passages
     */
    public static function addTerms(string $module, string $extension, string $domain, array $passages): void
    {
        TermsFields::addTo(self::TABLE, $module, $extension, $domain, $passages);
    }

    /**
     * Field definition for a secret: shown as a mask, never again in clear;
     * an empty field or the unchanged mask keeps the stored value; stored
     * encrypted, exactly as typed.
     *
     * @return array<string, mixed>
     */
    public static function secretField(int $maxlength = 512): array
    {
        return [
            'inputType' => 'text',
            'eval' => ['hideInput' => true, 'decodeEntities' => true, 'maxlength' => $maxlength, 'doNotCopy' => true, 'tl_class' => 'w50'],
            'load_callback' => [[AccessCallbacks::class, 'maskSecret']],
            'save_callback' => [[AccessCallbacks::class, 'saveSecret']],
            'sql' => "varchar(1024) NOT NULL default ''",
        ];
    }

    /**
     * True when at least one installed extension adds a section or a licence.
     */
    public static function hasSections(): bool
    {
        Controller::loadDataContainer(self::TABLE);

        $config = $GLOBALS['TL_DCA'][self::TABLE]['config']['neo_access'] ?? [];

        return !empty($config['ai']) || !empty($config['azure_translator']) || !empty($config['deepl']) || !empty($config['sections']) || !empty($GLOBALS['TL_DCA'][self::TABLE]['config']['neo_license']) || !empty($GLOBALS['TL_DCA'][self::TABLE]['config']['neo_terms']);
    }

    /**
     * Whether the current back-end user may open the page (links to it only then).
     */
    public static function canOpen(): bool
    {
        return System::getContainer()->get('security.helper')->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, self::MODULE);
    }

    /**
     * Back-end address of the page, optionally at a section.
     */
    public static function url(string $legend = ''): string
    {
        $url = System::getContainer()->get('router')->generate('contao_backend', ['do' => self::MODULE]);

        return '' !== $legend ? $url.'#pal_'.$legend : $url;
    }

    /**
     * Message at the top of an extension's back-end page: what is missing and,
     * for users who may open the page, a link to the section where it is entered.
     */
    public static function addMissingMessage(string $text, string $legend): void
    {
        System::loadLanguageFile(self::TABLE);

        $link = '';

        if (self::canOpen()) {
            $link = \sprintf(
                ' <a href="%s">%s</a>',
                StringUtil::specialchars(self::url($legend)),
                StringUtil::specialchars($GLOBALS['TL_LANG'][self::TABLE]['enter_here'] ?? ''),
            );
        }

        Message::addRaw(\sprintf('<p class="tl_error">%s%s</p>', StringUtil::specialchars($text), $link));
    }

    /**
     * Message at the top of an extension's back-end page about terms still to be
     * confirmed, with a link to the section for users who may open the page.
     */
    public static function addTermsMessage(string $text): void
    {
        System::loadLanguageFile(self::TABLE);

        $link = '';

        if (self::canOpen()) {
            $link = \sprintf(
                ' <a href="%s">%s</a>',
                StringUtil::specialchars(self::url(self::TERMS_LEGEND)),
                StringUtil::specialchars($GLOBALS['TL_LANG'][self::TABLE]['terms_confirm_now'] ?? ''),
            );
        }

        Message::addRaw(\sprintf('<p class="tl_error">%s%s</p>', StringUtil::specialchars($text), $link));
    }

    /**
     * The extensions that use a part of the page: "ai", "azure_translator",
     * "deepl" or the legend of a section.
     *
     * @return list<string>
     */
    public static function users(string $part): array
    {
        $config = $GLOBALS['TL_DCA'][self::TABLE]['config']['neo_access'] ?? [];
        $users = $config[$part] ?? $config['sections'][$part]['users'] ?? [];

        return array_values(array_unique(array_map('strval', (array) $users)));
    }

    private static function register(string $kind, string $extension): void
    {
        $config = &$GLOBALS['TL_DCA'][self::TABLE]['config']['neo_access'];
        $config[$kind][] = $extension;
        $config[$kind] = array_values(array_unique($config[$kind]));
    }
}
