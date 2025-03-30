<?php

declare(strict_types=1);

namespace App\Application\Salary\Controller;

use App\Application\Salary\Command\CreateSalary;
use App\Application\Salary\Command\CreateSalaryHandler;
use App\Application\Salary\Command\DeleteSalary;
use App\Application\Salary\Command\DeleteSalaryHandler;
use App\Application\Salary\Command\EditSalary;
use App\Application\Salary\Command\EditSalaryHandler;
use App\Application\Salary\Query\GetSalaryListQuery;
use App\Application\Salary\Query\GetSalaryListHandler;
use App\Application\Salary\Query\GetSalaryQuery;
use App\Application\Salary\Query\GetSalaryHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SalaryController
{
    public function __construct(
        private readonly GetSalaryListHandler $getSalaryListHandler,
        private readonly GetSalaryHandler $getSalaryHandler,
        private readonly CreateSalaryHandler $createSalaryHandler,
        private readonly EditSalaryHandler $editSalaryHandler,
        private readonly DeleteSalaryHandler $deleteSalaryHandler
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $companyId = $queryParams['company_id'] ?? null;
        $employeeId = isset($queryParams['employee_id']) ? (int)$queryParams['employee_id'] : null;

        $data = $this->getSalaryListHandler->handle(new GetSalaryListQuery($companyId, $employeeId));

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'description' => 'Success',
            'data' => $data
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $data = $this->getSalaryHandler->handle(new GetSalaryQuery((int)$args['id']));

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'description' => 'Success',
            'data' => $data
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();

        // Validate required fields
        $errors = [];
        
        // Basic required fields
        $basicFields = ['company_id', 'employee_id'];
        foreach ($basicFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "$field is required";
            }
        }

        // At least one of salary_amount or amount_received must be present
        if ((!isset($data['salary_amount']) || empty($data['salary_amount'])) && 
            (!isset($data['amount_received']) || empty($data['amount_received']))) {
            $errors[] = "Either salary_amount or amount_received must be provided";
        }

        if (!empty($errors)) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'description' => 'Validation failed',
                'errors' => $errors
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        try {
            $salaryAmount = isset($data['salary_amount']) ? (int)$data['salary_amount'] : null;
            $amountReceived = isset($data['amount_received']) ? (int)$data['amount_received'] : null;

            $this->createSalaryHandler->handle(new CreateSalary(
                $data['company_id'],
                (int)$data['employee_id'],
                $salaryAmount,
                $amountReceived,
                $data['description'] ?? null,
                $data['logs'] ?? null
            ));
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'description' => 'Validation failed',
                'errors' => [$e->getMessage()]
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'description' => 'Success',
            'data' => null
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            if (!isset($args['id'])) {
                throw new \RuntimeException('ID is required');
            }

            $data = (array)$request->getParsedBody();
            $id = (int)$args['id'];

            // Convert numeric fields to integers if they exist
            $salaryAmount = isset($data['salary_amount']) ? (int)$data['salary_amount'] : null;
            $amountReceived = isset($data['amount_received']) ? (int)$data['amount_received'] : null;

            $this->editSalaryHandler->handle(new EditSalary(
                $id,
                $salaryAmount,
                $amountReceived,
                $data['description'] ?? null,
                $data['logs'] ?? null
            ));

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'description' => 'Success',
                'data' => null
            ]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\RuntimeException $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'description' => $e->getMessage(),
                'data' => null
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->deleteSalaryHandler->handle(new DeleteSalary((int)$args['id']));

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'description' => 'Success',
            'data' => null
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
