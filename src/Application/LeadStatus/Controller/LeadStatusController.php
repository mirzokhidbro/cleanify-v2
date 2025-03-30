<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Controller;

use App\Application\Common\Response\ApiResponse;
use App\Application\LeadStatus\Command\CreateLeadStatus;
use App\Application\LeadStatus\Command\CreateLeadStatusHandler;
use App\Application\LeadStatus\Command\DeleteLeadStatus;
use App\Application\LeadStatus\Command\DeleteLeadStatusHandler;
use App\Application\LeadStatus\Command\EditLeadStatus;
use App\Application\LeadStatus\Command\EditLeadStatusHandler;
use App\Application\LeadStatus\Query\GetLeadStatus;
use App\Application\LeadStatus\Query\GetLeadStatusHandler;
use App\Application\LeadStatus\Query\GetLeadStatusListQuery;
use App\Application\LeadStatus\Query\GetLeadStatusListHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class LeadStatusController
{
    public function __construct(
        private readonly CreateLeadStatusHandler $createHandler,
        private readonly EditLeadStatusHandler $editHandler,
        private readonly DeleteLeadStatusHandler $deleteHandler,
        private readonly GetLeadStatusListHandler $listHandler,
        private readonly GetLeadStatusHandler $getHandler
    ) {}

    public function index(Request $request, Response $response): Response
    {
        try {
            $params = $request->getQueryParams();
            $companyId = $params['company_id'] ?? throw new RuntimeException('Company ID is required');

            $query = new GetLeadStatusListQuery($companyId);
            $result = $this->listHandler->handle($query);

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
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            
            $command = new CreateLeadStatus(
                $data['name'] ?? throw new RuntimeException('Name is required'),
                $data['company_id'] ?? throw new RuntimeException('Company ID is required'),
                $data['order'] ?? throw new RuntimeException('Order is required')
            );
            
            $this->createHandler->handle($command);
            
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
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $data = json_decode($request->getBody()->getContents(), true);
            
            $command = new EditLeadStatus(
                $id,
                $data['name'] ?? throw new RuntimeException('Name is required'),
                $data['color'] ?? null
            );
            
            $this->editHandler->handle($command);
            
            $apiResponse = ApiResponse::success(null, 'Lead status updated successfully');
            
            $response->getBody()->write(json_encode($apiResponse));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $apiResponse = ApiResponse::error($e->getMessage());
            
            $response->getBody()->write(json_encode($apiResponse));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $query = new GetLeadStatus((int) $args['id']);
            
            $result = $this->getHandler->handle($query);
            
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
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];

            if ($id === 99 || $id === 100) {
                throw new RuntimeException("Default o'rnatilgan statuslarni o'chirish mumkin emas");
            }
            
            $command = new DeleteLeadStatus($id);
            
            $this->deleteHandler->handle($command);
            
            $apiResponse = ApiResponse::success(null, 'Lead status deleted successfully');
            
            $response->getBody()->write(json_encode($apiResponse));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $apiResponse = ApiResponse::error($e->getMessage());
            
            $response->getBody()->write(json_encode($apiResponse));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}
