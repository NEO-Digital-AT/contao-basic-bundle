<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licenze';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Chiave di licenza', 'La chiave indicata nella conferma d’ordine. Svuotare il campo e salvare per rimuovere la licenza da questo sito.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Verifica ora';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Verifica in corso …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Ultima verifica: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'Il servizio licenze non era raggiungibile all’ultima verifica. Nuovo tentativo automatico entro un’ora o con «Verifica ora».';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Non coperto dalla licenza: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'La chiave di licenza si trova nella conferma d’ordine. Per rimuovere una licenza da questo sito, svuotare il suo campo e salvare.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Acquista una chiave di licenza nello store';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Edizione superiore o rinnovo nello store';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licenza %1$s valida fino al %2$s per %3$s.',
    'ok_ending' => 'Licenza %1$s disdetta, valida fino al %2$s per %3$s.',
    'ok_grace' => 'Licenza %1$s scaduta il %2$s. Senza rinnovo resta valida fino al %4$s.',
    'ok_unlimited' => 'Licenza %1$s illimitata per %3$s.',
    'no_key' => 'Senza licenza.',
    'invalid_key' => 'Questa chiave di licenza non è valida per questa estensione.',
    'no_certificate' => 'La chiave di licenza è salvata, ma non ancora confermata.',
    'unverifiable' => 'Il certificato del servizio licenze non può essere verificato con questa versione dell’estensione.',
    'wrong_module' => 'Il certificato appartiene a un’altra estensione.',
    'domain' => 'La licenza non copre questo dominio. Domini con licenza: %s.',
    'expired' => 'La licenza è scaduta.',
    'revoked' => 'La licenza è stata revocata.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'valida';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Questo dominio non è registrato nella licenza. Registrarlo nell’area clienti o acquistare domini aggiuntivi.',
    'installationsExhausted' => 'Tutte le installazioni della licenza sono in uso.',
    'domainsPerInstallationExceeded' => 'Questa installazione usa più domini di quanti ne consenta la licenza. Acquistare domini aggiuntivi per installazione.',
    'other' => 'non coperto dalla licenza',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Installazioni: %1$d di %2$d in uso';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Impossibile attivare: il numero di installazioni di questa licenza è stato superato. La licenza è già attiva in tutte le installazioni consentite (una per impostazione predefinita). Per aumentare il numero di installazioni rivolgersi a %s.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['fino a %d dominio per installazione', 'fino a %d domini per installazione'];
