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

    <script src="functions.js"></script>
    <style>
        body {  
            background: #1D1F20;
            padding: 16px;
        }
        canvas {
            border: 1px dotted #886600;
        }
    </style>
</head>
<body>

<div style="display: flex">
    <div style="width: 50%">
        <?=createGraph("http://localhost/graphs/getGraphData.php?from=1990-01-01&to=2099-01-01")?>
    </div>
    <div style="width: 50%">
        <?=createGraph("http://localhost/graphs/getGraphData.php")?>
    </div>
</div>
    
</body>
</html>

<?php

function createGraph($graphUrl) {
    $graphId = uniqid();
    echo "<canvas id='$graphId'></canvas>";
    echo "<script>buildGraphFromUrl('$graphId','$graphUrl')</script>";
}

?>