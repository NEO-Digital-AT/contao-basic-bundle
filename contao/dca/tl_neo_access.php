<?php

declare(strict_types=1);

use Contao\DC_Table;
use Neo\BasicBundle\Access\AccessCallbacks;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Translation\AzureConnection;
use Neo\BasicBundle\Translation\DeepLConnection;

/*
 * tl_neo_access — "Access keys and licences": the one record with everything that
 * connects the website to outside services, everything several extensions
 * share, and the licence keys. This file defines the record, the AI
 * connection and the accesses of Azure Translator and DeepL; the extensions add their sections in their own
 * contao/dca/tl_neo_access.php (Neo\BasicBundle\Access\AccessPage). The palette
 * is put together by AccessPaletteListener.
 */
$GLOBALS['TL_DCA']['tl_neo_access'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => false,
        'notCreatable' => true,
        'notCopyable' => true,
        'notDeletable' => true,
        'closed' => true,
        // One record, the list opens its form: the breadcrumb shows the module name only (BreadcrumbListener).
        'neoSingleRecord' => true,
        'onload_callback' => [
            [AccessCallbacks::class, 'openRecord'],
            // Sections with nothing left to do start collapsed.
            [AccessCallbacks::class, 'collapseComplete'],
        ],
        'sql' => ['keys' => ['id' => 'primary']],
    ],
    'edit' => [
        'buttons_callback' => [[AccessCallbacks::class, 'editButtons']],
    ],
    'list' => [
        'sorting' => ['mode' => 0],
        'label' => ['fields' => ['id'], 'format' => '%s'],
    ],
    'palettes' => [
        '__selector__' => ['ai_route', AzureConnection::LOCATION],
        'default' => '',
    ],
    'subpalettes' => [
        'ai_route_'.AiConnection::CUSTOM => 'ai_base_url',
        AzureConnection::LOCATION.'_'.AzureConnection::CUSTOM => AzureConnection::URL,
    ],
    'fields' => [
        'id' => ['sql' => 'int(10) unsigned NOT NULL auto_increment'],
        'tstamp' => ['sql' => 'int(10) unsigned NOT NULL default 0'],
        // Takeover migrations of the extensions that already ran (AiConnection::markAdopted).
        'adopted' => ['sql' => "varchar(255) NOT NULL default ''"],
        // Identifier of this installation for the licence service and the host that stands for root pages
        // without a domain name (Neo\BasicBundle\License\Installation). Not shown, not editable.
        'installation_id' => ['sql' => "varchar(64) NOT NULL default ''"],
        'installation_host' => ['sql' => "varchar(255) NOT NULL default ''"],

        'ai_status' => [
            'input_field_callback' => [AccessCallbacks::class, 'aiStatus'],
        ],
        'ai_route' => [
            'inputType' => 'select',
            'options_callback' => [AccessCallbacks::class, 'routeOptions'],
            'reference' => &$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'],
            'default' => AiConnection::DEFAULT_ROUTE,
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "varchar(32) NOT NULL default '".AiConnection::DEFAULT_ROUTE."'",
        ],
        'ai_api_key' => [
            'inputType' => 'text',
            'eval' => ['hideInput' => true, 'decodeEntities' => true, 'maxlength' => 512, 'doNotCopy' => true, 'tl_class' => 'w50'],
            'load_callback' => [[AccessCallbacks::class, 'maskSecret']],
            'save_callback' => [[AccessCallbacks::class, 'saveSecret']],
            'sql' => "varchar(1024) NOT NULL default ''",
        ],
        'ai_base_url' => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'placeholder' => 'https://example.com/v1', 'tl_class' => 'w50 clr'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        // Azure Translator (Neo\BasicBundle\Translation\AzureConnection), shown when an extension calls AccessPage::useAzureTranslator().
        'azure_translator_status' => [
            'input_field_callback' => [AccessCallbacks::class, 'azureTranslatorStatus'],
        ],
        AzureConnection::LOCATION => [
            'inputType' => 'select',
            'options_callback' => [AccessCallbacks::class, 'azureLocationOptions'],
            'reference' => &$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'],
            'default' => AzureConnection::DEFAULT_LOCATION,
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "varchar(16) NOT NULL default '".AzureConnection::DEFAULT_LOCATION."'",
        ],
        AzureConnection::URL => [
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'placeholder' => 'https://example.cognitiveservices.azure.com/translator/text/v3.0', 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        AzureConnection::KEY => [
            'inputType' => 'text',
            'eval' => ['hideInput' => true, 'decodeEntities' => true, 'maxlength' => 512, 'doNotCopy' => true, 'tl_class' => 'w50 clr'],
            'load_callback' => [[AccessCallbacks::class, 'maskSecret']],
            'save_callback' => [[AccessCallbacks::class, 'saveSecret']],
            'sql' => "varchar(1024) NOT NULL default ''",
        ],
        AzureConnection::REGION => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'placeholder' => 'westeurope', 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],

        // DeepL (Neo\BasicBundle\Translation\DeepLConnection), shown when an extension calls AccessPage::useDeepL().
        'deepl_status' => [
            'input_field_callback' => [AccessCallbacks::class, 'deeplStatus'],
        ],
        DeepLConnection::KEY => [
            'inputType' => 'text',
            'eval' => ['hideInput' => true, 'decodeEntities' => true, 'maxlength' => 512, 'doNotCopy' => true, 'tl_class' => 'w50 clr'],
            'load_callback' => [[AccessCallbacks::class, 'maskSecret']],
            'save_callback' => [[AccessCallbacks::class, 'saveSecret']],
            'sql' => "varchar(1024) NOT NULL default ''",
        ],
    ],
];
