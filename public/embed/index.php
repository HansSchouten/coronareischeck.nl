<?php
use Carbon\Carbon;

require_once __DIR__ . '/../../core.php';

$advicePerCountry = json_decode(file_get_contents(__DIR__ . '/../../data/downloads/latest.json'), true);
$lastUpdatedAt = Carbon::parse(filemtime(__DIR__ . '/../../data/downloads/latest.json'));
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,400i,700">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            overflow: hidden;
        }
        h1 {
            font-family: "Poppins", sans-serif;
            font-weight: 200;
            margin-top: 40px;
            margin-bottom: 35px;
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
            padding-top: 5px;
            text-align: center;
        }

        #map-canvas {
            position: relative;
            width: 100%;
            height: 100%;
            margin: 0;
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
            width: 100px;
        }
        .bottom-right {
            position: absolute;
            bottom: 5px;
            right: 3px;
            z-index: 1;
        }
        .bottom-right img {
            width: 80px;
        }

        .badge-red {
            background: #FF0000;
            color: #fff;
        }
        .badge-orange {
            background: #FFA000;
            color: #fff;
        }
        .badge-yellow {
            background: #FFFF00;
            color: #333;
        }
        .badge-green {
            background: #7FEB00;
            color: #fff;
        }
    </style>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MVCNMFR');
    </script>
    <!-- End Google Tag Manager -->
</head>
<body>
<div class="container">
    <div id="map-canvas" class="d-block"></div>

    <div class="region-buttons d-none">
        <button data-region="150" class="btn btn-sm btn-primary">Europa</button>
        <button data-region="world" class="btn btn-sm btn-primary">Wereldwijd</button>
    </div>

    <div class="hidden d-none">
        <a class="bottom-right" href="https://www.falcotravel.com/nl/blog/corona-reis-check" target="_blank">
            <img src="/assets/media/falco-logo.png"></a>
        </a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
<script type='text/javascript'>
    google.load('visualization', '1.1', {'packages': ['geochart']});
    google.setOnLoadCallback(drawVisualization);

    var region = '150';
    var dataPerCountry = <?= json_encode($advicePerCountry) ?>;

    function drawVisualization() {
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

        google.visualization.events.addListener(chart, 'select', function() {
            var selection = chart.getSelection();
            if (selection.length === 1) {
                var selectedRow = selection[0].row;
                var selectedCountryCode = data.getValue(selectedRow, 0);
                var countryData = dataPerCountry[selectedCountryCode];
                window.open(countryData['full_url'], '_blank');
            }
        });
        google.visualization.events.addListener(chart, 'ready', function() {
            $("#map-canvas").append($(".bottom-right"));
            $(".region-buttons").removeClass("d-none");
            $(".bottom-right").removeClass("d-none");
        });
    }

    function getColorCodeNames(countryData) {
        var codeNames = [];
        if (countryData['code_red']) {
            codeNames.push('<b class="badge badge-red">rood</b>');
        }
        if (countryData['code_orange']) {
            codeNames.push('<b class="badge badge-orange">oranje</b>');
        }
        if (countryData['code_yellow']) {
            codeNames.push('<b class="badge badge-yellow">geel</b>');
        }
        if (countryData['code_groen']) {
            codeNames.push('<b class="badge badge-green">groen</b>');
        }
        if (codeNames.length === 0) {
            return 'onbekend';
        }
        if (codeNames.length === 2) {
            return codeNames.join(' en ');
        }
        return codeNames.join(', ');
    }

    $(".region-buttons button").click(function() {
        $("body .hidden").append($(".bottom-right"));
        $("#map-canvas").html("");
        region = $(this).data('region');
        drawVisualization();
    });

    var resizeTimer;
    $(window).resize(function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            $("body .hidden").append($(".bottom-right"));
            $("#map-canvas").html("");
            drawVisualization();
        }, 200);
    });
</script>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MVCNMFR"
            height="0" width="0" style="display:none;visibility:hidden">
    </iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
</body>
</html>
