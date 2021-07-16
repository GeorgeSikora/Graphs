<?php

class ChartGraph {

    public $dateFormat = 'Y-m-d';

    public $graphName;
    private $dbConfig;
    public $groupBy;

    public $interval = '1month';
    public $dateScaleName = 'days';
    public $fromDate, $toDate;

    private $datasets = [];
   
    function __construct($graphName, $dbConfig) {

        $this->graphName = $graphName;
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

    function addDataset($labelName, $color, $table, $dateCol, $customDbConfig = null) {

        if ($customDbConfig != null) 
        {
            $mysqli = new mysqli(
                $customDbConfig['hostname'], 
                $customDbConfig['username'],
                $customDbConfig['password'],
                $customDbConfig['database']
            );
        }
        else
        {
            $mysqli = new mysqli(
                $this->dbConfig['hostname'], 
                $this->dbConfig['username'],
                $this->dbConfig['password'],
                $this->dbConfig['database']
            );
        }

        $dcn     = $dateCol;
        $from    = $this->fromDate;
        $to      = $this->toDate;
        $groupBy = $this->groupBy;

        $sql = "
            SELECT $dcn, COUNT(1) as 'totalCount' 
            FROM $table 
            WHERE $dcn BETWEEN '$from' AND '$to'
            GROUP BY $groupBy
            ORDER BY $dcn
        ";

        $result = $mysqli->query($sql);
        $data = [];

        while ($row = $result -> fetch_assoc()) {
            array_push($data, [
                'x' => $row[$dcn],
                'y' => $row['totalCount'],
            ]);
        }

        $dataset = [
            "label" => $labelName,
            "backgroundColor" => $color,
            "borderColor" => $color,
            "lineTension" => 0.3, // 0 - 0.3
            "fill" => false,
            "data" => $data,
        ];
        
        array_push($this->datasets, $dataset);
    }

    function getGraphData() {
        /*
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
            SELECT $dcn, COUNT(1) as 'totalCount' 
            FROM $table 
            WHERE $dcn BETWEEN '$from' AND '$to'
            GROUP BY $groupBy
            ORDER BY $dcn
        ";

        $result = $mysqli -> query($sql);

        while ($row = $result -> fetch_assoc()) {
            //array_push($this->labels, $row[$dcn]);
            array_push($this->data, [
                'x' => $row[$dcn],
                'y' => $row['totalCount'],
            ]);
        }

        $dataset = [
            "label" => "Nových uživatel",
            "backgroundColor" => "lime",
            "borderColor" => "green",
            "lineTension" => 0.3, // 0 - 0.3
            "fill" => false,
            "data" => $this->data,
        ];
        
        array_push($this->datasets, $dataset);
        
        */

        $graphData = [
            //"labels" => $this->labels, 
            "datasets" => $this->datasets,
        ];

        $graphName = $this->graphName.' ';

        if ($this->interval)
        {
            $czechInterval = $this->timeIntervalToCzech($this->interval);
            $graphName .= $czechInterval;
        }
        else 
        {
            $graphName .= "od $from do $to";
        }

        $dateScaleCzech = $this->scaleNameToCzech($this->dateScaleName);
        $graphName .= ", odstup v $dateScaleCzech";

        return ["graphData" => $graphData, "graphName" => $graphName];
    }

    // 1month, 3year, 10days, 2week ...
    private function timeIntervalToCzech($interval) {

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
    private function scaleNameToCzech($scaleName) {
        switch ($scaleName) {
            case 'hours':   return 'hodinách';
            case 'days':    return 'dnech';
            case 'weeks':   return 'týdnech';
            case 'months':  return 'měsících';
            case 'years':   return 'let';
            default: return $scaleName;
        }
    }
}