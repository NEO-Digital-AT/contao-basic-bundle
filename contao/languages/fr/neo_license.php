<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licences';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Clé de licence', 'La clé figurant dans la confirmation de commande. Videz le champ et enregistrez pour retirer la licence de ce site.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Vérifier maintenant';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Vérification en cours …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Dernière vérification : %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'Le service de licences était injoignable lors de la dernière vérification. Nouvel essai automatique dans l’heure ou avec « Vérifier maintenant ».';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'Non couvert par la licence : %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'La clé de licence figure dans la confirmation de commande. Pour retirer une licence de ce site, videz son champ et enregistrez.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Acheter une clé de licence dans la boutique';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Édition supérieure ou renouvellement dans la boutique';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licence %1$s valable jusqu’au %2$s pour %3$s.',
    'ok_ending' => 'Licence %1$s résiliée, valable jusqu’au %2$s pour %3$s.',
    'ok_grace' => 'Licence %1$s expirée le %2$s. Sans renouvellement, elle reste valable jusqu’au %4$s.',
    'ok_unlimited' => 'Licence %1$s illimitée pour %3$s.',
    'no_key' => 'Sans licence.',
    'invalid_key' => 'Cette clé de licence n’est pas valable pour cette extension.',
    'no_certificate' => 'La clé de licence est enregistrée, mais pas encore confirmée.',
    'unverifiable' => 'Le certificat du service de licences ne peut pas être vérifié avec cette version de l’extension.',
    'wrong_module' => 'Le certificat appartient à une autre extension.',
    'domain' => 'La licence ne couvre pas ce domaine. Domaines sous licence : %s.',
    'expired' => 'La licence a expiré.',
    'revoked' => 'La licence a été retirée.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'valide';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Ce domaine n’est pas enregistré dans la licence. Enregistrez-le dans l’espace client ou commandez des domaines supplémentaires.',
    'installationsExhausted' => 'Toutes les installations de la licence sont utilisées.',
    'domainsPerInstallationExceeded' => 'Cette installation utilise plus de domaines que la licence ne le permet. Commandez des domaines supplémentaires par installation.',
    'other' => 'non couvert par la licence',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Installations : %1$d sur %2$d utilisées';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'Activation impossible : le nombre d’installations de cette licence est dépassé. La licence est déjà active sur toutes les installations autorisées (une par défaut). Contactez %s pour augmenter le nombre d’installations.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['jusqu’à %d domaine par installation', 'jusqu’à %d domaines par installation'];
