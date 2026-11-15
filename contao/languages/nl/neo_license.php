<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licenties';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Licentiesleutel', 'De sleutel uit de orderbevestiging. Maak het veld leeg en sla op om de licentie van deze website te verwijderen.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Nu controleren';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Bezig met controleren …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Laatst gecontroleerd: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'De licentiedienst was bij de laatste controle niet bereikbaar. Nieuwe poging automatisch binnen een uur of met „Nu controleren”.';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Niet gedekt door de licentie: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'De licentiesleutel staat in de orderbevestiging. Om een licentie van deze website te verwijderen, maakt u het veld leeg en slaat u op.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Licentiesleutel kopen in de winkel';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Hogere editie of verlenging in de winkel';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licentie %1$s geldig tot %2$s voor %3$s.',
    'ok_ending' => 'Licentie %1$s opgezegd, geldig tot %2$s voor %3$s.',
    'ok_grace' => 'Licentie %1$s verlopen op %2$s. Zonder verlenging blijft ze geldig tot %4$s.',
    'ok_unlimited' => 'Licentie %1$s onbeperkt geldig voor %3$s.',
    'no_key' => 'Geen licentie.',
    'invalid_key' => 'Deze licentiesleutel is niet geldig voor deze extensie.',
    'no_certificate' => 'De licentiesleutel is opgeslagen, maar nog niet bevestigd.',
    'unverifiable' => 'Het certificaat van de licentiedienst kan met deze versie van de extensie niet worden gecontroleerd.',
    'wrong_module' => 'Het certificaat hoort bij een andere extensie.',
    'domain' => 'De licentie geldt niet voor dit domein. Gelicentieerd: %s.',
    'expired' => 'De licentie is verlopen.',
    'revoked' => 'De licentie is ingetrokken.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'geldig';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Dit domein staat niet in de licentie. Voeg het toe in de klantenomgeving of bestel extra domeinen.',
    'installationsExhausted' => 'Alle installaties van de licentie zijn in gebruik.',
    'domainsPerInstallationExceeded' => 'Deze installatie gebruikt meer domeinen dan de licentie toestaat. Bestel extra domeinen per installatie.',
    'other' => 'niet gedekt door de licentie',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Installaties: %1$d van %2$d in gebruik';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Kan niet worden geactiveerd: het aantal installaties van deze licentie is overschreden. De licentie is al actief in alle toegestane installaties (standaard één). Neem contact op met %s om het aantal installaties te verhogen.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['tot %d domein per installatie', 'tot %d domeinen per installatie'];
