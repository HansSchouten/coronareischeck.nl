<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

$config = require_once __DIR__ . '/config.php';
if ($config['debug_mode']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

setlocale(LC_ALL, 'nl_NL');
