<?php

declare(strict_types=1);

namespace App\Application\Lead\Controller;

use App\Application\Common\Response\ApiResponse;
use App\Application\Lead\Command\CreateLead;
use App\Application\Lead\Command\CreateLeadHandler;
use App\Application\Lead\Command\DeleteLead;
use App\Application\Lead\Command\DeleteLeadHandler;
use App\Application\Lead\Command\EditLead;
use App\Application\Lead\Command\EditLeadHandler;
use App\Application\Lead\Command\AddComment;
use App\Application\Lead\Command\AddCommentHandler;
use App\Application\Lead\Query\GetLeadListQuery;
use App\Application\Lead\Query\GetLeadListHandler;
use App\Application\Lead\Query\GetLeadQuery;
use App\Application\Lead\Query\GetLeadHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class LeadController
{
    public function __construct(
        private readonly CreateLeadHandler $createHandler,
        private readonly EditLeadHandler $editHandler,
        private readonly DeleteLeadHandler $deleteHandler,
        private readonly GetLeadListHandler $listHandler,
        private readonly GetLeadHandler $getHandler,
        private readonly AddCommentHandler $addCommentHandler
    ) {}

    public function index(Request $request, Response $response): Response
    {
        try {
            $params = $request->getQueryParams();
            $companyId = $params['company_id'] ?? throw new RuntimeException('Company ID is required');
            $phone = $params['phone'] ?? null;
            $status = isset($params['status']) ? (int) $params['status'] : null;

            $query = new GetLeadListQuery($companyId, $phone, $status);
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
            
            $command = new CreateLead(
                $data['company_id'] ?? throw new RuntimeException('Company ID is required'),
                $data['phone_number'] ?? throw new RuntimeException('Phone number is required'),
                $data['status'] ?? throw new RuntimeException('Status is required'),
                $data['name'] ?? null,
                $data['address'] ?? null,
                $data['source'] ?? null,
                $data['comment'] ?? null
            );
            
            $this->createHandler->handle($command);
            
            $apiResponse = ApiResponse::success(null, 'Lead created successfully');
            
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

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            
            $query = new GetLeadQuery($id);
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

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int) $args['id'];
            $data = json_decode($request->getBody()->getContents(), true);
            
            $command = new EditLead(
                $id,
                $data['name'] ?? null,
                $data['address'] ?? null,
                $data['phone_number'] ?? null,
                $data['source'] ?? null,
                isset($data['status']) ? (int) $data['status'] : null
            );
            
            $this->editHandler->handle($command);
            
            $apiResponse = ApiResponse::success(null, 'Lead updated successfully');
            
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
            
            $command = new DeleteLead($id);
            
            $this->deleteHandler->handle($command);
            
            $apiResponse = ApiResponse::success(null, 'Lead deleted successfully');
            
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

    public function addComment(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            
            $command = new AddComment(
                $data['lead_id'] ?? throw new RuntimeException('Lead ID is required'),
                $data['comment'] ?? throw new RuntimeException('Comment is required')
            );
            
            $this->addCommentHandler->handle($command);
            
            $apiResponse = ApiResponse::success(null, 'Comment added successfully');
            
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
