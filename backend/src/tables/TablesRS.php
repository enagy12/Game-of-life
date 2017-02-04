<?php
namespace hu\chrome\gameoflife\tables;

use hu\doxasoft\phpbackend\authentication\Requester;
use hu\doxasoft\phpbackend\exceptions\UnknownPathException;
use hu\doxasoft\phpbackend\Request;
use hu\doxasoft\phpbackend\RequestHandler;

/**
 * Class TablesRS
 * @package hu\chrome\gameoflife\tables
 *
 * @property TablesDAO $tablesDAO
 */
class TablesRS extends RequestHandler {

    private $tablesDAO;

    private static $BASE_LIF_URL = __DIR__.'/lif';

    public function __construct(Requester &$requester, Request &$req) {
        parent::__construct($requester, $req);
        $this->tablesDAO = new TablesDAO();
    }

    function handleRequest() {
        $route = $this->request->getNextRoute();
        switch ($route) {
            case null:
                $this->hasToBeGet();
                return array_merge($this->getAvailableTablesFromFiles(), $this->tablesDAO->getAll());
            case 'next':
                $this->hasToBePost();
                $this->hasToHavePayload();
                return $this->calculateNextStep($this->request->getPayload());
            case 'add':
                $this->hasToBePost();
                $this->hasToHavePayload();
                return $this->tablesDAO->add($this->request->getPayload());
            default:
                if (is_string($route)) {
                    $this->hasToBePost();
                    $this->hasToHavePayload();
                    return $this->initTableFromLif($route, $this->request->getPayload());
                } else {
                    throw new UnknownPathException();
                }
        }
    }

    private function getAvailableTablesFromFiles() {
        $fileNames = [];
        $files = array_diff(scandir(TablesRS::$BASE_LIF_URL), array('.', '..'));
        foreach($files as $file) {
            $fileNames[] = array('table_name' => pathinfo($file, PATHINFO_FILENAME));
        }
        return $fileNames;
    }

    private function initTableFromLif($name, $data) {
        $shapeView = false;
        $tableRows = (int)$data->rows;
        $tableCols = (int)$data->cols;
        $tableCenterRow = (int)round($tableRows / 2, 0, PHP_ROUND_HALF_ODD)-1;
        $tableCenterCol = (int)round($tableCols / 2, 0, PHP_ROUND_HALF_ODD)-1;
        $shapeTop = 0;
        $shapeLeft = 0;
        $extraRowInShape = 0;

        $tableLif = file(TablesRS::$BASE_LIF_URL.'/'.$name.'.lif');
        foreach($tableLif as $line) {
            if (0 === strpos($line, '#P')) {
                $shapeView = true;
                $line = trim(str_replace('#P', '', $line));
                $shapeTopLeftCoordinates = explode(' ', $line);
                if (sizeof($shapeTopLeftCoordinates)) {
                    $shapeTop = $shapeTopLeftCoordinates[0];
                    $shapeLeft = $shapeTopLeftCoordinates[1];
                }
                $extraRowInShape = 0;
            } else if ($shapeView) {
                $splittedLine = str_split(trim($line));
                for ($i = 0; $i < count($splittedLine); $i++) {
                    if ($splittedLine[$i] == '*') {
                        $data->table[$tableCenterRow + $shapeTop + $extraRowInShape]->cells[$tableCenterCol + $shapeLeft + $i]->state = 1;
                    }
                }
                $extraRowInShape++;
            }
        }
        return $data;
    }

    private function calculateNextStep($data) {
        for ($i = 0; $i < count($data->table); $i++) {
            for ($j = 0; $j < count($data->table[$i]->cells); $j++) {
                if ($data->table[$i]->cells[$j]->state == 1) {
                    $this->checkAliveAndDeadIfNeeded($i, $j, $data->table);
                } else {
                    $this->checkDeadAndReviveIfNeeded($i, $j, $data->table);
                }
            }
        }

        for ($i = 0; $i < count($data->table); $i++) {
            for ($j = 0; $j < count($data->table[$i]->cells); $j++) {
                if ($data->table[$i]->cells[$j]->state == 2) {
                    $data->table[$i]->cells[$j]->state = 0;
                }
                if ($data->table[$i]->cells[$j]->state == 3) {
                    $data->table[$i]->cells[$j]->state = 1;
                }
            }
        }

        return $data;
    }

    private function checkAliveAndDeadIfNeeded($i, $j, &$currentTable) {
        $neighborNumber = $this->checkNeighbor($i, $j, $currentTable);
        if ($neighborNumber < 2 || $neighborNumber > 3) {
            $currentTable[$i]->cells[$j]->state = 2; //become dead
        }
    }

    private function checkDeadAndReviveIfNeeded($i, $j, &$currentTable) {
        $neighborNumber = $this->checkNeighbor($i, $j, $currentTable);
        if ($neighborNumber == 3) {
            $currentTable[$i]->cells[$j]->state = 3; //become alive
        }
    }

    private function checkNeighbor($i, $j, &$currentTable) {
        $neighborNumber = 0;
        for ($k = -1; $k <= 1; $k++) {
            if (isset($currentTable[$i-1]) && isset($currentTable[$i-1]->cells[$j+$k]) && $currentTable[$i-1]->cells[$j+$k]->state != 0 && $currentTable[$i-1]->cells[$j+$k]->state != 3) {
                $neighborNumber++;
            }
            if (isset($currentTable[$i+1]) && isset($currentTable[$i+1]->cells[$j+$k]) && $currentTable[$i+1]->cells[$j+$k]->state != 0 && $currentTable[$i+1]->cells[$j+$k]->state != 3) {
                $neighborNumber++;
            }
        }
        if (isset($currentTable[$i]->cells[$j-1]) && $currentTable[$i]->cells[$j-1]->state != 0 && $currentTable[$i]->cells[$j-1]->state != 3) {
            $neighborNumber++;
        }
        if (isset($currentTable[$i]->cells[$j+1]) && $currentTable[$i]->cells[$j+1]->state != 0 && $currentTable[$i]->cells[$j+1]->state != 3) {
            $neighborNumber++;
        }
        return $neighborNumber;
    }
}
