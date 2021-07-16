<?php include 'ChartGraph.php';

// konfigurace databáze č.1
$dbConfigLocal = [
    'hostname' => 'localhost', // IP hosta
    'username' => 'root', // Uživatel
    'password' => '', // Heslo
    'database' => 'graphs', // Databáze
];

// konfigurace databáze č.2
$dbConfig = [
    'hostname' => '185.221.124.205', // IP hosta
    'username' => '', // Uživatel
    'password' => '', // Heslo
    'database' => 'sajkoradb', // Databáze
];

// vytvoření objektu grafu
$cg = new ChartGraph('Název grafu', $dbConfig);

// Přidání nového datasetu (dat grafu)
$cg->addDataset('users', 'dateCreated', [
    'label' => 'Návštěvnost',
    'backgroundColor' => 'pink',
    'borderColor' => 'purple',
    'lineTension' => 0.3,
    'fill' => false,
]);

// Přidání nového datasetu (dat grafu)
$cg->addDataset('visits', 'dateCreated', [
    'label' => 'Nových uživatel',
    'backgroundColor' => 'yellow',
    'borderColor' => 'orange',
    'lineTension' => 0.3,
    'fill' => false,
], $dbConfigLocal);

// Navrácení JSON objektu
echo json_encode($cg->getGraphData());