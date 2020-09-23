<?php
use Illuminate\Support\Str;

require_once __DIR__ . '/core.php';

$countries = json_decode(file_get_contents(__DIR__ . '/data/countries.json'), true);

foreach ($countries as $country) {
}
