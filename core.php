<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

$config = require_once __DIR__ . '/config.php';
$debugMode = /*$_GET['debug_mode'] ?? */$config['debug_mode'] ?? false;
if ($debugMode) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

setlocale(LC_TIME, 'nl_NL');
\Carbon\Carbon::setLocale('nl');
