<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Alle extensies';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'AI-verbinding';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Route', 'Via welke aanbieder de extensies het taalmodel bereiken.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, EU-route (verwerking in de EU)',
    'requesty_global' => 'Requesty, standaardroute (wereldwijd)',
    'openai' => 'OpenAI (direct)',
    'custom' => 'Eigen adres (OpenAI-compatibel)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Adres', 'Adres van uw eigen interface, bijvoorbeeld https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Sleutel', 'Wordt versleuteld opgeslagen en niet meer getoond. Leeg laten behoudt de opgeslagen sleutel.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Sleutel opgeslagen (versleuteld). Adres: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Nog geen sleutel opgeslagen.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'De route „Eigen adres” heeft een adres nodig.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Verbinding testen', 'Verbinding testen'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Verbinding testen', 'Verbinding testen'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Elke extensie kiest het model in haar eigen instellingen.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Opgevraagd adres';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Verbinding werkt: HTTP %d in %d ms, %d modellen beschikbaar.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'De route antwoordt met HTTP %d (na %d ms). Antwoord:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Geen verbinding: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Opgeslagen.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Nog niet opgeslagen.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Niet opgeslagen. Alleen nodig voor sommige functies.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Nu invullen onder „Toegang en licenties”';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Sleutel', 'Sleutel 1 of 2 van de resource (Azure-portal → Sleutels en eindpunt). Wordt versleuteld opgeslagen; leeg laten behoudt hem.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Verwerking', 'Waar Azure de teksten vertaalt.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Europa (Frankrijk of West-Europa)',
    'global' => 'Wereldwijd (dichtstbijzijnde datacenter)',
    'nam' => 'Amerika',
    'apc' => 'Azië-Pacific',
    'custom' => 'Eigen adres van de resource',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Adres van de resource', 'Het adres uit de Azure-portal, bijvoorbeeld https://name.cognitiveservices.azure.com/translator/text/v3.0. Nodig bij beperkte netwerktoegang of voor verwerking in Zwitserland.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Regio', 'De locatie van de resource, bijvoorbeeld westeurope. Leeg laten bij een globale resource.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Verbinding testen', 'Verbinding testen'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Opgevraagd adres';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Verbinding in orde: HTTP %d in %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure antwoordt met HTTP %d (na %d ms). Antwoord:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Geen verbinding: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Sleutel opgeslagen (versleuteld). Adres: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Nog geen sleutel opgeslagen.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'De verwerking „Eigen adres van de resource” heeft een adres nodig.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Nog geen sleutel opgeslagen. Alleen nodig voor vertalingen.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Verbinding testen', 'Verbinding testen'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Sleutel', 'De authenticatiesleutel van uw DeepL API-account (Account → API-sleutels). Wordt versleuteld opgeslagen; leeg laten behoudt hem.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Sleutel opgeslagen (versleuteld). Interface: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Nog geen sleutel opgeslagen. Alleen nodig voor vertalingen.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Nog geen sleutel opgeslagen.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free of Pro volgt uit de sleutel: sleutels van het gratis abonnement eindigen op „:fx”.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Verbinding testen', 'Verbinding testen'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Verbinding testen', 'Verbinding testen'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Opgevraagd adres';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Verbinding in orde: HTTP %1$d in %2$d ms. Verbruikt in deze factuurperiode: %3$s van %4$s tekens.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Verbinding in orde: HTTP %d in %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL antwoordt met HTTP %d (na %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'De sleutel wordt niet geaccepteerd.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'De tekens van deze factuurperiode zijn op.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Geen verbinding: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Gebruikt door %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Bezig met testen …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Kan worden getest zodra de toegang is opgeslagen.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'Dat is niet gelukt. Laad de pagina opnieuw en probeer het nog eens.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'De pagina is niet meer actueel. Laad haar opnieuw en probeer het nog eens.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Voorwaarden van de extensies';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · editie %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · zonder licentie';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'versie %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'nieuw';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'gewijzigd';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'tekst tonen';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Duitse versie tonen';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Nog niet bevestigd.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'De voorwaarden zijn gewijzigd. Bevestig de gemarkeerde passages opnieuw.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Bevestigd door %1$s op %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Vertaling ter verduidelijking. Alleen de Duitse versie van de licentievoorwaarden is bindend.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Deze passages maken deel uit van de licentievoorwaarden van de extensie (bestand LICENSE). Nieuwe passages verschijnen hier wanneer u een hogere editie in licentie neemt of de voorwaarden wijzigen.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'Ik heb de voorwaarden gelezen en ga ermee akkoord.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Bevestigen';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Vink eerst het vakje aan om de voorwaarden te bevestigen.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Voorwaarden van %s bevestigd.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Nu bevestigen';
