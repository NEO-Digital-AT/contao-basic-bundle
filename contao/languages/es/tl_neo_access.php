<?php

declare(strict_types=1);

// Breadcrumb of the one record of the page.
$GLOBALS['TL_LANG']['tl_neo_access']['edit'] = 'Todas las extensiones';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_legend_name'] = 'Conexión de IA';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_route'] = ['Ruta', 'Proveedor a través del cual las extensiones acceden al modelo de lenguaje.'];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_route_options'] = [
    'requesty_eu' => 'Requesty, ruta UE (procesamiento en la UE)',
    'requesty_global' => 'Requesty, ruta estándar (mundial)',
    'openai' => 'OpenAI (directo)',
    'custom' => 'Dirección propia (compatible con OpenAI)',
];

$GLOBALS['TL_LANG']['tl_neo_access']['ai_base_url'] = ['Dirección', 'Dirección de su propia interfaz, por ejemplo https://example.com/v1.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_api_key'] = ['Clave', 'Se guarda cifrada y no se vuelve a mostrar. Dejar vacío conserva la clave guardada.'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_ready'] = 'Clave guardada (cifrada). Dirección: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_key'] = 'Todavía no hay ninguna clave guardada.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_no_url'] = 'La ruta «Dirección propia» necesita una dirección.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test'] = ['Probar conexión', 'Probar conexión'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_ai_test'] = ['Probar conexión', 'Probar conexión'];
$GLOBALS['TL_LANG']['tl_neo_access']['ai_model_note'] = 'Cada extensión elige el modelo en su propia configuración.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_address'] = 'Dirección consultada';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_ok'] = 'La conexión funciona: HTTP %d en %d ms, %d modelos disponibles.';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_http'] = 'La ruta responde con HTTP %d (tras %d ms). Respuesta:';
$GLOBALS['TL_LANG']['tl_neo_access']['ai_test_failed'] = 'Sin conexión: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['section_ready'] = 'Guardado.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_missing'] = 'Todavía no guardado.';
$GLOBALS['TL_LANG']['tl_neo_access']['section_optional'] = 'No guardado. Solo se necesita para algunas funciones.';
$GLOBALS['TL_LANG']['tl_neo_access']['enter_here'] = 'Introducirla ahora en «Accesos y licencias»';

// Azure Translator (Neo\BasicBundle\Translation\AzureConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_legend_name'] = 'Azure Translator';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_key'] = ['Clave', 'Clave 1 o 2 del recurso (portal de Azure → Claves y punto de conexión). Se guarda cifrada; dejarla vacía la conserva.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location'] = ['Procesamiento', 'Dónde traduce Azure los textos.'];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_location_options'] = [
    'eur' => 'Europa (Francia o Europa Occidental)',
    'global' => 'Todo el mundo (centro de datos más cercano)',
    'nam' => 'América',
    'apc' => 'Asia-Pacífico',
    'custom' => 'Dirección propia del recurso',
];

$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_url'] = ['Dirección del recurso', 'La dirección del portal de Azure, por ejemplo https://name.cognitiveservices.azure.com/translator/text/v3.0. Necesaria con acceso de red restringido o para el procesamiento en Suiza.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_region'] = ['Región', 'La ubicación del recurso, por ejemplo westeurope. Dejar vacío para un recurso global.'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test'] = ['Comprobar conexión', 'Comprobar conexión'];
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_address'] = 'Dirección consultada';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_ok'] = 'Conexión correcta: HTTP %d en %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_http'] = 'Azure responde con HTTP %d (tras %d ms). Respuesta:';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_test_failed'] = 'Sin conexión: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_ready'] = 'Clave guardada (cifrada). Dirección: %s';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_key'] = 'Todavía no hay ninguna clave guardada.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_no_url'] = 'El procesamiento «Dirección propia del recurso» necesita una dirección.';
$GLOBALS['TL_LANG']['tl_neo_access']['azure_translator_optional'] = 'Todavía no hay ninguna clave guardada. Solo se necesita para las traducciones.';
$GLOBALS['TL_LANG']['tl_neo_access']['neo_azure_translator_test'] = ['Comprobar conexión', 'Comprobar conexión'];

