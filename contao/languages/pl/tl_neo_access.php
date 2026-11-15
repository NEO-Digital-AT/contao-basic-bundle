<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Wszystkie rozszerzenia';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'Połączenie AI';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Trasa', 'Dostawca, przez którego rozszerzenia łączą się z modelem językowym.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, trasa UE (przetwarzanie w UE)',
    'requesty_global' => 'Requesty, trasa standardowa (globalna)',
    'openai' => 'OpenAI (bezpośrednio)',
    'custom' => 'Własny adres (zgodny z OpenAI)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Adres', 'Adres własnego interfejsu, na przykład https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Klucz', 'Zapisywany w postaci zaszyfrowanej i nie wyświetlany ponownie. Puste pole zachowuje zapisany klucz.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Klucz zapisany (zaszyfrowany). Adres: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Nie zapisano jeszcze klucza.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'Trasa „Własny adres” wymaga adresu.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Sprawdź połączenie', 'Sprawdź połączenie'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Sprawdź połączenie', 'Sprawdź połączenie'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Każde rozszerzenie wybiera model we własnych ustawieniach.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Odpytany adres';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Połączenie działa: HTTP %d w %d ms, dostępne modele: %d.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'Trasa odpowiada kodem HTTP %d (po %d ms). Odpowiedź:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Brak połączenia: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Zapisano.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Jeszcze nie zapisano.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Nie zapisano. Potrzebne tylko do niektórych funkcji.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Wprowadź teraz w „Dostępy i licencje”';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Klucz', 'Klucz 1 lub 2 zasobu (portal Azure → Klucze i punkt końcowy). Zapisywany zaszyfrowany; puste pole go zachowuje.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Przetwarzanie', 'Gdzie Azure tłumaczy teksty.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Europa (Francja lub Europa Zachodnia)',
    'global' => 'Cały świat (najbliższe centrum danych)',
    'nam' => 'Ameryka',
    'apc' => 'Azja i Pacyfik',
    'custom' => 'Własny adres zasobu',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Adres zasobu', 'Adres z portalu Azure, na przykład https://name.cognitiveservices.azure.com/translator/text/v3.0. Potrzebny przy ograniczonym dostępie sieciowym lub do przetwarzania w Szwajcarii.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Region', 'Lokalizacja zasobu, na przykład westeurope. Przy zasobie globalnym pozostaw puste.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Sprawdź połączenie', 'Sprawdź połączenie'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Odpytywany adres';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Połączenie w porządku: HTTP %d w %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure odpowiada HTTP %d (po %d ms). Odpowiedź:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Brak połączenia: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Klucz zapisany (zaszyfrowany). Adres: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Nie zapisano jeszcze klucza.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'Przetwarzanie „Własny adres zasobu” wymaga adresu.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Nie zapisano jeszcze klucza. Potrzebny tylko do tłumaczeń.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Sprawdź połączenie', 'Sprawdź połączenie'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Klucz', 'Klucz uwierzytelniający konta DeepL API (Konto → Klucze API). Zapisywany zaszyfrowany; puste pole go zachowuje.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Klucz zapisany (zaszyfrowany). Interfejs: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Nie zapisano jeszcze klucza. Potrzebny tylko do tłumaczeń.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Nie zapisano jeszcze klucza.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free albo Pro wynika z klucza: klucze bezpłatnego planu kończą się na „:fx”.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Sprawdź połączenie', 'Sprawdź połączenie'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Sprawdź połączenie', 'Sprawdź połączenie'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Odpytany adres';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Połączenie działa: HTTP %1$d w %2$d ms. Zużyto w tym okresie rozliczeniowym: %3$s z %4$s znaków.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Połączenie w porządku: HTTP %d w %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL odpowiada kodem HTTP %d (po %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'Klucz nie został zaakceptowany.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'Znaki z tego okresu rozliczeniowego zostały wykorzystane.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Brak połączenia: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Używane przez: %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Trwa test …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Można przetestować po zapisaniu dostępu.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'Nie udało się. Odśwież stronę i spróbuj ponownie.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'Strona jest nieaktualna. Odśwież ją i spróbuj ponownie.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Warunki rozszerzeń';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · edycja %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · bez licencji';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'wersja %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'nowe';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'zmienione';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'pokaż tekst';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Pokaż wersję niemiecką';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Jeszcze nie potwierdzono.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'Warunki zostały zmienione. Prosimy ponownie potwierdzić zaznaczone fragmenty.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Potwierdzone przez %1$s dnia %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Tłumaczenie dla zrozumienia. Wiążąca jest wyłącznie niemiecka wersja warunków licencji.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Te fragmenty są częścią warunków licencji rozszerzenia (plik LICENSE). Nowe fragmenty pojawiają się tutaj, gdy wykupią Państwo licencję na wyższą edycję lub gdy warunki się zmienią.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'Zapoznałem(-am) się z warunkami i akceptuję je.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Potwierdź';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Najpierw zaznacz pole, aby potwierdzić warunki.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Warunki rozszerzenia %s potwierdzone.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Potwierdź teraz';
