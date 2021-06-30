<?php
use League\ColorExtractor\Palette;
use League\ColorExtractor\Color;

function extractWorldCountryData(&$countryData)
{
    $countryUrl = 'https://www.nederlandwereldwijd.nl/landen/' . $countryData['slug'] . '/reizen/reisadvies';
    $countryData['full_url'] = $countryUrl;
    $countryInfo = file_get_contents($countryUrl);

    if (strpos($countryInfo, '/content-afbeeldingen/reisadviezen/') === false) {
        return;
    }

    $urlMatches = [];
    preg_match('~binaries/large/content/gallery/nederlandwereldwijd/content-afbeeldingen/reisadviezen/(.+).png~', $countryInfo, $urlMatches);
    $adviceImageUrl = 'https://www.nederlandwereldwijd.nl/' . $urlMatches[0];

    extractTravelAdvice($countryData, $adviceImageUrl);
}

function extractTravelAdvice(&$countryData, $adviceImageUrl)
{
    $countryData['advice_image_url'] = $adviceImageUrl;
    $adviceImagePalette = Palette::fromFilename($adviceImageUrl);
    foreach ($adviceImagePalette as $intColor => $count) {
        if ($count < 400) break;

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
