<?php

namespace App\Infra\DI;

use Closure;
use ReflectionClass;
use ReflectionParameter;
use Exception;

class Container
{
    protected static $instance;
    protected $bindings = [];

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    public function make($abstract, $parameters = [])
    {
        return $this->resolve($abstract, $parameters);
    }

    public function resolve($abstract, $parameters = [])
    {
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract]['concrete'];
        } else {
            $concrete = $abstract;
        }

        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $this->resolveDependencies($constructor->getParameters());
        
        return $reflector->newInstanceArgs($dependencies);
    }

    protected function resolveDependencies(array $parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = null;
            if ($parameter->getType() && !$parameter->getType()->isBuiltin()) {
                $dependency = new ReflectionClass($parameter->getType()->getName());
            }

            if (is_null($dependency)) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve class dependency {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->resolve($dependency->name);
            }
        }

        return $dependencies;
    }
}
