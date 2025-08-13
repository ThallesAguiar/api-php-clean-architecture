<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__.'/../vendor/autoload.php';

use App\Application\Providers\AppServiceProvider;
use App\Infra\DI\Container;
use App\Infra\Http\Request;
use App\Infra\Routes\Router;

$container = Container::getInstance();

$appServiceProvider = new AppServiceProvider($container);
$appServiceProvider->register();

$request = new Request();

$router = new Router($container, $request);
$router->dispatch();
