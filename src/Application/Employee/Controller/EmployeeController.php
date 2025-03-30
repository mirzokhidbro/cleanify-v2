<?php

declare(strict_types=1);

namespace App\Application\Employee\Controller;

use App\Application\Employee\Command\DeleteEmployee;
use App\Application\Employee\Command\DeleteEmployeeHandler;
use App\Application\Employee\Command\EditEmployee;
use App\Application\Employee\Command\EditEmployeeHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EmployeeController
{
    public function __construct(
        private readonly EditEmployeeHandler $editEmployeeHandler,
        private readonly DeleteEmployeeHandler $deleteEmployeeHandler
    ) {}

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            if (!isset($args["id"])) {
                throw new \RuntimeException("ID is required");
            }

            $data = (array)$request->getParsedBody();
            $id = (int)$args["id"];

            $this->editEmployeeHandler->handle(new EditEmployee(
                $id,
                $data["name"] ?? null,
                $data["phone"] ?? null,
                $data["role"] ?? null,
                $data["description"] ?? null,
                $data["telegram_id"] ?? null
            ));

            $response->getBody()->write(json_encode([
                "status" => "success",
                "description" => "Success",
                "data" => null
            ]));

            return $response->withHeader("Content-Type", "application/json");
        } catch (\RuntimeException $e) {
            $response->getBody()->write(json_encode([
                "status" => "error",
                "description" => $e->getMessage(),
                "data" => null
            ]));

            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(404);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->deleteEmployeeHandler->handle(new DeleteEmployee((int)$args["id"]));

        $response->getBody()->write(json_encode([
            "status" => "success",
            "description" => "Success",
            "data" => null
        ]));

        return $response->withHeader("Content-Type", "application/json");
    }
}
