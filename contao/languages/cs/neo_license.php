<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licence';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Licenční klíč', 'Klíč z potvrzení objednávky. Vyprázdněním pole a uložením odeberete licenci z tohoto webu.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Zkontrolovat nyní';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Probíhá kontrola …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Naposledy zkontrolováno: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'Licenční služba byla při poslední kontrole nedostupná. Nový pokus proběhne automaticky do hodiny nebo pomocí „Zkontrolovat nyní“.';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Licence se nevztahuje na: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'Licenční klíč najdete v potvrzení objednávky. Chcete-li licenci z tohoto webu odebrat, vyprázdněte její pole a uložte.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Koupit licenční klíč v obchodě';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Vyšší edice nebo prodloužení v obchodě';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licence %1$s platná do %2$s pro %3$s.',
    'ok_ending' => 'Licence %1$s vypovězena, platná do %2$s pro %3$s.',
    'ok_grace' => 'Licence %1$s vypršela %2$s. Bez prodloužení zůstává platná do %4$s.',
    'ok_unlimited' => 'Licence %1$s neomezeně platná pro %3$s.',
    'no_key' => 'Bez licence.',
    'invalid_key' => 'Tento licenční klíč pro toto rozšíření neplatí.',
    'no_certificate' => 'Licenční klíč je uložen, ale zatím nebyl potvrzen.',
    'unverifiable' => 'Certifikát licenční služby nelze touto verzí rozšíření ověřit.',
    'wrong_module' => 'Certifikát patří k jinému rozšíření.',
    'domain' => 'Licence se nevztahuje na tuto doménu. Licencováno: %s.',
    'expired' => 'Platnost licence vypršela.',
    'revoked' => 'Licence byla odebrána.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'platná';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Tato doména není v licenci zapsána. Zapište ji v zákaznické sekci nebo si objednejte další domény.',
    'installationsExhausted' => 'Všechny instalace licence jsou obsazeny.',
    'domainsPerInstallationExceeded' => 'Tato instalace používá více domén, než licence povoluje. Objednejte si další domény na instalaci.',
    'other' => 'není pokryta licencí',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Instalace: obsazeno %1$d z %2$d';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Nelze aktivovat: počet instalací této licence je překročen. Licence je již aktivní ve všech povolených instalacích (ve výchozím stavu v jedné). Chcete-li počet instalací navýšit, obraťte se na %s.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['až %d doména na instalaci', 'domén na instalaci: až %d'];
