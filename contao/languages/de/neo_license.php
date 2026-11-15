<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Lizenzen';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Lizenzschlüssel', 'Der Schlüssel aus der Bestellbestätigung. Feld leeren und speichern entfernt die Lizenz von dieser Website.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Jetzt prüfen';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Wird geprüft …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Zuletzt geprüft: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'Der Lizenzdienst war bei der letzten Prüfung nicht erreichbar. Neuer Versuch automatisch in einer Stunde oder mit „Jetzt prüfen“.';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Nicht von der Lizenz gedeckt: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'Den Lizenzschlüssel finden Sie in der Bestellbestätigung. Um eine Lizenz von dieser Website zu entfernen, leeren Sie ihr Feld und speichern.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Lizenzschlüssel im Store kaufen';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Höhere Stufe oder Verlängerung im Store';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basis',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Lizenz %1$s gültig bis %2$s für %3$s.',
    'ok_ending' => 'Lizenz %1$s gekündigt, gültig bis %2$s für %3$s.',
    'ok_grace' => 'Lizenz %1$s am %2$s abgelaufen. Ohne Verlängerung gilt sie noch bis %4$s.',
    'ok_unlimited' => 'Lizenz %1$s unbegrenzt gültig für %3$s.',
    'no_key' => 'Ohne Lizenz.',
    'invalid_key' => 'Dieser Lizenzschlüssel gilt nicht für diese Erweiterung.',
    'no_certificate' => 'Der Lizenzschlüssel ist gespeichert, aber noch nicht bestätigt.',
    'unverifiable' => 'Der Nachweis des Lizenzdienstes lässt sich mit dieser Fassung der Erweiterung nicht prüfen.',
    'wrong_module' => 'Der Nachweis gehört zu einer anderen Erweiterung.',
    'domain' => 'Die Lizenz gilt nicht für diese Domain. Lizenziert: %s.',
    'expired' => 'Die Lizenz ist abgelaufen.',
    'revoked' => 'Die Lizenz wurde zurückgezogen.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'gültig';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Diese Domain ist in der Lizenz nicht eingetragen. Tragen Sie sie im Kundenbereich ein oder buchen Sie zusätzliche Domains.',
    'installationsExhausted' => 'Alle Installationen der Lizenz sind belegt.',
    'domainsPerInstallationExceeded' => 'Diese Installation nutzt mehr Domains, als die Lizenz erlaubt. Buchen Sie zusätzliche Domains je Installation.',
    'other' => 'nicht von der Lizenz gedeckt',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Installationen: %1$d von %2$d belegt';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Kann nicht aktiviert werden: Die Zahl der Installationen dieser Lizenz ist überschritten. Die Lizenz ist schon in allen erlaubten Installationen aktiv (standardmäßig in einer). Wenden Sie sich an %s, um die Zahl der Installationen zu erhöhen.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['bis zu %d Domain je Installation', 'bis zu %d Domains je Installation'];
