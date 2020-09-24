<?php
require_once __DIR__ . '/../core.php';
//require_once __DIR__ . '/../data/cron.php';

$advicePerCountry = json_decode(file_get_contents(__DIR__ . '/../data/downloads/latest.json'), true);

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
        $codes[] = 'Rood';
    } elseif ($countryData['code_orange']) {
        $codes[] = 'Oranje';
    } elseif ($countryData['code_yellow']) {
        $codes[] = 'Geel';
    } elseif ($countryData['code_green']) {
        $codes[] = 'Groen';
    }

    return 'Code: ' . implode(',', $codes) . "<br><small>Klik voor meer informatie</small>";
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Doe voor vertrek.. de corona reis check!</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Doe voor vertrek, de corona reis check">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,400i,700">

    <style>
        h1 {
            font-family: "Poppins", sans-serif;
            font-weight: 200;
            margin-bottom: 45px;
            font-size: 30px;
            text-align: center;
        }

        #visualization {
            width: 1200px;
            margin: 0 auto;
        }

        .bottom-left {
            position: fixed;
            bottom: 5px;
            left: 10px;
        }
        .bottom-left img {
            width: 200px;
        }
    </style>
</head>
<body>
    <h1>Doe voor vertrek.. de corona reis check!</h1>
    <div id='visualization'></div>

    <a class="bottom-left" href="https://falcotravel.com">
        <img src="/assets/media/falco-logo.png"></a>
    </a>

    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    <script type='text/javascript'>google.load('visualization', '1.1', {'packages': ['geochart']});
        google.setOnLoadCallback(drawVisualization);

        function drawVisualization() {
            var data = new google.visualization.DataTable();

            data.addColumn('string', 'Country');
            data.addColumn('number', 'Value');
            data.addColumn({type: 'string', role: 'tooltip'});

            var data = google.visualization.arrayToDataTable([
                ['Country', 'Value', {role: 'tooltip', p:{html:true}}],
                <?php
                foreach ($advicePerCountry as $countryCode => $countryData):
                ?>
                [{v: '<?= $countryCode ?>', f: '<?= e($countryData['name']) ?>'}, <?= getAdviceValue($countryData) ?>, '<?= getAdviceText($countryData)?>'],
                <?php
                endforeach;
                ?>
            ]);

            var options = {
                backgroundColor: {fill: '#FFFFFF', stroke: '#FFFFFF', strokeWidth: 0},
                colorAxis: {
                    minValue: 0,
                    maxValue: 4,
                    colors: ['#F5F0E7', '#7FEB00', '#FFFF00', '#FFA000', '#FF0000']
                },
                legend: 'none',
                datalessRegionColor: '#F5F0E7',
                displayMode: 'regions',
                enableRegionInteractivity: 'true',
                resolution: 'countries',
                sizeAxis: {minValue: 1, maxValue: 1, minSize: 10, maxSize: 10},
                region: '150',
                keepAspectRatio: false,
                width: 1200,
                height: 740,
                magnifyingGlass: {enable: true, zoomFactor: 7.5},
                tooltip: {textStyle: {color: '#444444'}, trigger: 'focus', isHtml: true}
            };

            var chart = new google.visualization.GeoChart(document.getElementById('visualization'));
            chart.draw(data, options);
        }
    </script>
</body>
</html>
