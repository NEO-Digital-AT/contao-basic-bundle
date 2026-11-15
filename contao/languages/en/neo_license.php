<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licences';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Licence key', 'The key from the order confirmation. Empty the field and save to remove the licence from this website.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Check now';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Checking …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Last checked: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'The licence service could not be reached at the last check. It is tried again automatically within an hour, or with "Check now".';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Not covered by the licence: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'The licence key is in the order confirmation. To remove a licence from this website, empty its field and save.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Buy a licence key in the store';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Higher edition or renewal in the store';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licence %1$s valid until %2$s for %3$s.',
    'ok_ending' => 'Licence %1$s cancelled, valid until %2$s for %3$s.',
    'ok_grace' => 'Licence %1$s expired on %2$s. Without renewal it remains valid until %4$s.',
    'ok_unlimited' => 'Licence %1$s, unlimited, for %3$s.',
    'no_key' => 'No licence.',
    'invalid_key' => 'This licence key is not valid for this extension.',
    'no_certificate' => 'The licence key is stored but not confirmed yet.',
    'unverifiable' => 'The certificate of the licence service cannot be checked with this version of the extension.',
    'wrong_module' => 'The certificate belongs to another extension.',
    'domain' => 'The licence does not cover this domain. Licensed: %s.',
    'expired' => 'The licence has expired.',
    'revoked' => 'The licence has been withdrawn.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'valid';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'This domain is not entered in the licence. Enter it in the customer area or book additional domains.',
    'installationsExhausted' => 'All installations of the licence are in use.',
    'domainsPerInstallationExceeded' => 'This installation uses more domains than the licence allows. Book additional domains per installation.',
    'other' => 'not covered by the licence',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Installations: %1$d of %2$d in use';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Cannot be activated: the number of installations of this licence has been exceeded. The licence is already active in all installations it allows (one by default). Contact %s to raise the number of installations.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['up to %d domain per installation', 'up to %d domains per installation'];
