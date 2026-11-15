<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Všechna rozšíření';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'Připojení AI';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Trasa', 'Poskytovatel, přes kterého rozšíření přistupují k jazykovému modelu.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, trasa EU (zpracování v EU)',
    'requesty_global' => 'Requesty, standardní trasa (celosvětově)',
    'openai' => 'OpenAI (přímo)',
    'custom' => 'Vlastní adresa (kompatibilní s OpenAI)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Adresa', 'Adresa vlastního rozhraní, například https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Klíč', 'Ukládá se šifrovaně a znovu se nezobrazuje. Prázdné pole ponechá uložený klíč.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Klíč uložen (šifrovaně). Adresa: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Zatím není uložen žádný klíč.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'Trasa „Vlastní adresa“ vyžaduje adresu.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Otestovat připojení', 'Otestovat připojení'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Otestovat připojení', 'Otestovat připojení'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Každé rozšíření si model vybírá ve vlastním nastavení.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Dotazovaná adresa';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Připojení funguje: HTTP %d za %d ms, dostupné modely: %d.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'Trasa odpovídá kódem HTTP %d (po %d ms). Odpověď:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Žádné připojení: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Uloženo.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Zatím neuloženo.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Neuloženo. Potřeba jen pro některé funkce.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Zadat nyní v části „Přístupy a licence“';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Klíč', 'Klíč 1 nebo 2 prostředku (portál Azure → Klíče a koncový bod). Ukládá se šifrovaně; prázdné pole ho ponechá.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Zpracování', 'Kde Azure texty překládá.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Evropa (Francie nebo západní Evropa)',
    'global' => 'Celosvětově (nejbližší datové centrum)',
    'nam' => 'Amerika',
    'apc' => 'Asie a Tichomoří',
    'custom' => 'Vlastní adresa prostředku',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Adresa prostředku', 'Adresa z portálu Azure, například https://name.cognitiveservices.azure.com/translator/text/v3.0. Potřebná při omezeném síťovém přístupu nebo pro zpracování ve Švýcarsku.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Oblast', 'Umístění prostředku, například westeurope. U globálního prostředku nechte prázdné.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Ověřit připojení', 'Ověřit připojení'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Dotazovaná adresa';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Připojení v pořádku: HTTP %d za %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure odpovídá HTTP %d (po %d ms). Odpověď:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Žádné připojení: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Klíč uložen (šifrovaně). Adresa: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Zatím není uložen žádný klíč.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'Zpracování „Vlastní adresa prostředku“ potřebuje adresu.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Zatím není uložen žádný klíč. Potřebný jen pro překlady.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Ověřit připojení', 'Ověřit připojení'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Klíč', 'Ověřovací klíč vašeho účtu DeepL API (Účet → Klíče API). Ukládá se šifrovaně; prázdné pole ho ponechá.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Klíč uložen (šifrovaně). Rozhraní: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Zatím není uložen žádný klíč. Potřebný jen pro překlady.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Zatím není uložen žádný klíč.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free nebo Pro vyplývá z klíče: klíče bezplatného tarifu končí na „:fx“.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Otestovat připojení', 'Otestovat připojení'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Otestovat připojení', 'Otestovat připojení'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Dotazovaná adresa';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Připojení v pořádku: HTTP %1$d za %2$d ms. Spotřebováno v tomto zúčtovacím období: %3$s z %4$s znaků.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Připojení v pořádku: HTTP %d za %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL odpovídá kódem HTTP %d (po %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'Klíč nebyl přijat.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'Znaky tohoto zúčtovacího období jsou vyčerpány.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Žádné připojení: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Používá: %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Probíhá test …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Lze otestovat, jakmile je přístup uložen.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'To se nepovedlo. Načtěte stránku znovu a zkuste to ještě jednou.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'Stránka už není aktuální. Načtěte ji znovu a zkuste to ještě jednou.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Podmínky rozšíření';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · edice %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · bez licence';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'verze %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'nové';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'změněno';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'zobrazit text';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Zobrazit německé znění';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Zatím nepotvrzeno.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'Podmínky se změnily. Potvrďte prosím označené pasáže znovu.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Potvrdil(a) %1$s dne %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Překlad pro porozumění. Závazné je pouze německé znění licenčních podmínek.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Tyto pasáže jsou součástí licenčních podmínek rozšíření (soubor LICENSE). Nové pasáže se zde objeví, když licencujete vyšší edici nebo když se podmínky změní.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'Přečetl(a) jsem podmínky a souhlasím s nimi.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Potvrdit';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Nejprve zaškrtněte políčko pro potvrzení podmínek.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Podmínky rozšíření %s potvrzeny.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Potvrdit nyní';
