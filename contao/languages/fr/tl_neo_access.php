<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Toutes les extensions';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'Connexion IA';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Route', 'Fournisseur par lequel les extensions accèdent au modèle de langage.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, route UE (traitement dans l’UE)',
    'requesty_global' => 'Requesty, route standard (monde entier)',
    'openai' => 'OpenAI (direct)',
    'custom' => 'Adresse personnalisée (compatible OpenAI)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Adresse', 'Adresse de votre propre interface, par exemple https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Clé', 'Enregistrée chiffrée et plus affichée. Laissez vide pour conserver la clé enregistrée.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Clé enregistrée (chiffrée). Adresse : %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Aucune clé enregistrée pour l’instant.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'La route « Adresse personnalisée » nécessite une adresse.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Tester la connexion', 'Tester la connexion'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Tester la connexion', 'Tester la connexion'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Chaque extension choisit le modèle dans ses propres paramètres.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Adresse interrogée';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'Connexion opérationnelle : HTTP %d en %d ms, %d modèles disponibles.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'La route répond avec HTTP %d (après %d ms). Réponse :';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Pas de connexion : %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Enregistré.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Pas encore enregistré.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'Non enregistré. Nécessaire uniquement pour certaines fonctions.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'L’enregistrer maintenant sous « Accès et licences »';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Clé', 'Clé 1 ou 2 de la ressource (portail Azure → Clés et point de terminaison). Enregistrée chiffrée ; laissez vide pour la conserver.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Traitement', 'Où Azure traduit les textes.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Europe (France ou Europe de l’Ouest)',
    'global' => 'Monde (centre de données le plus proche)',
    'nam' => 'Amériques',
    'apc' => 'Asie-Pacifique',
    'custom' => 'Adresse propre de la ressource',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Adresse de la ressource', 'L’adresse du portail Azure, par exemple https://name.cognitiveservices.azure.com/translator/text/v3.0. Nécessaire avec un accès réseau restreint ou pour un traitement en Suisse.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Région', 'L’emplacement de la ressource, par exemple westeurope. Laisser vide pour une ressource globale.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Tester la connexion', 'Tester la connexion'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Adresse interrogée';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Connexion correcte : HTTP %d en %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure répond avec HTTP %d (après %d ms). Réponse :';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Pas de connexion : %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Clé enregistrée (chiffrée). Adresse : %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Aucune clé enregistrée pour l’instant.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'Le traitement « Adresse propre de la ressource » nécessite une adresse.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Aucune clé enregistrée. Nécessaire uniquement pour les traductions.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Tester la connexion', 'Tester la connexion'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Clé', 'La clé d’authentification de votre compte DeepL API (Compte → Clés API). Enregistrée chiffrée ; laissez vide pour la conserver.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Clé enregistrée (chiffrée). Interface : %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Aucune clé enregistrée. Nécessaire uniquement pour les traductions.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Aucune clé enregistrée.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free ou Pro découle de la clé : les clés de l’offre gratuite se terminent par « :fx ».';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Tester la connexion', 'Tester la connexion'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Tester la connexion', 'Tester la connexion'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Adresse interrogée';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Connexion OK : HTTP %1$d en %2$d ms. Utilisés pendant cette période de facturation : %3$s caractères sur %4$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Connexion correcte : HTTP %d en %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL répond avec HTTP %d (après %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'La clé n’est pas acceptée.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'Les caractères de cette période de facturation sont épuisés.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Pas de connexion : %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Utilisé par %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Test en cours …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Peut être testé dès que l’accès est enregistré.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'Cela n’a pas fonctionné. Veuillez recharger la page et réessayer.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'La page n’est plus à jour. Veuillez la recharger et réessayer.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Conditions des extensions';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · édition %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · sans licence';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'version %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'nouveau';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'modifié';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'afficher le texte';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Afficher la version allemande';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Pas encore confirmé.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'Les conditions ont changé. Veuillez confirmer à nouveau les passages marqués.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Confirmé par %1$s le %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Traduction pour la compréhension. Seule la version allemande des conditions de licence fait foi.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Ces passages font partie des conditions de licence de l\'extension (fichier LICENSE). De nouveaux passages apparaissent ici lorsque vous prenez la licence d\'une édition supérieure ou que les conditions changent.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'J\'ai lu les conditions et je les accepte.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Confirmer';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Veuillez d\'abord cocher la case pour confirmer les conditions.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Conditions de %s confirmées.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Confirmer maintenant';
