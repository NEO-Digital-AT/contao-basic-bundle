<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licence';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Licenčni ključ', 'Ključ iz potrditve naročila. Če polje izpraznite in shranite, se licenca odstrani s tega spletnega mesta.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Preveri zdaj';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Preverjanje …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Zadnje preverjanje: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'Licenčna storitev ob zadnjem preverjanju ni bila dosegljiva. Nov poskus samodejno v eni uri ali z »Preveri zdaj«.';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Licenca ne pokriva: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'Licenčni ključ najdete v potrditvi naročila. Če želite licenco odstraniti s tega spletnega mesta, izpraznite njeno polje in shranite.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Kupite licenčni ključ v trgovini';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Višja izdaja ali podaljšanje v trgovini';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licenca %1$s velja do %2$s za %3$s.',
    'ok_ending' => 'Licenca %1$s odpovedana, velja do %2$s za %3$s.',
    'ok_grace' => 'Licenca %1$s je potekla %2$s. Brez podaljšanja velja še do %4$s.',
    'ok_unlimited' => 'Licenca %1$s neomejeno veljavna za %3$s.',
    'no_key' => 'Brez licence.',
    'invalid_key' => 'Ta licenčni ključ ne velja za to razširitev.',
    'no_certificate' => 'Licenčni ključ je shranjen, vendar še ni potrjen.',
    'unverifiable' => 'Potrdila licenčne storitve s to različico razširitve ni mogoče preveriti.',
    'wrong_module' => 'Potrdilo pripada drugi razširitvi.',
    'domain' => 'Licenca ne velja za to domeno. Licencirano: %s.',
    'expired' => 'Licenca je potekla.',
    'revoked' => 'Licenca je bila preklicana.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'veljavna';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Ta domena ni vpisana v licenco. Vpišite jo v območju za stranke ali naročite dodatne domene.',
    'installationsExhausted' => 'Vse namestitve licence so zasedene.',
    'domainsPerInstallationExceeded' => 'Ta namestitev uporablja več domen, kot jih licenca dovoljuje. Naročite dodatne domene na namestitev.',
    'other' => 'ni pokrita z licenco',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Namestitve: zasedenih %1$d od %2$d';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Ni mogoče aktivirati: število namestitev te licence je preseženo. Licenca je že aktivna v vseh dovoljenih namestitvah (privzeto v eni). Za povečanje števila namestitev se obrnite na %s.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['do %d domene na namestitev', 'število domen na namestitev: do %d'];
