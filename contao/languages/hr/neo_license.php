<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licence';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Licencni ključ', 'Ključ iz potvrde narudžbe. Ispraznite polje i spremite kako biste uklonili licencu s ove web-stranice.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Provjeri sada';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Provjera …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Zadnja provjera: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'Licencna usluga pri zadnjoj provjeri nije bila dostupna. Novi pokušaj automatski unutar sat vremena ili s „Provjeri sada”.';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Licenca ne pokriva: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'Licencni ključ nalazi se u potvrdi narudžbe. Za uklanjanje licence s ove web-stranice ispraznite njezino polje i spremite.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Kupite licencni ključ u trgovini';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Viša razina ili produljenje u trgovini';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licenca %1$s vrijedi do %2$s za %3$s.',
    'ok_ending' => 'Licenca %1$s otkazana, vrijedi do %2$s za %3$s.',
    'ok_grace' => 'Licenca %1$s istekla je %2$s. Bez produljenja vrijedi do %4$s.',
    'ok_unlimited' => 'Licenca %1$s neograničeno vrijedi za %3$s.',
    'no_key' => 'Bez licence.',
    'invalid_key' => 'Ovaj licencni ključ ne vrijedi za ovo proširenje.',
    'no_certificate' => 'Licencni ključ je spremljen, ali još nije potvrđen.',
    'unverifiable' => 'Certifikat licencne usluge ne može se provjeriti ovom verzijom proširenja.',
    'wrong_module' => 'Certifikat pripada drugom proširenju.',
    'domain' => 'Licenca ne pokriva ovu domenu. Licencirano: %s.',
    'expired' => 'Licenca je istekla.',
    'revoked' => 'Licenca je povučena.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'valjana';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Ova domena nije upisana u licencu. Upišite je u korisničkom području ili naručite dodatne domene.',
    'installationsExhausted' => 'Sve instalacije licence su zauzete.',
    'domainsPerInstallationExceeded' => 'Ova instalacija koristi više domena nego što licenca dopušta. Naručite dodatne domene po instalaciji.',
    'other' => 'nije pokrivena licencom',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Instalacije: zauzeto %1$d od %2$d';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Nije moguće aktivirati: premašen je broj instalacija ove licence. Licenca je već aktivna u svim dopuštenim instalacijama (zadano u jednoj). Za povećanje broja instalacija obratite se na %s.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['do %d domene po instalaciji', 'do %d domena po instalaciji'];
