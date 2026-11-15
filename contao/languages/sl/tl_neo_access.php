<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Vse razširitve';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'Povezava UI';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Pot', 'Ponudnik, prek katerega razširitve dostopajo do jezikovnega modela.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, pot EU (obdelava v EU)',
    'requesty_global' => 'Requesty, standardna pot (po vsem svetu)',
    'openai' => 'OpenAI (neposredno)',
    'custom' => 'Lasten naslov (združljiv z OpenAI)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Naslov', 'Naslov lastnega vmesnika, na primer https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Ključ', 'Shrani se šifrirano in se ne prikaže več. Prazno polje ohrani shranjeni ključ.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Ključ shranjen (šifrirano). Naslov: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Ključ še ni shranjen.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'Pot »Lasten naslov« potrebuje naslov.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Preveri povezavo', 'Preveri povezavo'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Preveri povezavo', 'Preveri povezavo'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Vsaka razširitev izbere model v svojih nastavitvah.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Poizvedeni naslov';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Povezava deluje: HTTP %d v %d ms, na voljo modelov: %d.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'Pot odgovarja s HTTP %d (po %d ms). Odgovor:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Ni povezave: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Shranjeno.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Še ni shranjeno.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Ni shranjeno. Potrebno le za nekatere funkcije.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Vnesite zdaj v »Dostopi in licence«';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Ključ', 'Ključ 1 ali 2 vira (portal Azure → Ključi in končna točka). Shrani se šifrirano; prazno polje ga ohrani.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Obdelava', 'Kje Azure prevaja besedila.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Evropa (Francija ali Zahodna Evropa)',
    'global' => 'Po vsem svetu (najbližji podatkovni center)',
    'nam' => 'Amerika',
    'apc' => 'Azija in Pacifik',
    'custom' => 'Lasten naslov vira',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Naslov vira', 'Naslov iz portala Azure, na primer https://name.cognitiveservices.azure.com/translator/text/v3.0. Potreben pri omejenem omrežnem dostopu ali za obdelavo v Švici.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Regija', 'Lokacija vira, na primer westeurope. Pri globalnem viru pustite prazno.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Preveri povezavo', 'Preveri povezavo'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Poizvedovani naslov';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Povezava je v redu: HTTP %d v %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure odgovarja s HTTP %d (po %d ms). Odgovor:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Ni povezave: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Ključ shranjen (šifrirano). Naslov: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Ključ še ni shranjen.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'Obdelava »Lasten naslov vira« potrebuje naslov.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Ključ še ni shranjen. Potreben je samo za prevode.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Preveri povezavo', 'Preveri povezavo'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Ključ', 'Ključ za preverjanje pristnosti vašega računa DeepL API (Račun → Ključi API). Shrani se šifrirano; prazno polje ga ohrani.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Ključ shranjen (šifrirano). Vmesnik: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Ključ še ni shranjen. Potreben je samo za prevode.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Ključ še ni shranjen.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free ali Pro izhaja iz ključa: ključi brezplačnega paketa se končajo na »:fx«.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Preveri povezavo', 'Preveri povezavo'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Preveri povezavo', 'Preveri povezavo'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Poizvedeni naslov';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Povezava deluje: HTTP %1$d v %2$d ms. Porabljeno v tem obračunskem obdobju: %3$s od %4$s znakov.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Povezava je v redu: HTTP %d v %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL odgovarja s HTTP %d (po %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'Ključ ni sprejet.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'Znaki tega obračunskega obdobja so porabljeni.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Ni povezave: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Uporablja: %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Preizkušanje …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Preizkusiti ga je mogoče, ko je dostop shranjen.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'To ni uspelo. Znova naložite stran in poskusite še enkrat.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'Stran ni več aktualna. Znova jo naložite in poskusite še enkrat.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Pogoji razširitev';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · izdaja %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · brez licence';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'različica %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'novo';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'spremenjeno';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'prikaži besedilo';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Prikaži nemško različico';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Še ni potrjeno.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'Pogoji so se spremenili. Prosimo, znova potrdite označene odstavke.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Potrdil(a) %1$s dne %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Prevod za razumevanje. Zavezujoča je samo nemška različica licenčnih pogojev.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Ti odstavki so del licenčnih pogojev razširitve (datoteka LICENSE). Novi odstavki se prikažejo tukaj, ko licencirate višjo izdajo ali ko se pogoji spremenijo.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'Pogoje sem prebral(a) in jih sprejemam.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Potrdi';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Najprej označite polje, da potrdite pogoje.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Pogoji za %s potrjeni.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Potrdi zdaj';
