<?php
use Carbon\Carbon;

require_once __DIR__ . '/../core.php';

$advicePerCountry = json_decode(file_get_contents(__DIR__ . '/../data/downloads/latest.json'), true);
$lastUpdatedAt = Carbon::parse(filemtime(__DIR__ . '/../data/downloads/latest.json'));
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            overflow: hidden;
        }
        h1 {
            font-family: "Poppins", sans-serif;
            font-weight: 200;
            margin-top: 30px;
            margin-bottom: 20px;
            font-size: 30px;
            text-align: center;
        }
        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: 200;
            font-size: 13px;
            text-align: center;
        }

        .region-buttons {
            text-align: center;
        }

        #map-canvas {
            width: 100%;
            margin: 0 auto;
            margin-top: 25px;
            margin-bottom: 25px;
        }
        #map-canvas small {
            white-space: nowrap;
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
    <div class="container">
        <h1>Doe voor vertrek.. de corona reis check!</h1>

        <div class="region-buttons">
            <button data-region="150" class="btn btn-sm btn-primary">Europa</button>
            <button data-region="world" class="btn btn-sm btn-secondary">Wereldwijd</button>
        </div>

        <div id="map-canvas"></div>

        <h2>Bron: <a href="https://www.nederlandwereldwijd.nl" target="_blank">Nederland Wereldwijd</a>, laatst bijgewerkt: <?= $lastUpdatedAt->diffForHumans() ?></h2>
    </div>

    <a class="bottom-left" href="https://falcotravel.com" target="_blank">
        <img src="/assets/media/falco-logo.png"></a>
    </a>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    <script type='text/javascript'>
        google.load('visualization', '1.1', {'packages': ['geochart']});
        google.setOnLoadCallback(drawVisualization);

        var region = '150';

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
                region: region,
                keepAspectRatio: true,
                magnifyingGlass: {enable: true, zoomFactor: 7.5},
                tooltip: {textStyle: {color: '#444444'}, trigger: 'focus', isHtml: true}
            };

            var chart = new google.visualization.GeoChart(document.getElementById('map-canvas'));
            chart.draw(data, options);
        }

        $(".region-buttons button").click(function() {
            region = $(this).data('region');
            drawVisualization();
        });

        var resizeTimer;
        $(window).resize(function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                $("#map-canvas").html("");
                drawVisualization();
            }, 200);
        });
    </script>
</body>
</html>
