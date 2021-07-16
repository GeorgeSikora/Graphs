<?php

// 1month, 3year, 10days, 2week ...
function timeIntervalToCzech($interval) {

    $splitted = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$interval);
    $timeVal = $splitted[0];
    $timeUnit = $splitted[1];

    if ($timeVal == 1) {
        $czechInterval = "předešlého ";
    } else {
        $czechInterval = "předešlých ";
    }
    
    switch ($timeUnit) {
        case 'day':
            if ($timeVal == 1) {
                $czechInterval .= "dne";
            } else {
                $czechInterval .= "$timeVal dnů";
            }
            return $czechInterval;
        case 'month':
            if ($timeVal == 1) {
                $czechInterval .= "měsíce";
            } else {
                $czechInterval .= "$timeVal měsíců";
            }
            return $czechInterval;
        case 'year':
            if ($timeVal == 1) {
                $czechInterval .= "roku";
            } else {
                $czechInterval .= "$timeVal let";
            }
            return $czechInterval;
    }
    return $interval;
}

// hours, days, weeks, months, years
function intervalNameToCzech($intervalName) {
    switch ($intervalName) {
        case 'hours':   return 'hodinách';
        case 'days':    return 'dnech';
        case 'weeks':   return 'týdnech';
        case 'months':  return 'měsících';
        case 'years':   return 'let';
        default: return $intervalName;
    }
}

class ChartGraph {

    public $dateFormat = 'Y-m-d';

    private $dbConfig;

    public $groupBy;

    public $interval = '1month';
    public $dateScaleName = 'days';
    public $fromDate, $toDate;
   
    function __construct($dbConfig) {

        $this->dbConfig = $dbConfig;

        if (isset($_GET['interval'])) 
        {
            $this->interval = $_GET['interval'];
        }

        $df = $this->dateFormat;
        $now = date($df);
        $interval = $this->interval;
        $this->fromDate = date($df, strtotime("$now - $interval"));
        $this->toDate = $now;

        if (isset($_GET['from'])) 
        {
            $this->fromDate = $_GET['from'];
        }

        if (isset($_GET['to'])) 
        {
            $this->toDate = $_GET['to'];
            $this->interval = null;
        }
        
        $dcn = $this->dbConfig['dateCol'];
        $this->groupBy = "YEAR($dcn), MONTH($dcn), DAY($dcn)"; 

        $DateFrom   = date_create($this->fromDate);
        $DateTo     = date_create($this->toDate);
        $DatesDiff  = date_diff($DateFrom, $DateTo);
        $daysDiff   = $DatesDiff->days;
        $monthsDiff = round($daysDiff * 0.032855);
        $yearsDiff  = round($daysDiff * 0.002738);
        
        if ($yearsDiff >= 1) 
        {
            $this->dateScaleName = "months";
            $this->groupBy = "YEAR($dcn), MONTH($dcn)";
        }
        else if ($monthsDiff >= 3) 
        {
            $this->dateScaleName = "weeks";
            $this->groupBy = "YEAR($dcn), MONTH($dcn), WEEK($dcn)";
        } 
    }

    function getGraphData() {

        $mysqli = new mysqli(
            $this->dbConfig['hostname'], 
            $this->dbConfig['username'],
            $this->dbConfig['password'],
            $this->dbConfig['database']
        );

        $table   = $this->dbConfig['table'];
        $dcn     = $this->dbConfig['dateCol'];
        $from    = $this->fromDate;
        $to      = $this->toDate;
        $groupBy = $this->groupBy;

        $sql = "
            SELECT $dcn, COUNT(1) as 'totalVisits' 
            FROM $table 
            WHERE $dcn BETWEEN '$from' AND '$to'
            GROUP BY $groupBy
            ORDER BY $dcn
        ";

        //echo $sql; return;  

        $result = $mysqli -> query($sql);

        $labels = [];
        $totalVisits = [];

        while ($row = $result -> fetch_assoc()) {
            array_push($labels, $row[$dcn]);
            array_push($totalVisits, $row['totalVisits']);
        }

        $datasets = [
            [
                "label" => "Nových uživatel",
                "backgroundColor" => "lime",
                "borderColor" => "green",
                "lineTension" => 0.3, // 0 - 0.3
                "fill" => false,
                "data" => $totalVisits,
            ],
        ];

        $graphData = [
            "labels" => $labels, 
            "datasets" => $datasets,
        ];

        $graphName = "Celkový počet návštěv ";

        if ($this->interval)
        {
            $czechInterval = timeIntervalToCzech($this->interval);
            $graphName .= $czechInterval;
        }
        else 
        {
            $graphName .= "od $from do $to";
        }

        $dateIntervalCzech = intervalNameToCzech($this->dateScaleName);
        $graphName .= ", odstup v $dateIntervalCzech";

        return ["graphData" => $graphData, "graphName" => $graphName];
    }
}

$dbConfig = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'sajkoradb',
    'table'    => 'users',
    'dateCol'  => 'dateCreated',
];

$cg = new ChartGraph($dbConfig);
echo json_encode($cg->getGraphData());
return;

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
        "label" => "Nových uživatel",
        "backgroundColor" => "lime",
        "borderColor" => "green",
        "lineTension" => 0.3, // 0 - 0.3
        "fill" => false,
        "data" => $totalVisits,
    ],
];

$graphData = [
    "labels" => $labels, 
    "datasets" => $datasets,
];

$graphName = "Celkový počet návštěv ";

if ($interval)
{
    $czechInterval = timeIntervalToCzech($interval);
    $graphName .= $czechInterval;
}
else 
{
    $graphName .= "od $from do $to";
}

$dateIntervalCzech = intervalNameToCzech($dateInterval);
$graphName .= ", odstup v $dateIntervalCzech";

echo json_encode(["graphData" => $graphData, "graphName" => $graphName]);