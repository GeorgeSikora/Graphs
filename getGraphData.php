<?php

// INSERT INTO `visits` (`id`, `date`) VALUES (NULL, "2002-01-22");

$interval = '1month';

if (isset($_GET['interval'])) $interval = $_GET['interval'];

$now = date('Y-m-d');
$from = date('Y-m-d', strtotime($now.' - '.$interval));
$to = $now;

//echo $from." ... ".$to; return;

if (isset($_GET['from'])) 
{
    $from = $_GET['from'];
}

if (isset($_GET['to'])) 
{
    $to = $_GET['to'];
    $interval = null;
}

$mysqli = new mysqli("localhost", "root", "", "graphs");

$sql = "
    SELECT date, COUNT(1) as 'totalVisits' 
    FROM visits 
    WHERE date BETWEEN '$from' AND '$to'
    GROUP BY date
";

/*
$sql = "
    SET @i = -1;
    SELECT DATE(ADDDATE('$from', INTERVAL @i:=@i+1 DAY)) AS 'date', (
        SELECT COUNT(1)
        FROM visits
        WHERE DATE(ADDDATE('$from', INTERVAL @i:=@i DAY)) = date
    ) as 'totalVisits' 
    FROM visits 
    HAVING @i < DATEDIFF('$to', '$from') 
    ORDER BY `date`";
*/

//echo $sql; return;  

$result = $mysqli -> query($sql);

$labels = [];
$totalVisits = [];

while ($row = $result -> fetch_assoc()) {
    array_push($labels, $row['date']);
    array_push($totalVisits, $row['totalVisits']);
}

$datasets = [
    [
        "label" => "Celkový počet návštěv",
        "backgroundColor" => "yellow",
        "borderColor" => "orange",
        "lineTension" => 0.3, // 0 - 0.3
        "fill" => false,
        "data" => $totalVisits,
    ],
];

$graphData = [
    "labels" => $labels, 
    "datasets" => $datasets,
];

if ($interval)
{
    $graphName = "Celkový počet návštěv předešlých $interval";
}
else 
{
    $graphName = "Celkový počet návštěv od $from do $to";
}

echo json_encode(["graphData" => $graphData, "graphName" => $graphName]);