<?php

// 1month, 3year, 10days, 2week
function timeIntervalToCzech($interval) {

    $splitted = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$interval);
    $number = $splitted[0];
    $timeUnit = $splitted[1];
    
    $phrase  = "You should eat fruits, vegetables, and fiber every day.";
    $healthy = ["fruits", "vegetables", "fiber"];
    $yummy   = ["pizza", "beer", "ice cream"];

    $newphrase = str_replace($healthy, $yummy, $phrase);
}

// INSERT INTO `visits` (`id`, `date`) VALUES (NULL, "2002-01-22");

$dateColName = 'dateCreated';
$interval = '1month';

if (isset($_GET['interval'])) 
{
    $interval = $_GET['interval'];
}

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

$dateInterval = "days";
$groupBy = "YEAR($dateColName), MONTH($dateColName), DAY($dateColName)"; // MONTH(date)

$fromDate = date_create($from);
$toDate = date_create($to);
$diff = date_diff($fromDate, $toDate);
$daysDiff = $diff->days;
$monthsDiff = round($daysDiff * 0.032855);
$yearsDiff = round($daysDiff * 0.002738);

//echo $daysDiff . ' ... ' . $monthsDiff . ' ... ' . $yearsDiff; return;

if ($yearsDiff >= 1) 
{
    $dateInterval = "months";
    $groupBy = "YEAR($dateColName), MONTH($dateColName)";
}
else if ($monthsDiff >= 3) 
{
    $dateInterval = "weeks";
    $groupBy = "YEAR($dateColName), MONTH($dateColName), WEEK($dateColName)";
} 

//$mysqli = new mysqli("localhost", "root", "", "graphs");
$mysqli = new mysqli("185.221.124.205", "janek", "kokos", "sajkoradb");

$sql = "
    SELECT $dateColName, COUNT(1) as 'totalVisits' 
    FROM users 
    WHERE $dateColName BETWEEN '$from' AND '$to'
    GROUP BY $groupBy
    ORDER BY $dateColName
";

//echo $sql; return;  

$result = $mysqli -> query($sql);

$labels = [];
$totalVisits = [];

while ($row = $result -> fetch_assoc()) {
    array_push($labels, $row[$dateColName]);
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
    $graphName = "Celkový počet návštěv $interval";
}
else 
{
    $graphName = "Celkový počet návštěv od $from do $to";
}

$graphName .= " uskupeno odstup $dateInterval";

echo json_encode(["graphData" => $graphData, "graphName" => $graphName]);