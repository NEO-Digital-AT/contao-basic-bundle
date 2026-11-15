<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'All extensions';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'AI connection';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Route', 'Which provider the extensions use to reach the language model.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, EU route (processing in the EU)',
    'requesty_global' => 'Requesty, standard route (worldwide)',
    'openai' => 'OpenAI (direct)',
    'custom' => 'Own address (OpenAI-compatible)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Address', 'Address of your own interface, for example https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Key', 'Stored encrypted and not shown again. Leave empty to keep the stored key.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Key stored (encrypted). Address: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'No key stored yet.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'The route "Own address" needs an address.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Test connection', 'Test connection'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Test connection', 'Test connection'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Each extension chooses the model in its own settings.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Address queried';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Connection works: HTTP %d in %d ms, %d models available.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'The route answers with HTTP %d (after %d ms). Answer:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'No connection: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Stored.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Not stored yet.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Not stored. Only needed for some functions.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Enter it now under "Access keys and licences"';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Key', 'Key 1 or 2 of the resource (Azure portal → Keys and Endpoint). Stored encrypted; leave empty to keep it.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Processing', 'Where Azure translates the texts.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Europe (France or West Europe)',
    'global' => 'Worldwide (nearest data centre)',
    'nam' => 'Americas',
    'apc' => 'Asia Pacific',
    'custom' => 'Own address of the resource',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Address of the resource', 'The address from the Azure portal, for example https://name.cognitiveservices.azure.com/translator/text/v3.0. Needed with restricted network access or for processing in Switzerland.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Region', 'The location of the resource, for example westeurope. Leave empty for a global resource.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Test connection', 'Test connection'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Address called';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Connection OK: HTTP %d in %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure answers with HTTP %d (after %d ms). Answer:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'No connection: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Key stored (encrypted). Address: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'No key stored yet.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'The processing "Own address of the resource" needs an address.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'No key stored yet. Only needed for translations.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Test connection', 'Test connection'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Key', 'The authentication key of your DeepL API account (Account → API keys). Stored encrypted; leave empty to keep it.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Key stored (encrypted). Interface: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'No key stored yet. Only needed for translations.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'No key stored yet.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free or Pro follows from the key: keys of the free plan end in ":fx".';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Test connection', 'Test connection'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Test connection', 'Test connection'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Address queried';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Connection OK: HTTP %1$d in %2$d ms. Used in this billing period: %3$s of %4$s characters.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Connection OK: HTTP %d in %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL answers with HTTP %d (after %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'The key is not accepted.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'The characters of this billing period are used up.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'No connection: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Used by %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Testing …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Can be tested once the access is saved.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'That did not work. Please reload the page and try again.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'The page is out of date. Please reload it and try again.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Terms of the extensions';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · edition %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · without licence';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'version %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'new';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'changed';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'show text';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Show the German version';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Not confirmed yet.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'The terms have changed. Please confirm the marked passages again.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Confirmed by %1$s on %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Translation for your understanding. The German version of the licence terms is binding.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'These passages are part of the licence terms of the extension (file LICENSE). New passages appear here when you license a higher edition or the terms change.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'I have read the terms and accept them.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Confirm';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Please tick the box first to confirm the terms.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Terms of %s confirmed.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Confirm now';
