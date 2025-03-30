<?php

declare(strict_types=1);

use App\Application\Common\Response\ApiResponse;
use App\Application\Lead\Controller\LeadController;
use App\Application\LeadStatus\Controller\LeadStatusController;
use App\Domain\LeadStatus\LeadStatusRepositoryInterface;
use App\Infrastructure\Persistence\DoctrineLeadStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new \App\Core\Container\Container();

// EntityManager'ni container'ga qo'shish
// Create EntityManager
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/../src/Domain'],
    isDevMode: true,
);

$connection = [
    'driver' => $_ENV['DB_DRIVER'],
    'host' => $_ENV['DB_HOST'],
    'port' => (int)$_ENV['DB_PORT'],
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8'
];

$entityManager = EntityManager::create($connection, $config);

// Add to container
$container->set(EntityManagerInterface::class, $entityManager);
$container->set(EntityManager::class, $entityManager);

$container->set(
    LeadStatusRepositoryInterface::class, 
    DoctrineLeadStatusRepository::class
);

$container->set(
    \App\Domain\Lead\LeadRepositoryInterface::class,
    \App\Infrastructure\Persistence\DoctrineLeadRepository::class
);

$container->set(
    \App\Domain\Comment\CommentRepositoryInterface::class,
    \App\Infrastructure\Persistence\DoctrineCommentRepository::class
);

$container->set(
    \App\Domain\Salary\SalaryRepositoryInterface::class,
    \App\Infrastructure\Persistence\DoctrineSalaryRepository::class
);

$container = new class($container) implements \Psr\Container\ContainerInterface {
    public function __construct(private \App\Core\Container\Container $container) {}
    
    public function get(string $id): mixed {
        return $this->container->get($id);
    }
    
    public function has(string $id): bool {
        return true;
    }
};

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addRoutingMiddleware();

// Add routes
$routes = require __DIR__ . '/../routes/api.php';
$routes($app);

// Error handling
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Global error handler
$errorMiddleware->setErrorHandler(
    \Throwable::class,
    function (
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) use ($app) {
        $payload = ApiResponse::error(
            $exception->getMessage(),
            $displayErrorDetails ? [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace()
            ] : null
        );

        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(json_encode($payload));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
);

// 404 handler
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    $apiResponse = ApiResponse::error('Route not found', [
        'method' => $request->getMethod(),
        'uri' => $request->getUri()->getPath()
    ]);
    
    $response->getBody()->write(json_encode($apiResponse));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);
});

$app->run();
