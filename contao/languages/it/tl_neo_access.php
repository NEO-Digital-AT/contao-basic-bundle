<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Tutte le estensioni';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'Connessione IA';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Percorso', 'Il fornitore tramite il quale le estensioni raggiungono il modello linguistico.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, percorso UE (elaborazione nell’UE)',
    'requesty_global' => 'Requesty, percorso standard (mondiale)',
    'openai' => 'OpenAI (diretto)',
    'custom' => 'Indirizzo proprio (compatibile con OpenAI)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Indirizzo', 'Indirizzo della propria interfaccia, ad esempio https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Chiave', 'Viene salvata cifrata e non viene più mostrata. Lasciare vuoto per mantenere la chiave salvata.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Chiave salvata (cifrata). Indirizzo: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Nessuna chiave ancora salvata.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'Il percorso «Indirizzo proprio» richiede un indirizzo.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Verifica connessione', 'Verifica connessione'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Verifica connessione', 'Verifica connessione'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Ogni estensione sceglie il modello nelle proprie impostazioni.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Indirizzo interrogato';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Connessione funzionante: HTTP %d in %d ms, %d modelli disponibili.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'Il percorso risponde con HTTP %d (dopo %d ms). Risposta:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Nessuna connessione: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Salvato.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Non ancora salvato.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Non salvato. Serve solo per alcune funzioni.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Inserirla ora in «Accessi e licenze»';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Chiave', 'Chiave 1 o 2 della risorsa (portale Azure → Chiavi ed endpoint). Viene salvata cifrata; lasciare vuoto per mantenerla.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Elaborazione', 'Dove Azure traduce i testi.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Europa (Francia o Europa occidentale)',
    'global' => 'Mondo (data center più vicino)',
    'nam' => 'Americhe',
    'apc' => 'Asia-Pacifico',
    'custom' => 'Indirizzo proprio della risorsa',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Indirizzo della risorsa', 'L’indirizzo dal portale Azure, per esempio https://name.cognitiveservices.azure.com/translator/text/v3.0. Necessario con accesso di rete limitato o per l’elaborazione in Svizzera.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Regione', 'La posizione della risorsa, per esempio westeurope. Lasciare vuoto per una risorsa globale.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Verifica connessione', 'Verifica connessione'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Indirizzo interrogato';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Connessione corretta: HTTP %d in %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure risponde con HTTP %d (dopo %d ms). Risposta:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Nessuna connessione: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Chiave salvata (cifrata). Indirizzo: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Nessuna chiave ancora salvata.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'L’elaborazione «Indirizzo proprio della risorsa» richiede un indirizzo.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Nessuna chiave salvata. Serve solo per le traduzioni.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Verifica connessione', 'Verifica connessione'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Chiave', 'La chiave di autenticazione del suo account DeepL API (Account → Chiavi API). Viene salvata cifrata; lasciare vuoto per mantenerla.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Chiave salvata (cifrata). Interfaccia: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Nessuna chiave salvata. Serve solo per le traduzioni.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Nessuna chiave salvata.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free o Pro dipende dalla chiave: le chiavi del piano gratuito terminano con «:fx».';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Verifica connessione', 'Verifica connessione'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Verifica connessione', 'Verifica connessione'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Indirizzo interrogato';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Connessione OK: HTTP %1$d in %2$d ms. Usati in questo periodo di fatturazione: %3$s di %4$s caratteri.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Connessione corretta: HTTP %d in %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL risponde con HTTP %d (dopo %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'La chiave non viene accettata.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'I caratteri di questo periodo di fatturazione sono esauriti.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Nessuna connessione: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Usato da %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Test in corso …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Si può verificare non appena l’accesso è salvato.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'Non è riuscito. Ricarichi la pagina e riprovi.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'La pagina non è più aggiornata. La ricarichi e riprovi.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Condizioni delle estensioni';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · edizione %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · senza licenza';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'versione %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'nuovo';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'modificato';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'mostra il testo';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Mostra la versione tedesca';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Non ancora confermato.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'Le condizioni sono cambiate. Confermi di nuovo i passaggi contrassegnati.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Confermato da %1$s il %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Traduzione a scopo di comprensione. Fa fede solo la versione tedesca delle condizioni di licenza.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Questi passaggi fanno parte delle condizioni di licenza dell\'estensione (file LICENSE). Nuovi passaggi compaiono qui quando acquista la licenza di un\'edizione superiore o quando le condizioni cambiano.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'Ho letto le condizioni e le accetto.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Conferma';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Spunti prima la casella per confermare le condizioni.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Condizioni di %s confermate.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Conferma ora';
