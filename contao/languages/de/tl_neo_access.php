<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Alle Erweiterungen';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'KI-Verbindung';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Route', 'Über welchen Anbieter die Erweiterungen das Sprachmodell erreichen.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, EU-Route (Verarbeitung in der EU)',
    'requesty_global' => 'Requesty, Standard-Route (weltweit)',
    'openai' => 'OpenAI (direkt)',
    'custom' => 'Eigene Adresse (OpenAI-kompatibel)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Adresse', 'Adresse der eigenen Schnittstelle, zum Beispiel https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Schlüssel', 'Wird verschlüsselt gespeichert und nicht wieder angezeigt. Leer lassen behält den gespeicherten Schlüssel.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Schlüssel gespeichert (verschlüsselt). Adresse: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Noch kein Schlüssel gespeichert.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'Die Route „Eigene Adresse“ braucht eine Adresse.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Verbindung prüfen', 'Verbindung prüfen'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Verbindung prüfen', 'Verbindung prüfen'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Das Modell wählt jede Erweiterung in ihren eigenen Einstellungen.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Abgefragte Adresse';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Verbindung in Ordnung: HTTP %d in %d ms, %d Modelle verfügbar.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'Die Route antwortet mit HTTP %d (nach %d ms). Antwort:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Keine Verbindung: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Hinterlegt.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Noch nicht hinterlegt.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Nicht hinterlegt. Wird nur für einzelne Funktionen gebraucht.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Jetzt unter „Zugänge und Lizenzen“ hinterlegen';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Schlüssel', 'Schlüssel 1 oder 2 der Ressource (Azure-Portal → Schlüssel und Endpunkt). Wird verschlüsselt gespeichert; leer lassen behält ihn.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Verarbeitung', 'Wo Azure die Texte übersetzt.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Europa (Frankreich oder Westeuropa)',
    'global' => 'Weltweit (nächstes Rechenzentrum)',
    'nam' => 'Amerika',
    'apc' => 'Asien-Pazifik',
    'custom' => 'Eigene Adresse der Ressource',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Adresse der Ressource', 'Die Adresse aus dem Azure-Portal, zum Beispiel https://name.cognitiveservices.azure.com/translator/text/v3.0. Nötig bei eingeschränktem Netzwerkzugang oder für die Verarbeitung in der Schweiz.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Region', 'Der Standort der Ressource, zum Beispiel westeurope. Bei einer globalen Ressource leer lassen.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Verbindung prüfen', 'Verbindung prüfen'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Abgefragte Adresse';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Verbindung in Ordnung: HTTP %d in %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure antwortet mit HTTP %d (nach %d ms). Antwort:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Keine Verbindung: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Schlüssel gespeichert (verschlüsselt). Adresse: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Noch kein Schlüssel gespeichert.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'Die Verarbeitung „Eigene Adresse der Ressource“ braucht eine Adresse.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Noch kein Schlüssel gespeichert. Wird nur für Übersetzungen gebraucht.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Verbindung prüfen', 'Verbindung prüfen'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Schlüssel', 'Der Authentifizierungsschlüssel Ihres DeepL-API-Kontos (Konto → API-Schlüssel). Wird verschlüsselt gespeichert; leer lassen behält ihn.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Schlüssel gespeichert (verschlüsselt). Schnittstelle: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Noch kein Schlüssel gespeichert. Wird nur für Übersetzungen gebraucht.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Noch kein Schlüssel gespeichert.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free oder Pro ergibt sich aus dem Schlüssel: Schlüssel des kostenlosen Tarifs enden auf „:fx“.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Verbindung prüfen', 'Verbindung prüfen'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Verbindung prüfen', 'Verbindung prüfen'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Abgefragte Adresse';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Verbindung in Ordnung: HTTP %1$d in %2$d ms. In diesem Abrechnungszeitraum verbraucht: %3$s von %4$s Zeichen.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Verbindung in Ordnung: HTTP %d in %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL antwortet mit HTTP %d (nach %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'Der Schlüssel wird nicht angenommen.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'Die Zeichen dieses Abrechnungszeitraums sind aufgebraucht.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Keine Verbindung: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Genutzt von %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Wird geprüft …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Kann geprüft werden, sobald der Zugang gespeichert ist.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'Das hat nicht geklappt. Bitte laden Sie die Seite neu und versuchen Sie es noch einmal.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'Die Seite ist nicht mehr aktuell. Bitte laden Sie sie neu und versuchen Sie es noch einmal.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Bedingungen der Erweiterungen';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · Stufe %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · ohne Lizenz';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'Fassung %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'neu';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'geändert';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'Text anzeigen';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Deutsche Fassung anzeigen';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Noch nicht bestätigt.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'Die Bedingungen wurden geändert. Bitte bestätigen Sie die markierten Passagen erneut.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Bestätigt von %1$s am %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Übersetzung zum Verständnis. Verbindlich ist die deutsche Fassung der Lizenzbedingungen.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Diese Passagen sind Teil der Lizenzbedingungen der Erweiterung (Datei LICENSE). Neue Passagen erscheinen hier, wenn Sie eine höhere Stufe lizenzieren oder sich die Bedingungen ändern.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'Ich habe die Bedingungen gelesen und akzeptiere sie.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Bestätigen';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Bitte setzen Sie zuerst das Häkchen, um die Bedingungen zu bestätigen.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Bedingungen für %s bestätigt.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Jetzt bestätigen';
