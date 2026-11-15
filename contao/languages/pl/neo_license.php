<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licencje';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Klucz licencyjny', 'Klucz z potwierdzenia zamówienia. Wyczyść pole i zapisz, aby usunąć licencję z tej strony.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Sprawdź teraz';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Trwa sprawdzanie …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Ostatnie sprawdzenie: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'Serwis licencji był nieosiągalny podczas ostatniego sprawdzenia. Kolejna próba automatycznie w ciągu godziny lub przez „Sprawdź teraz”.';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Nieobjęte licencją: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'Klucz licencyjny znajduje się w potwierdzeniu zamówienia. Aby usunąć licencję z tej strony, wyczyść jej pole i zapisz.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Kup klucz licencyjny w sklepie';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Wyższa edycja lub przedłużenie w sklepie';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licencja %1$s ważna do %2$s dla %3$s.',
    'ok_ending' => 'Licencja %1$s wypowiedziana, ważna do %2$s dla %3$s.',
    'ok_grace' => 'Licencja %1$s wygasła %2$s. Bez przedłużenia pozostaje ważna do %4$s.',
    'ok_unlimited' => 'Licencja %1$s bezterminowa dla %3$s.',
    'no_key' => 'Bez licencji.',
    'invalid_key' => 'Ten klucz licencyjny nie jest ważny dla tego rozszerzenia.',
    'no_certificate' => 'Klucz licencyjny jest zapisany, ale jeszcze niepotwierdzony.',
    'unverifiable' => 'Certyfikatu serwisu licencji nie można sprawdzić tą wersją rozszerzenia.',
    'wrong_module' => 'Certyfikat należy do innego rozszerzenia.',
    'domain' => 'Licencja nie obejmuje tej domeny. Licencjonowane: %s.',
    'expired' => 'Licencja wygasła.',
    'revoked' => 'Licencja została cofnięta.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'ważna';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Ta domena nie jest wpisana do licencji. Wpisz ją w strefie klienta lub dokup dodatkowe domeny.',
    'installationsExhausted' => 'Wszystkie instalacje licencji są zajęte.',
    'domainsPerInstallationExceeded' => 'Ta instalacja używa więcej domen, niż pozwala licencja. Dokup dodatkowe domeny na instalację.',
    'other' => 'nieobjęta licencją',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Instalacje: zajęte %1$d z %2$d';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Nie można aktywować: przekroczono liczbę instalacji tej licencji. Licencja jest już aktywna we wszystkich dozwolonych instalacjach (domyślnie w jednej). Aby zwiększyć liczbę instalacji, skontaktuj się z %s.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['do %d domeny na instalację', 'liczba domen na instalację: do %d'];
