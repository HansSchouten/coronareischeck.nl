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

// special extraction method for Dutch Caribbean, because f*ck consistency
function extractDutchCaribbeanData(&$countryData)
{
    $url = 'https://www.nederlandwereldwijd.nl/reizen/reisadvies-caribisch-deel-van-het-koninkrijk';
    $countryData['full_url'] = $url;
    $caribbeanInfo = file_get_contents($url);

    // combine Bonaire, Sint Eustatius and Saba from separate images
    if ($countryData['iso2_code'] === 'BQ') {
        $islandSlugs = ['bonaire', 'saba', 'sint20eustatius'];
        foreach ($islandSlugs as $islandSlug) {
            $urlMatches = [];
            preg_match('~binaries/medium/content/gallery/nederlandwereldwijd/content-afbeeldingen/reisadviezen/caribisch-nederland/reisadvies_' . $islandSlug . '(.+).png~', $caribbeanInfo, $urlMatches);
            $adviceImageUrl = 'https://www.nederlandwereldwijd.nl/' . $urlMatches[0];

            extractTravelAdvice($countryData, $adviceImageUrl);
        }
        return;
    }

    $urlMatches = [];
    preg_match('~binaries/medium/content/gallery/nederlandwereldwijd/content-afbeeldingen/reisadviezen/caribisch-nederland/reisadvies_' . $countryData['slug'] . '(.+).png~', $caribbeanInfo, $urlMatches);
    $adviceImageUrl = 'https://www.nederlandwereldwijd.nl/' . $urlMatches[0];

    extractTravelAdvice($countryData, $adviceImageUrl);
}

function extractTravelAdvice(&$countryData, $adviceImageUrl)
{
    $countryData['advice_image_url'] = $adviceImageUrl;
    $adviceImagePalette = Palette::fromFilename($adviceImageUrl);
    foreach ($adviceImagePalette as $intColor => $count) {
        if ($count < 200) break;

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
