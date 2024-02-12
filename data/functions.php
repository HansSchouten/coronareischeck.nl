<?php
use League\ColorExtractor\Palette;
use League\ColorExtractor\Color;

function extractWorldCountryData(&$countryData)
{
    $countryUrl = 'https://www.nederlandwereldwijd.nl/reisadvies/' . $countryData['slug'];
    $countryData['full_url'] = $countryUrl;
    $countryInfo = file_get_contents($countryUrl);

    if (strpos($countryInfo, 'opendata.nederlandwereldwijd.nl') === false) {
        return;
    }

    $imageTagMatches = [];
    preg_match('~<img class="land_countryMap__wvBpL"(.+?)/>~', $countryInfo, $imageTagMatches);
    if (empty($imageTagMatches)) {
        echo $countryData['name'] . " not found\n";
        return false;
    }

    $imageTagMatch = $imageTagMatches[0];
    $imageSrc = [];
    preg_match('~src="(.+?)"~', $imageTagMatch, $imageSrc);
    if (empty($imageSrc)) {
        echo $countryData['name'] . " not found\n";
        return false;
    }

    $imageSrc = $imageSrc[1];
    if (strpos($imageSrc, 'https://opendata.nederlandwereldwijd.nl') !== 0 || strpos($imageSrc, '.png') === false) {
        echo $countryData['name'] . " not found\n";
        return false;
    }

    $adviceImageUrl = $imageSrc;
    extractTravelAdvice($countryData, $adviceImageUrl);
    return true;
}

function extractTravelAdvice(&$countryData, $adviceImageUrl)
{
    $countryData['advice_image_url'] = $adviceImageUrl;
    $adviceImagePalette = Palette::fromFilename($adviceImageUrl);
    foreach ($adviceImagePalette as $intColor => $count) {
        if ($count < 300) break;

        switch (Color::fromIntToHex($intColor)) {
            case '#FF0000':
                $countryData['code_red'] = true;
                break;
            case '#FFA000':
                $countryData['code_orange'] = true;
                break;
            case '#FFFF00':
                $countryData['code_yellow'] = true;
                break;
            case '#7FEB00':
                $countryData['code_green'] = true;
                break;
        }
    }
}