// DeepL (Neo\BasicBundle\Translation\DeepLConnection)
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_legend_name'] = 'DeepL';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_key'] = ['Clave', 'La clave de autenticación de su cuenta de DeepL API (Cuenta → Claves de API). Se guarda cifrada; dejarla vacía la conserva.'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plans'] = ['free' => 'DeepL API Free', 'pro' => 'DeepL API Pro'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_ready'] = 'Clave guardada (cifrada). Interfaz: %1$s (%2$s)';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_optional'] = 'Todavía no hay ninguna clave guardada. Solo se necesita para las traducciones.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_no_key'] = 'Todavía no hay ninguna clave guardada.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_plan_note'] = 'Free o Pro se deduce de la clave: las claves del plan gratuito terminan en «:fx».';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test'] = ['Probar conexión', 'Probar conexión'];
$GLOBALS['TL_LANG']['tl_neo_access']['neo_deepl_test'] = ['Probar conexión', 'Probar conexión'];
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_address'] = 'Dirección consultada';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok'] = 'Conexión correcta: HTTP %1$d en %2$d ms. Usados en este periodo de facturación: %3$s de %4$s caracteres.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_ok_short'] = 'Conexión correcta: HTTP %d en %d ms.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_http'] = 'DeepL responde con HTTP %d (tras %d ms).';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_403'] = 'La clave no es aceptada.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_456'] = 'Los caracteres de este periodo de facturación se han agotado.';
$GLOBALS['TL_LANG']['tl_neo_access']['deepl_test_failed'] = 'Sin conexión: %s';

// Buttons that run in place (Neo\BasicBundle\Backend\AccessActions)
$GLOBALS['TL_LANG']['tl_neo_access']['used_by'] = 'Lo usa: %s.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_testing'] = 'Probando …';
$GLOBALS['TL_LANG']['tl_neo_access']['action_needs_access'] = 'Se puede probar en cuanto el acceso esté guardado.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_failed'] = 'No ha funcionado. Vuelva a cargar la página e inténtelo de nuevo.';
$GLOBALS['TL_LANG']['tl_neo_access']['action_invalid_token'] = 'La página ya no está actualizada. Vuelva a cargarla e inténtelo de nuevo.';

// Terms of the extensions (Neo\BasicBundle\Terms\TermsFields)
$GLOBALS['TL_LANG']['tl_neo_access']['neo_terms_legend'] = 'Condiciones de las extensiones';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading'] = '%1$s · edición %2$s';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_heading_free'] = '%s · sin licencia';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_version'] = 'versión %d';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_new'] = 'nuevo';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_badge_changed'] = 'modificado';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show'] = 'mostrar texto';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_show_german'] = 'Mostrar la versión alemana';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_open'] = 'Aún no confirmado.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_renew'] = 'Las condiciones han cambiado. Confirme de nuevo los pasajes marcados.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_done'] = 'Confirmado por %1$s el %2$s.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_binding'] = 'Traducción para su comprensión. Solo es vinculante la versión alemana de las condiciones de licencia.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_full'] = 'Estos pasajes forman parte de las condiciones de licencia de la extensión (archivo LICENSE). Aparecerán nuevos pasajes aquí cuando licencie una edición superior o cambien las condiciones.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check'] = 'He leído las condiciones y las acepto.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accept'] = 'Confirmar';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_check_missing'] = 'Marque primero la casilla para confirmar las condiciones.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_accepted'] = 'Condiciones de %s confirmadas.';
$GLOBALS['TL_LANG']['tl_neo_access']['terms_confirm_now'] = 'Confirmar ahora';
