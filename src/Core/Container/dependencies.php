<?php

declare(strict_types=1);

use App\Application\Client\Command\DeleteClientHandler;
use App\Application\Employee\Command\DeleteEmployeeHandler;
use App\Application\Employee\Command\EditEmployeeHandler;
use App\Application\OrderItemType\Command\DeleteOrderItemTypeHandler;
use App\Core\Database\Connection;
use App\Domain\Client\ClientRepository;
use App\Domain\Employee\EmployeeRepository;
use App\Domain\OrderItemType\OrderItemTypeRepository;
use DI\ContainerBuilder;
use function DI\{autowire, get};

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Database
        Connection::class => autowire()
            ->constructorParameter('dsn', 'pgsql:host=localhost;port=5432;dbname=cleanify')
            ->constructorParameter('username', 'postgres')
            ->constructorParameter('password', 'postgres'),

        // Repositories
        ClientRepository::class => autowire()
            ->constructorParameter('connection', get(Connection::class)),
        EmployeeRepository::class => autowire()
            ->constructorParameter('connection', get(Connection::class)),
        OrderItemTypeRepository::class => autowire()
            ->constructorParameter('connection', get(Connection::class)),

        // Client
        DeleteClientHandler::class => autowire()
            ->constructorParameter('clientRepository', get(ClientRepository::class)),

        // Employee
        DeleteEmployeeHandler::class => autowire()
            ->constructorParameter('employeeRepository', get(EmployeeRepository::class)),
        EditEmployeeHandler::class => autowire()
            ->constructorParameter('employeeRepository', get(EmployeeRepository::class)),

        // OrderItemType
        DeleteOrderItemTypeHandler::class => autowire()
            ->constructorParameter('orderItemTypeRepository', get(OrderItemTypeRepository::class)),
    ]);
};
