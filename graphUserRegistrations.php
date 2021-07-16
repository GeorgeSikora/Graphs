<?php

include 'ChartGraph.php';

// Konfigurace databáze
$dbConfigLocal = [
    'hostname' => 'localhost', // IP hosta
    'username' => 'root', // Uživatel
    'password' => '', // Heslo
    'database' => 'graphs', // Databáze
    'table'    => 'visits', // Tabulka
    'dateCol'  => 'dateCreated', // Sloupec s datumem
];

// Konfigurace databáze
$dbConfig = [
    'hostname' => '185.221.124.205', // IP hosta
    'username' => 'janek', // Uživatel
    'password' => 'kokos', // Heslo
    'database' => 'sajkoradb', // Databáze
    'table'    => 'users', // Tabulka
    'dateCol'  => 'dateCreated', // Sloupec s datumem
];

// Vytvoření objektu grafu
$cg = new ChartGraph('Název grafu', $dbConfig);

// tabulka, název sloupce s datem
$cg->addDataset('Nových uživatel','orange', 'users', 'dateCreated');
$cg->addDataset('Nových uživatel z lokalu', 'lime', 'visits', 'dateCreated', $dbConfigLocal);

// Navrácení JSON objektu
echo json_encode($cg->getGraphData());