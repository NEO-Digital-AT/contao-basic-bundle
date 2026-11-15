<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Minden bővítmény';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'MI-kapcsolat';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Útvonal', 'A szolgáltató, amelyen keresztül a bővítmények elérik a nyelvi modellt.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, EU-útvonal (feldolgozás az EU-ban)',
    'requesty_global' => 'Requesty, normál útvonal (világszerte)',
    'openai' => 'OpenAI (közvetlenül)',
    'custom' => 'Saját cím (OpenAI-kompatibilis)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Cím', 'A saját interfész címe, például https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Kulcs', 'Titkosítva tárolódik, és nem jelenik meg újra. Ha üresen hagyja, a tárolt kulcs megmarad.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Kulcs tárolva (titkosítva). Cím: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Még nincs tárolt kulcs.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'A „Saját cím” útvonalhoz cím szükséges.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Kapcsolat ellenőrzése', 'Kapcsolat ellenőrzése'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Kapcsolat ellenőrzése', 'Kapcsolat ellenőrzése'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Minden bővítmény a saját beállításaiban választja ki a modellt.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Lekérdezett cím';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'A kapcsolat működik: HTTP %d, %d ms, elérhető modellek: %d.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'Az útvonal HTTP %d kóddal válaszol (%d ms után). Válasz:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Nincs kapcsolat: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Tárolva.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Még nincs tárolva.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Nincs tárolva. Csak egyes funkciókhoz kell.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Megadás most: „Hozzáférések és licencek”';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Kulcs', 'Az erőforrás 1. vagy 2. kulcsa (Azure-portál → Kulcsok és végpont). Titkosítva tárolódik; üresen hagyva megmarad.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Feldolgozás', 'Hol fordítja le az Azure a szövegeket.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Európa (Franciaország vagy Nyugat-Európa)',
    'global' => 'Világszerte (legközelebbi adatközpont)',
    'nam' => 'Amerika',
    'apc' => 'Ázsia és a csendes-óceáni térség',
    'custom' => 'Az erőforrás saját címe',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Az erőforrás címe', 'A cím az Azure-portálról, például https://name.cognitiveservices.azure.com/translator/text/v3.0. Korlátozott hálózati hozzáférésnél vagy svájci feldolgozáshoz szükséges.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Régió', 'Az erőforrás helye, például westeurope. Globális erőforrásnál hagyja üresen.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Kapcsolat ellenőrzése', 'Kapcsolat ellenőrzése'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Lekérdezett cím';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'A kapcsolat rendben: HTTP %d, %d ms alatt.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Az Azure HTTP %d kóddal válaszol (%d ms után). Válasz:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Nincs kapcsolat: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Kulcs tárolva (titkosítva). Cím: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Még nincs tárolt kulcs.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'Az „Az erőforrás saját címe” feldolgozáshoz meg kell adni a címet.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Még nincs mentett kulcs. Csak a fordításokhoz szükséges.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Kapcsolat ellenőrzése', 'Kapcsolat ellenőrzése'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Kulcs', 'A DeepL API-fiókja hitelesítési kulcsa (Fiók → API-kulcsok). Titkosítva tárolódik; üresen hagyva megmarad.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Kulcs tárolva (titkosítva). Felület: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Még nincs mentett kulcs. Csak a fordításokhoz szükséges.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Még nincs mentett kulcs.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'A kulcsból derül ki, hogy Free vagy Pro: az ingyenes csomag kulcsai „:fx” végződésűek.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Kapcsolat ellenőrzése', 'Kapcsolat ellenőrzése'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Kapcsolat ellenőrzése', 'Kapcsolat ellenőrzése'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Lekérdezett cím';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'A kapcsolat rendben: HTTP %1$d, %2$d ms. Felhasználva ebben az elszámolási időszakban: %3$s / %4$s karakter.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'A kapcsolat rendben: HTTP %d, %d ms alatt.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'A DeepL HTTP %d kóddal válaszol (%d ms után).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'A kulcsot nem fogadja el.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'Az elszámolási időszak karakterkerete elfogyott.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Nincs kapcsolat: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Használja: %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Tesztelés …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Akkor tesztelhető, ha a hozzáférés mentve van.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'Ez nem sikerült. Töltse be újra az oldalt, és próbálja meg ismét.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'Az oldal már nem friss. Töltse be újra, és próbálja meg ismét.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'A bővítmények feltételei';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · %2$s kiadás';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · licenc nélkül';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = '%d. változat';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'új';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'módosult';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'szöveg megjelenítése';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Német változat megjelenítése';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Még nincs megerősítve.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'A feltételek megváltoztak. Kérjük, erősítse meg újra a megjelölt részeket.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Megerősítette: %1$s, %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Fordítás a megértéshez. Kizárólag a licencfeltételek német változata kötelező érvényű.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Ezek a részek a bővítmény licencfeltételeinek részei (LICENSE fájl). Új részek akkor jelennek meg itt, ha magasabb kiadásra vált licencet, vagy ha a feltételek megváltoznak.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'Elolvastam a feltételeket, és elfogadom azokat.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Megerősítés';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'A feltételek megerősítéséhez először jelölje be a négyzetet.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = '%s feltételei megerősítve.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Megerősítés most';
