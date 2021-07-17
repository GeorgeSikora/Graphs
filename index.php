<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphs</title>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.1/chart.min.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script src="functions.js"></script>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background: #1D1F20; /* #1D1F20 */
            padding: 16px;
        }

        .chartGraph {
            position: relative;
        }

        .chartGraph canvas {
            border: 1px dotted #888; /* #886600 */
            width: 100%;
            height: 100%;
        }
        
        .chartGraph .overlay {
            position: absolute;
            top: 50%;
            width: 100%;
            text-align: center;
            transform: translateY(-50%);
            font-size: 32px;
            color: #444;
        }

        .chartGraph .overlay.error {
            color: #c44;
        }

    </style>
</head>
<body>


    <div style="display: flex">
        <div style="width: 50%">
            <?=createGraph("http://localhost/graphs/graphPageVisits.php?interval=1month")?>
        </div>
        <div style="width: 50%">
            <?=createGraph("http://localhost/graphs/graphPageVisits.php?interval=1week")?>
        </div>
    </div>
    
    <div style="display: flex">
        <div style="width: 33.33%">
            <?=createGraph("http://localhost/graphs/graphPageVisits.php?show=browser")?>
        </div>
        <div style="width: 33.33%">
            <?=createGraph("http://localhost/graphs/graphPageVisits.php?show=platform")?>
        </div>
        <div style="width: 33.33%">
            <?=createGraph("http://localhost/graphs/graphPageVisits.php?show=method")?>
        </div>
    </div>

</body>
</html>

<?php

function createGraph($graphUrl) {
    $graphId = uniqid();
    echo "<div class='chartGraph' id='$graphId'><canvas></canvas><div class='overlay'></div></div>";
    echo "<script>buildGraphFromUrl('$graphId','$graphUrl')</script>";
}

?>