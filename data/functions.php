<?php
use League\ColorExtractor\Palette;
use League\ColorExtractor\Color;

function extractWorldCountryData(&$countryData)
{
    $countryUrl = 'https://www.nederlandwereldwijd.nl/reisadvies/' . $countryData['slug'];
    $countryData['full_url'] = $countryUrl;
    $countryInfo = file_get_contents($countryUrl);

    if (strpos($countryInfo, '/content-afbeeldingen/reisadviezen/') === false) {
        return;
    }

    $urlMatches = [];
    preg_match('~binaries/content/gallery/nederlandwereldwijd/content-afbeeldingen/reisadviezen/(.+).png~', $countryInfo, $urlMatches);
    if (empty($urlMatches)) {
        echo $countryData['name'] . " not found\n";
        return false;
    }
    $imagePath = explode('.png', $urlMatches[0])[0] . '.png';
    $adviceImageUrl = 'https://opendata.nederlandwereldwijd.nl/' . $imagePath;

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
