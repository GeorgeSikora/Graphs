<?php

class ChartGraph {

    public $dateFormat = 'Y-m-d H:m:s';

    public $type; // line / bar / radar / doughnut / scatter

    public $graphName;
    private $dbConfig;

    public $sqlWhere;
    public $sqlGroupBy;

    public $interval = '1month';
    public $dateScaleName;
    public $fromDate, $toDate;

    private $labels = [];
    private $datasets = [];
    private $sqlList = [];
   
    function __construct($type, $name, $dbConfig) {

        $this->type = $type;
        $this->graphName = $name;
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
    }

    function addDataset($table, $tatgetCol, $dsConf, $customDbConfig = null) {

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
        
        $tc = $tatgetCol;
        $this->groupAutoscale($tc);

        $from    = $this->fromDate;
        $to      = $this->toDate;
        $where   = $this->sqlWhere;
        $groupBy = $this->sqlGroupBy;

        if ($this->type == 'line')
        {
            $sql = "
                SELECT $tc, COUNT(1) as 'totalCount'
                FROM $table 
                WHERE $tc BETWEEN '$from' AND '$to' $where
                GROUP BY $groupBy
                ORDER BY $tc
            ";
        }
        else
        {
            $sql = "
                SELECT $tc, COUNT($tc) as 'totalCount'
                FROM $table 
                GROUP BY $tc
                ORDER BY $tc
            ";
        }

        $this->sqlWhere = '';

        array_push($this->sqlList, preg_replace('/\s+/', ' ', $sql));

        $result = $mysqli->query($sql);
        $data = [];

        if ($this->type == 'line') 
        {
            while ($row = $result -> fetch_assoc()) {
                array_push($data, [
                    'x' => $row[$tc],
                    'y' => $row['totalCount'],
                ]);
            }
        }
        else
        {
            while ($row = $result -> fetch_assoc()) {
                array_push($this->labels, $row[$tc]);
                array_push($data, $row['totalCount']);
            }
        }

        $dataset = [
            "data" => $data,
        ];
        $dataset = array_merge($dataset, $dsConf);
        
        array_push($this->datasets, $dataset);

        return $this;
    }

    private function groupAutoscale($dateCol) {

        $DateFrom   = date_create($this->fromDate);
        $DateTo     = date_create($this->toDate);
        $DatesDiff  = date_diff($DateFrom, $DateTo);
        $daysDiff   = $DatesDiff->days;
        $monthsDiff = round($daysDiff * 0.032855);
        $yearsDiff  = round($daysDiff * 0.002738);
        
        $dcn = $dateCol;

        if ($yearsDiff >= 1) 
        {
            $this->dateScaleName = "months";
            $this->sqlGroupBy = "YEAR($dcn), MONTH($dcn)";
        }
        else if ($monthsDiff >= 3) 
        {
            $this->dateScaleName = "weeks";
            $this->sqlGroupBy = "YEAR($dcn), MONTH($dcn), WEEK($dcn)";
        }
        else if ($daysDiff >= 14) 
        {
            $this->dateScaleName = "days";
            $this->sqlGroupBy = "YEAR($dcn), MONTH($dcn), WEEK($dcn), DAY($dcn)";
        }
        else if ($daysDiff >= 3) 
        {
            $this->dateScaleName = "hours";
            $this->sqlGroupBy = "YEAR($dcn), MONTH($dcn), WEEK($dcn), DAY($dcn), HOUR($dcn)";
        }
        else 
        {
            $this->dateScaleName = "minutes";
            $this->sqlGroupBy = "YEAR($dcn), MONTH($dcn), DAY($dcn), HOUR($dcn), MINUTE($dcn)"; 
        }
    }

    function getGraphData() {

        $graphData = [
            "labels" => $this->labels,
            "datasets" => $this->datasets,
        ];

        $graphName = $this->graphName.' ';

        if ($this->interval)
        {
            $czechInterval = $this->timeIntervalToCzech($this->interval);
            $graphName .= "od $czechInterval";
        }
        else 
        {
            //$graphName .= "od $from do $to";
        }

        if ($this->type == 'line')
        {
            $dateScaleCzech = $this->scaleNameToCzech($this->dateScaleName);
            $graphName .= ", odstup v $dateScaleCzech";
        }

        return [
            "graphType"     => $this->type,
            "graphData"     => $graphData, 
            "graphName"     => $graphName, 
            "sqlList"       => $this->sqlList
        ];
    }

    function setType($type) {
        
        $this->type = $type;

        return $this;
    }

    function selectWhere($sqlWhere) {

        $this->sqlWhere = " AND $sqlWhere";

        return $this;
    }

    function groupBy($sqlGroup) {

        $this->sqlGroupBy = "$sqlGroup";

        return $this;
    }

    // $interval = 1month, 3year, 10days, 2week ...
    private function timeIntervalToCzech($interval) {

        $splitted = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$interval);
        $timeVal = $splitted[0];
        $timeUnit = $splitted[1];

        if ($timeVal == 1) {
            $czechInterval = "minulého ";
        } else {
            $czechInterval = "minulých ";
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

    // $scaleName = hours, days, weeks, months, years
    private function scaleNameToCzech($scaleName) {
        switch ($scaleName) {
            case 'minutes': return 'minutách';
            case 'hours':   return 'hodinách';
            case 'days':    return 'dnech';
            case 'weeks':   return 'týdnech';
            case 'months':  return 'měsících';
            case 'years':   return 'let';
            default: return $scaleName;
        }
    }
}