<?php
// Built-in server: files under public/ as they are, everything else through Contao.
$file = __DIR__.'/public'.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ('/' !== substr($file, -1) && is_file($file) && !str_ends_with($file, '.php')) {
    return false;
}
if (str_starts_with(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/preview.php')) {
    $_SERVER['SCRIPT_FILENAME'] = __DIR__.'/public/preview.php';
    $_SERVER['SCRIPT_NAME'] = '/preview.php';
    require __DIR__.'/public/preview.php';
    return;
}
$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__.'/public/index.php';
