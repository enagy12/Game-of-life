<?php

require_once 'config.php';
require __DIR__ . '/vendor/autoload.php';

use hu\chrome\gameoflife\tables\TablesRS;
use \hu\chrome\gameoflife\tables\TablesDAO;
use hu\doxasoft\phpbackend\DoxaBackendApp;
use hu\doxasoft\phpbackend\DoxaBackendConfiguration;

$app = new DoxaBackendApp((new DoxaBackendConfiguration())
    ->frontend('http://gameoflife')
    ->backend('http://be.gameoflife')
    ->db_host('localhost')
    ->db_name('gameoflife')
    ->db_user('gameoflife')
    ->db_pass('qwe123')
    ->ready()
);

$app->addService('table', TablesRS::class, TablesDAO::class);

$app->run();