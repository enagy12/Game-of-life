<?php

require_once 'config.php';
require __DIR__ . '/vendor/autoload.php';

use hu\chrome\gameoflife\tables\TablesRS;
use hu\doxasoft\phpbackend\DoxaBackendApp;
use hu\doxasoft\phpbackend\DoxaBackendConfiguration;

$app = new DoxaBackendApp((new DoxaBackendConfiguration())
    ->frontend('http://gameoflife')
    ->backend('http://be.gameoflife')
    ->ready()
);

$app->addService('table', TablesRS::class);

$app->run();