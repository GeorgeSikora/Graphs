<?php

// INSERT INTO `visits` (`id`, `date`) VALUES (NULL, "2002-01-22");

$interval = '1 month';

if (isset($_GET['interval'])) $interval = $_GET['interval'];

$now = date('Y-m-d');
$from = date('Y-m-d', strtotime($now.' - '.$interval));
$to = $now;

//echo $from." ... ".$to; return;

if (isset($_GET['from']))   $from   = $_GET['from'];
if (isset($_GET['to']))     $to     = $_GET['to'];

$mysqli = new mysqli("localhost", "root", "", "graphs");

$sql = "
SELECT DATE(ADDDATE('$from', INTERVAL @i:=@i+1 DAY)) AS 'date', (
	SELECT COUNT(1)
    FROM visits
    WHERE DATE(ADDDATE('$from', INTERVAL @i:=@i DAY)) = date
) as 'totalVisits' 
FROM visits 
HAVING @i < DATEDIFF('$to', '$from') 
ORDER BY `date`
";
/*

$sql = "
SELECT DATE(ADDDATE('2021-06-16', INTERVAL @i:=@i+1 DAY)) AS 'date', (
	SELECT COUNT(1)
    FROM visits
    WHERE DATE(ADDDATE('2021-06-16', INTERVAL @i:=@i DAY)) = date
) as 'totalVisits' 
FROM visits  
HAVING @i < DATEDIFF('2021-07-16', '2021-06-16')  
ORDER BY `date`";
*/

echo $sql; return;  

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

echo json_encode(["labels" => $labels, "datasets" => $datasets]);