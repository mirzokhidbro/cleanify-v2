<?php

declare(strict_types=1);

namespace App\Application\Client\Controller;

use App\Application\Client\Command\DeleteClient;
use App\Application\Client\Command\DeleteClientHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ClientController
{
    public function __construct(
        private readonly DeleteClientHandler $deleteClientHandler
    ) {}

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->deleteClientHandler->handle(new DeleteClient((int)$args["id"]));

        $response->getBody()->write(json_encode([
            "status" => "success",
            "description" => "Success",
            "data" => null
        ]));

        return $response->withHeader("Content-Type", "application/json");
    }
}
