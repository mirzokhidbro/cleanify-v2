<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create EntityManager
$config = Setup::createAttributeMetadataConfiguration(
    [__DIR__ . '/src/Domain'],
    true
);

$connection = [
    'driver'   => $_ENV['DB_DRIVER'],
    'host'     => $_ENV['DB_HOST'],
    'port'     => $_ENV['DB_PORT'],
    'dbname'   => $_ENV['DB_NAME'],
    'user'     => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD']
];

$entityManager = EntityManager::create($connection, $config);

return ConsoleRunner::createHelperSet($entityManager);
