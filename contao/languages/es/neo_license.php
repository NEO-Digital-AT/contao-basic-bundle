<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['neo_license']['legend'] = 'Licencias';
$GLOBALS['TL_LANG']['neo_license']['key'] = ['Clave de licencia', 'La clave de la confirmación del pedido. Vacíe el campo y guarde para retirar la licencia de este sitio web.'];
$GLOBALS['TL_LANG']['neo_license']['refresh'] = 'Comprobar ahora';
$GLOBALS['TL_LANG']['neo_license']['refreshing'] = 'Comprobando …';
$GLOBALS['TL_LANG']['neo_license']['checked_at'] = 'Última comprobación: %s';
$GLOBALS['TL_LANG']['neo_license']['service_unreachable'] = 'El servicio de licencias no estaba disponible en la última comprobación. Nuevo intento automático en una hora o con «Comprobar ahora».';
$GLOBALS['TL_LANG']['neo_license']['uncovered'] = 'No cubierto por la licencia: %s.';
$GLOBALS['TL_LANG']['neo_license']['intro'] = 'La clave de licencia figura en la confirmación del pedido. Para retirar una licencia de este sitio web, vacíe su campo y guarde.';
$GLOBALS['TL_LANG']['neo_license']['store_buy'] = 'Comprar una clave de licencia en la tienda';
$GLOBALS['TL_LANG']['neo_license']['store_manage'] = 'Edición superior o renovación en la tienda';

$GLOBALS['TL_LANG']['neo_license']['editions'] = [
    'basic' => 'Basic',
    'plus' => 'Plus',
    'pro' => 'Pro',
];

$GLOBALS['TL_LANG']['neo_license']['reason'] = [
    'ok' => 'Licencia %1$s válida hasta el %2$s para %3$s.',
    'ok_ending' => 'Licencia %1$s cancelada, válida hasta el %2$s para %3$s.',
    'ok_grace' => 'Licencia %1$s caducada el %2$s. Sin renovación sigue siendo válida hasta el %4$s.',
    'ok_unlimited' => 'Licencia %1$s ilimitada para %3$s.',
    'no_key' => 'Sin licencia.',
    'invalid_key' => 'Esta clave de licencia no es válida para esta extensión.',
    'no_certificate' => 'La clave de licencia está guardada, pero aún no confirmada.',
    'unverifiable' => 'El certificado del servicio de licencias no se puede comprobar con esta versión de la extensión.',
    'wrong_module' => 'El certificado pertenece a otra extensión.',
    'domain' => 'La licencia no cubre este dominio. Con licencia: %s.',
    'expired' => 'La licencia ha caducado.',
    'revoked' => 'La licencia ha sido retirada.',
];

// Licence service with installations: per domain valid or why not, installations and domains per installation.
$GLOBALS['TL_LANG']['neo_license']['domain_valid'] = 'válida';
$GLOBALS['TL_LANG']['neo_license']['domain_reason'] = [
    'notLicensed' => 'Este dominio no está registrado en la licencia. Regístrelo en el área de clientes o contrate dominios adicionales.',
    'installationsExhausted' => 'Todas las instalaciones de la licencia están en uso.',
    'domainsPerInstallationExceeded' => 'Esta instalación utiliza más dominios de los que permite la licencia. Contrate dominios adicionales por instalación.',
    'other' => 'no cubierto por la licencia',
];
$GLOBALS['TL_LANG']['neo_license']['installations'] = 'Instalaciones: %1$d de %2$d en uso';
$GLOBALS['TL_LANG']['neo_license']['installations_exhausted'] = 'No se puede activar: se ha superado el número de instalaciones de esta licencia. La licencia ya está activa en todas las instalaciones permitidas (una por defecto). Póngase en contacto con %s para ampliar el número de instalaciones.';
$GLOBALS['TL_LANG']['neo_license']['domains_per_installation'] = ['hasta %d dominio por instalación', 'hasta %d dominios por instalación'];
