<?php
use Illuminate\Support\Str;

require_once __DIR__ . '/../core.php';
require_once __DIR__ . '/functions.php';

ini_set('max_execution_time', 600);

$countryList = json_decode(file_get_contents(__DIR__ . '/countries.json'), true);

$dataPerCountry = [];
if (file_exists(__DIR__ . '/downloads/latest.json')) {
    $dataPerCountry = json_decode(file_get_contents(__DIR__ . '/downloads/latest.json'), true);
}

foreach ($countryList as $country) {
    if (isset($country['exclude']) || isset($dataPerCountry[$country['iso2_code']])) {
        continue;
    }

    $countryData = [
        'name' => $country['name'],
        'iso2_code' => $country['iso2_code'],
        'slug' => $country['slug'] ?? Str::slug($country['name']),
        'code_red' => false,
        'code_orange' => false,
        'code_yellow' => false,
        'code_green' => false
    ];

    if (isset($country['dutch_caribbean'])) {
        extractDutchCaribbeanData($countryData);
    } else {
        extractWorldCountryData($countryData);
    }

    $dataPerCountry[$country['iso2_code']] = $countryData;
    file_put_contents(__DIR__ . '/downloads/latest.json', json_encode($dataPerCountry));

    // give the remote server some time to relax and enjoy this beautiful day
    sleep(0.5);
}
