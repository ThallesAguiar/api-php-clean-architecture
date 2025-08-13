<?php

namespace App\Infra\Routes;

use App\Infra\DI\Container;
use App\Infra\Http\Request;
use App\Infra\Http\Response;
use Closure;

class Router
{
    protected static $routes = [];
    protected $container;
    protected $request;

    public function __construct(Container $container, Request $request)
    {
        $this->container = $container;
        $this->request = $request;
        $this->loadRoutes();
    }

    private function loadRoutes()
    {
        require_once __DIR__ . "/../../../routes/api.php";
    }

    public static function get($uri, $action)
    {
        self::addRoute('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        self::addRoute('POST', $uri, $action);
    }

    public static function addRoute($method, $uri, $action)
    {
        self::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    public function dispatch()
    {
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();

        foreach (self::$routes as $route) {
            if ($this->matchRoute($route, $uri, $method)) {
                return $this->executeAction($route, $uri);
            }
        }

        Response::error('Route not found', 404);
    }

    private function matchRoute($route, $uri, $method)
    {
        if ($route['method'] !== $method) {
            return false;
        }

        $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9_]+)', $route['uri']);
        $pattern = "#^" . $pattern . "$#";

        return preg_match($pattern, $uri);
    }

    private function executeAction($route, $uri)
    {
        $action = $route['action'];
        $params = $this->extractParams($route, $uri);

        if ($action instanceof Closure) {
            return $action(...$params);
        }

        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);
            $controllerClass = "App\\Infra\\Http\\".$controller;

            if (class_exists($controllerClass)) {
                $controllerInstance = $this->container->make($controllerClass);
                if (method_exists($controllerInstance, $method)) {
                    $reflectionMethod = new \ReflectionMethod($controllerInstance, $method);
                    $methodParams = $reflectionMethod->getParameters();

                    $args = [];
                    foreach ($methodParams as $param) {
                        if ($param->getType() && $param->getType()->getName() === Request::class) {
                            $args[] = $this->request;
                        } else {
                            if (count($params) > 0) {
                                $args[] = array_shift($params);
                            }
                        }
                    }

                    return $controllerInstance->$method(...$args);
                }
            }
        }

        Response::error('Invalid action', 500);
    }

    private function extractParams($route, $uri)
    {
        $params = [];
        $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9_]+)', $route['uri']);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);
            $params = $matches;
        }

        return $params;
    }
}