<?php

declare(strict_types=1);

use App\Application\Common\Response\ApiResponse;
use App\Application\LeadStatus\Command\CreateLeadStatus;
use App\Application\LeadStatus\Command\CreateLeadStatusHandler;
use App\Application\LeadStatus\Command\EditLeadStatus;
use App\Application\LeadStatus\Command\EditLeadStatusHandler;
use App\Application\LeadStatus\Command\DeleteLeadStatus;
use App\Application\LeadStatus\Command\DeleteLeadStatusHandler;
use App\Application\LeadStatus\Query\GetLeadStatus;
use App\Application\LeadStatus\Query\GetLeadStatusHandler;
use App\Application\LeadStatus\Query\GetLeadStatusListQuery;
use App\Application\LeadStatus\Query\GetLeadStatusListHandler;
use App\Domain\LeadStatus\LeadStatusRepositoryInterface;
use App\Infrastructure\Persistence\DoctrineLeadStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new \App\Core\Container\Container();

// EntityManager'ni container'ga qo'shish
$container->set(EntityManagerInterface::class, function() {
    // EntityManager configuration
    $config = ORMSetup::createAttributeMetadataConfiguration(
        paths: [__DIR__ . '/../src/Domain'],
        isDevMode: true
    );

    $connection = [
        'driver'   => $_ENV['DB_DRIVER'],
        'host'     => $_ENV['DB_HOST'],
        'port'     => $_ENV['DB_PORT'],
        'dbname'   => $_ENV['DB_NAME'],
        'user'     => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD']
    ];

    return EntityManager::create($connection, $config);
});


$container->set(
    LeadStatusRepositoryInterface::class, 
    DoctrineLeadStatusRepository::class
);

$container = new class($container) implements \Psr\Container\ContainerInterface {
    public function __construct(private \App\Core\Container\Container $container) {}
    
    public function get(string $id): mixed {
        return $this->container->get($id);
    }
    
    public function has(string $id): bool {
        return true; // Simplistic implementation
    }
};

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addRoutingMiddleware();

// Error handling
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Global error handler
$errorMiddleware->setErrorHandler(
    Throwable::class,
    function (
        Psr\Http\Message\ServerRequestInterface $request,
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

$app->get('/api/v2/lead-status', function (Request $request, Response $response) use ($container) {
    try {
        $params = $request->getQueryParams();
        $companyId = $params['company_id'] ?? throw new RuntimeException('Company ID is required');
        
        $handler = $container->get(GetLeadStatusListHandler::class);
        $query = new GetLeadStatusListQuery($companyId);
        
        $result = $handler->handle($query);
        
        $apiResponse = ApiResponse::success($result);
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $apiResponse = ApiResponse::error($e->getMessage());
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

$app->get('/api/v2/lead-status/{id}', function (Request $request, Response $response, array $args) use ($container) {
    try {
        $handler = $container->get(GetLeadStatusHandler::class);
        $query = new GetLeadStatus((int) $args['id']);
        
        $result = $handler->handle($query);
        
        $apiResponse = ApiResponse::success($result);
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $apiResponse = ApiResponse::error($e->getMessage());
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

$app->post('/api/v2/lead-status', function (Request $request, Response $response) use ($container) {
    try {
        $data = json_decode($request->getBody()->getContents(), true);
        
        $handler = $container->get(CreateLeadStatusHandler::class);
        $command = new CreateLeadStatus(
            $data['name'] ?? throw new RuntimeException('Name is required'),
            $data['company_id'] ?? throw new RuntimeException('Company ID is required'),
            $data['order'] ?? throw new RuntimeException('Order is required')
        );
        
        $handler->handle($command);
        
        $apiResponse = ApiResponse::success(null, 'Lead status created successfully');
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $apiResponse = ApiResponse::error($e->getMessage());
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

// Edit lead status
$app->put('/api/v2/lead-status/{id}', function (Request $request, Response $response, array $args) use ($container) {
    try {
        $id = (int) $args['id'];
        $data = json_decode($request->getBody()->getContents(), true);
        
        $handler = $container->get(EditLeadStatusHandler::class);
        $command = new EditLeadStatus(
            $id,
            $data['name'] ?? throw new RuntimeException('Name is required')
        );
        
        $handler->handle($command);
        
        $apiResponse = ApiResponse::success(null, 'Lead status updated successfully');
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response
            ->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $apiResponse = ApiResponse::error($e->getMessage());
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

// Delete lead status
$app->delete('/api/v2/lead-status/{id}', function (Request $request, Response $response, array $args) use ($container) {
    try {
        $id = (int) $args['id'];
        
        $handler = $container->get(DeleteLeadStatusHandler::class);
        $command = new DeleteLeadStatus($id);
        
        $handler->handle($command);
        
        $apiResponse = ApiResponse::success(null, 'Lead status deleted successfully');
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response
            ->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        $apiResponse = ApiResponse::error($e->getMessage());
        
        $response->getBody()->write(json_encode($apiResponse));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

// 404 handler - eng oxirida qo'shilishi kerak
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
