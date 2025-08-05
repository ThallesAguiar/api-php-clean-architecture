<?php

require_once __DIR__.'/../vendor/autoload.php';

use App\Infra\Routes\Router;

$router = new Router();
$router->dispatch();

