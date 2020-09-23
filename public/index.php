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
?>

<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,400i,700">
    <title>Corona Reis Check</title>
</head>
<body>

<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
<script type='text/javascript'>google.load('visualization', '1', {'packages': ['geochart']});
    google.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
        var data = new google.visualization.DataTable();

        data.addColumn('string', 'Country');
        data.addColumn('number', 'Value');
        data.addColumn({type: 'string', role: 'tooltip'});

        <?php
        foreach ($advicePerCountry as $countryCode => $countryData):
        ?>
        data.addRows([[{v: '<?= $countryCode ?>', f: '<?= e($countryData['name']) ?>'}, <?= getAdviceValue($countryData) ?>, '']]);
        <?php
        endforeach;
        ?>

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
            region: 'world',
            keepAspectRatio: true,
            width: 1200,
            height: 740,
            tooltip: {textStyle: {color: '#444444'}, trigger: 'focus'}
        };

        var chart = new google.visualization.GeoChart(document.getElementById('visualization'));
        chart.draw(data, options);
    }
</script>
<h1 style="text-align: center;">Corona Reis Check</h1>
<div id='visualization'></div>
<style>
    h1 {
        font-family: "Poppins", sans-serif;
        font-weight: 200;
        margin-bottom: 45px;
        font-size: 30px;
    }

    #visualization {
        width: 1200px;
        margin: 0 auto;
    }
</style>
</body>
</html>
