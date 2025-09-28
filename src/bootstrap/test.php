<?php

use Dotenv\Dotenv;
use App\Helpers\Config;
use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;

define("ROOT_PATH", dirname(__DIR__) . '/../');

require_once ROOT_PATH . "vendor/autoload.php";

$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

$config = Config::getConfig('database', 'pdo_testing');

$PDOConnection = new PDODatabaseConnection($config);

$PDOQueryBuilder = new PDOQueryBuilder($PDOConnection->connect());

$PDOQueryBuilder->truncateAllTables();
