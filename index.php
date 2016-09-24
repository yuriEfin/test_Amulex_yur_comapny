<?php
use \app;

function __autoload($class)
{
    require __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
}
spl_autoload_register('__autoload');

defined('LEVEL_ERROR') or define('LEVEL_ERROR', true);

if (LEVEL_ERROR) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}

// instance app
$pathApp = __DIR__ . '/app/App.php';
require $pathApp;

// config file
$config = require __DIR__ . '/app/config/config.php';
app\App::createApp($config)->run();
