<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Sva proširenja';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'AI veza';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Ruta', 'Pružatelj preko kojeg proširenja pristupaju jezičnom modelu.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, EU ruta (obrada u EU)',
    'requesty_global' => 'Requesty, standardna ruta (globalno)',
    'openai' => 'OpenAI (izravno)',
    'custom' => 'Vlastita adresa (kompatibilna s OpenAI)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Adresa', 'Adresa vlastitog sučelja, na primjer https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Ključ', 'Sprema se šifrirano i više se ne prikazuje. Prazno polje zadržava spremljeni ključ.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Ključ spremljen (šifrirano). Adresa: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Ključ još nije spremljen.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'Ruta „Vlastita adresa” treba adresu.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Provjeri vezu', 'Provjeri vezu'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Provjeri vezu', 'Provjeri vezu'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Svako proširenje odabire model u vlastitim postavkama.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Upitana adresa';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Veza radi: HTTP %d za %d ms, dostupni modeli: %d.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'Ruta odgovara s HTTP %d (nakon %d ms). Odgovor:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Nema veze: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Spremljeno.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Još nije spremljeno.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Nije spremljeno. Potrebno samo za neke funkcije.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Unesite sada pod „Pristupi i licence”';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Ključ', 'Ključ 1 ili 2 resursa (portal Azure → Ključevi i krajnja točka). Sprema se šifrirano; prazno polje ga zadržava.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Obrada', 'Gdje Azure prevodi tekstove.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Europa (Francuska ili Zapadna Europa)',
    'global' => 'Cijeli svijet (najbliži podatkovni centar)',
    'nam' => 'Amerika',
    'apc' => 'Azija i Pacifik',
    'custom' => 'Vlastita adresa resursa',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Adresa resursa', 'Adresa iz portala Azure, na primjer https://name.cognitiveservices.azure.com/translator/text/v3.0. Potrebna kod ograničenog mrežnog pristupa ili za obradu u Švicarskoj.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Regija', 'Lokacija resursa, na primjer westeurope. Kod globalnog resursa ostavite prazno.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Provjeri vezu', 'Provjeri vezu'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Upitana adresa';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Veza je u redu: HTTP %d za %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure odgovara s HTTP %d (nakon %d ms). Odgovor:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Nema veze: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Ključ spremljen (šifrirano). Adresa: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Ključ još nije spremljen.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'Obrada „Vlastita adresa resursa” treba adresu.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Ključ još nije spremljen. Potreban je samo za prijevode.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Provjeri vezu', 'Provjeri vezu'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Ključ', 'Ključ za autentifikaciju vašeg računa DeepL API (Račun → API ključevi). Sprema se šifrirano; prazno polje ga zadržava.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Ključ spremljen (šifrirano). Sučelje: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Ključ još nije spremljen. Potreban je samo za prijevode.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Ključ još nije spremljen.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free ili Pro proizlazi iz ključa: ključevi besplatnog paketa završavaju na „:fx”.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Provjeri vezu', 'Provjeri vezu'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Provjeri vezu', 'Provjeri vezu'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Upitana adresa';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Veza je u redu: HTTP %1$d za %2$d ms. Potrošeno u ovom obračunskom razdoblju: %3$s od %4$s znakova.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Veza je u redu: HTTP %d za %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL odgovara s HTTP %d (nakon %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'Ključ nije prihvaćen.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'Znakovi ovog obračunskog razdoblja su potrošeni.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Nema veze: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Koristi: %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Testiranje …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Može se testirati čim je pristup spremljen.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'To nije uspjelo. Ponovno učitajte stranicu i pokušajte još jednom.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'Stranica više nije aktualna. Ponovno je učitajte i pokušajte još jednom.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Uvjeti proširenja';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · izdanje %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · bez licence';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'verzija %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'novo';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'izmijenjeno';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'prikaži tekst';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Prikaži njemačku verziju';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Još nije potvrđeno.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'Uvjeti su se promijenili. Molimo ponovno potvrdite označene odlomke.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Potvrdio/la %1$s dana %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Prijevod radi razumijevanja. Obvezujuća je samo njemačka verzija licencnih uvjeta.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Ovi odlomci dio su licencnih uvjeta proširenja (datoteka LICENSE). Novi odlomci pojavljuju se ovdje kada licencirate više izdanje ili kada se uvjeti promijene.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'Pročitao/la sam uvjete i prihvaćam ih.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Potvrdi';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Najprije označite okvir kako biste potvrdili uvjete.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Uvjeti za %s potvrđeni.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Potvrdi sada';
