<?php

declare(strict_types=1);

namespace App\Core\Container;

use ReflectionClass;
use RuntimeException;

class Container
{
    private array $services = [];

    private array $resolved = [];

    public function set(string $id, mixed $concrete): void
    {
        $this->services[$id] = $concrete;
    }

    public function get(string $id): mixed
    {
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        $concrete = $this->services[$id] ?? $id;

        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }

        if (is_string($concrete)) {
            return $this->resolve($concrete);
        }

        return $concrete;
    }

    private function resolve(string $concrete): object
    {
        $reflector = new ReflectionClass($concrete);
        
        $constructor = $reflector->getConstructor();
        
        if (!$constructor) {
            return new $concrete();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            
            if (!$type) {
                throw new RuntimeException(
                    "Cannot resolve parameter {$parameter->getName()} without type hint"
                );
            }

            $dependencies[] = $this->get($type->getName());
        }

        $instance = $reflector->newInstanceArgs($dependencies);
        $this->resolved[$concrete] = $instance;

        return $instance;
    }
}
