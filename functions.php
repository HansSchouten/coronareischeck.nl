<?php

function dd($var)
{
    print_r($var);
    exit();
}

function getAdviceValue($countryData): int
{
    if ($countryData['code_red']) {
        return 4;
    } elseif ($countryData['code_orange']) {
        return 3;
    } elseif ($countryData['code_yellow']) {
        return 2;
    } elseif ($countryData['code_green']) {
        return 1;
    }
    return 0;
}

function getAdviceText($countryData): string
{
    $codes = [];
    if ($countryData['code_red']) {
        $codes[] = 'rood';
    }
    if ($countryData['code_orange']) {
        $codes[] = 'oranje';
    }
    if ($countryData['code_yellow']) {
        $codes[] = 'geel';
    }
    if ($countryData['code_green']) {
        $codes[] = 'groen';
    }
    $codesText = implode('/', $codes);

    return 'Code: ' . $codesText . "<br><small>Klik voor meer informatie</small>";
}

function getAdviceBadges($countryData): string
{
    $badges = [];
    if ($countryData['code_red']) {
        $badges[] = '<b class="badge badge-red">rood</b>';
    }
    if ($countryData['code_orange']) {
        $badges[] = '<b class="badge badge-orange">oranje</b>';
    }
    if ($countryData['code_yellow']) {
        $badges[] = '<b class="badge badge-yellow">geel</b>';
    }
    if ($countryData['code_green']) {
        $badges[] = '<b class="badge badge-green">groen</b>';
    }
    if (sizeof($badges) === 0) {
        return 'onbekend';
    } elseif (sizeof($badges) === 2) {
        return implode(' en ', $badges);
    }
    return implode(', ', $badges);
}
