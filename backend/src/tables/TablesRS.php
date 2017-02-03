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
 */
class TablesRS extends RequestHandler {

    public function __construct(Requester &$requegit add .ster, Request &$req) {
        parent::__construct($requester, $req);
    }

    function handleRequest() {
        $route = $this->request->getNextRoute();
        switch ($route) {
            case null:         // /table list
                $this->hasToBeGet();
                return ['1', '2'];

            case 'next':         // /table/next
                $this->hasToBePost();
                $this->hasToHavePayload();
                return $this->calculateNextStep($this->request->getPayload());

            default:
                if (is_numeric($route)) {
                    return $this->handleItemRequest($route);
                } else {                    // /places/<any>
                    throw new UnknownPathException();
                }
        }
    }

    private function handleItemRequest($itemId) {
        $action = $this->request->getNextRoute();
        switch ($action) {
            case null:          // /tables/:id
                $this->hasToBeGet();
                return [];

            default:            // /places/:id/<any>
                throw new UnknownPathException();
        }
    }

    private function calculateNextStep($current) {
        for ($i = 0; $i < count($current->table); $i++) {
            for ($j = 0; $j < count($current->table[$i]->cells); $j++) {
                if ($current->table[$i]->cells[$j]->state == 1) {
                    $this->checkAliveAndDeadIfNeeded($i, $j, $current->table);
                } else {
                    $this->checkDeadAndReviveIfNeeded($i, $j, $current->table);
                }
            }
        }

        for ($i = 0; $i < count($current->table); $i++) {
            for ($j = 0; $j < count($current->table[$i]->cells); $j++) {
                if ($current->table[$i]->cells[$j]->state == 2) {
                    $current->table[$i]->cells[$j]->state = 0;
                }
                if ($current->table[$i]->cells[$j]->state == 3) {
                    $current->table[$i]->cells[$j]->state = 1;
                }
            }
        }

        return $current;
    }

    private function checkAliveAndDeadIfNeeded($i, $j, $currentTable) {
        $neighborNumber = $this->checkNeighbor($i, $j, $currentTable);
        if ($neighborNumber < 2 || $neighborNumber > 3) {
            $currentTable[$i]->cells[$j]->state = 2; //become dead
        }
    }

    private function checkDeadAndReviveIfNeeded($i, $j, $currentTable) {
        $neighborNumber = $this->checkNeighbor($i, $j, $currentTable);
        if ($neighborNumber == 3) {
            $currentTable[$i]->cells[$j]->state = 3; //become alive
        }
    }

    private function checkNeighbor($i, $j, $currentTable) {
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
