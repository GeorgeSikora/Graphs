<?php include 'ChartGraph.php';

// konfigurace databáze č.2
$dbConfig = [
    'hostname' => '185.221.124.205', // IP hosta
    'username' => '', // Uživatel
    'password' => '', // Heslo
    'database' => 'sajkoradb', // Databáze
];

// vytvoření objektu grafu
$cg = new ChartGraph('line', 'Návštěvnost', $dbConfig); // line / bar / radar / doughnut / scatter

$show = isset($_GET['show']) ? $_GET['show'] : '';

switch ($show)
{
    case 'browser':
        $cg ->setType('doughnut')
            ->addDataset('visits', 'browser', [
            'label' => 'požadavků',
            'backgroundColor' => [
                '#42BFDD', '#F7B2B7', '#559CAD', '#DE639A', '#084B83', '#4A5899', '#F7717D'
            ],
            'borderColor' => '#222',
            'borderWidth' => 1,
            'lineTension' => 0.3,
            'fill' => false,
        ]);
        break;
    case 'platform':
        $cg ->setType('doughnut')
            ->addDataset('visits', 'platform', [
            'label' => 'požadavků',
            'backgroundColor' => [
                '#DE639A', '#084B83', '#4A5899', '#F7717D', '#42BFDD', '#F7B2B7', '#559CAD'
            ],
            'borderColor' => '#222',
            'borderWidth' => 1,
            'lineTension' => 0.3,
            'fill' => false,
        ]);
        break;
    case 'method':
        $cg ->setType('doughnut')
            ->addDataset('visits', 'method', [
            'label' => 'požadavků',
            'backgroundColor' => [
                '#84A9C0', '#B3CBB9'
            ],
            'borderColor' => '#222',
            'borderWidth' => 1,
            'lineTension' => 0.3,
            'fill' => false,
        ]);
        break;
    case 'url':
        $cg ->setType('doughnut')
            ->addDataset('visits', 'url', [
            'label' => 'požadavků',
            'backgroundColor' => [
                'blue', 'yellow'
            ],
            'borderColor' => '#222',
            'borderWidth' => 1,
            'lineTension' => 0.3,
            'fill' => false,
        ]);
        break;
    case 'status':
        $cg ->setType('doughnut')
            ->addDataset('visits', 'status', [
            'label' => 'požadavků',
            'backgroundColor' => [
                'blue', 'yellow'
            ],
            'borderColor' => '#222',
            'borderWidth' => 1,
            'lineTension' => 0.3,
            'fill' => false,
        ]);
        break;
    default: 
        // Přidání nového datasetu (dat grafu)
        $cg ->addDataset('visits', 'dateCreated', [
            'label' => 'Načtených požadavků',
            'backgroundColor' => 'yellow',
            'borderColor' => 'orange',
            'lineTension' => 0.3,
            'fill' => false,
        ]);

        // Přidání nového datasetu (dat grafu)
        $cg ->selectWhere("method = 'GET'")
            ->addDataset('visits', 'dateCreated', [
            'label' => 'GET požadavků',
            'backgroundColor' => 'green',
            'borderColor' => 'lime',
            'lineTension' => 0.3,
            'fill' => false,
        ]);

        // Přidání nového datasetu (dat grafu)
        $cg ->selectWhere("method = 'POST'")
            ->addDataset('visits', 'dateCreated', [
            'label' => 'POST požadavků',
            'backgroundColor' => 'darkred',
            'borderColor' => 'red',
            'lineTension' => 0.3,
            'fill' => false,
        ]);
        break;
}

// Navrácení JSON objektu
echo json_encode($cg->getGraphData());