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

    private function resolve(string $concrete): mixed
    {
        try {
            $reflector = new ReflectionClass($concrete);
            
            if (!$reflector->isInstantiable()) {
                throw new RuntimeException("Class $concrete is not instantiable");
            }
            
            $constructor = $reflector->getConstructor();
            
            if (!$constructor) {
                return new $concrete();
            }

            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $type = $parameter->getType();
                
                if (!$type) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $dependencies[] = $parameter->getDefaultValue();
                        continue;
                    }
                    
                    throw new RuntimeException(
                        "Cannot resolve parameter {$parameter->getName()} without type hint or default value"
                    );
                }

                if ($type->isBuiltin()) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $dependencies[] = $parameter->getDefaultValue();
                        continue;
                    }

                    // Try to get scalar value from container
                    $paramName = $parameter->getName();
                    if (isset($this->services[$paramName])) {
                        $dependencies[] = $this->services[$paramName];
                        continue;
                    }
                    
                    throw new RuntimeException(
                        "Cannot resolve scalar parameter {$parameter->getName()} without default value"
                    );
                }

                $dependencies[] = $this->get($type->getName());
            }

            $instance = $reflector->newInstanceArgs($dependencies);
            $this->resolved[$concrete] = $instance;

            return $instance;
        } catch (\ReflectionException $e) {
            throw new RuntimeException("Cannot resolve class $concrete: " . $e->getMessage());
        }
    }
}
