<?php

declare(strict_types=1);

namespace Neo\BasicBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Neo\BasicBundle\Access\AccessCallbacks;
use Neo\BasicBundle\Access\AccessPage;
use Neo\BasicBundle\Translation\AzureConnection;
use Neo\BasicBundle\Translation\DeepLConnection;

/**
 * Puts the palette of "Access keys and licences" together once the DCA files of
 * all bundles are loaded, in the order of what an administrator comes back
 * for: the licences (and the terms that depend on them) first, then the AI
 * connection, the translation services Azure Translator and DeepL (each when
 * an extension uses it) and the sections of the extensions. The legends carry
 * the name of the area only; the status line of a section names the
 * extensions that use it.
 */
#[AsHook('loadDataContainer')]
final class AccessPaletteListener
{
    public function __invoke(string $table): void
    {
        if (AccessPage::TABLE !== $table) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA'][AccessPage::TABLE];
        $config = $dca['config']['neo_access'] ?? [];
        $lang = &$GLOBALS['TL_LANG'][AccessPage::TABLE];
        $boxes = [];

        $licenses = $dca['config']['neo_license'] ?? [];

        if ([] !== $licenses) {
            $fields = ['neo_license_intro'];

            foreach (array_keys($licenses) as $module) {
                $fields[] = 'license_'.$module.'_key';
                $fields[] = 'license_'.$module.'_status';
            }

            $boxes[] = '{'.AccessPage::LICENSE_LEGEND.'},'.implode(',', $fields);
        }

        // Right after the licences: which passages apply depends on the edition.
        $terms = $dca['config']['neo_terms'] ?? [];

        if ([] !== $terms) {
            $boxes[] = '{'.AccessPage::TERMS_LEGEND.'},'.implode(',', array_map(static fn (string $module): string => 'terms_'.$module, array_keys($terms)));
        }

        if (!empty($config['ai'])) {
            $boxes[] = '{'.AccessPage::AI_LEGEND.'},ai_status,ai_route,ai_api_key';
            $lang[AccessPage::AI_LEGEND] = $lang['ai_legend_name'] ?? 'AI connection';
        }

        if (!empty($config['azure_translator'])) {
            $boxes[] = '{'.AccessPage::AZURE_TRANSLATOR_LEGEND.'},azure_translator_status,'.AzureConnection::LOCATION.','.AzureConnection::KEY.','.AzureConnection::REGION;
            $lang[AccessPage::AZURE_TRANSLATOR_LEGEND] = $lang['azure_translator_legend_name'] ?? 'Azure Translator';
        }

        if (!empty($config['deepl'])) {
            $boxes[] = '{'.AccessPage::DEEPL_LEGEND.'},deepl_status,'.DeepLConnection::KEY;
            $lang[AccessPage::DEEPL_LEGEND] = $lang['deepl_legend_name'] ?? 'DeepL';
        }

        foreach ($config['sections'] ?? [] as $legend => $section) {
            $status = substr($legend, 0, -\strlen('_legend')).'_status';
            $dca['fields'][$status] = ['input_field_callback' => [AccessCallbacks::class, 'sectionStatus']];
            $boxes[] = '{'.$legend.'},'.$status.','.implode(',', $section['fields']);
            $lang[$legend] = $lang[$legend.'_name'] ?? $legend;
        }

        $dca['palettes']['default'] = implode(';', $boxes);
    }
}
