<?php

declare(strict_types=1);

namespace App\Application\OrderItemType\Controller;

use App\Application\OrderItemType\Command\DeleteOrderItemType;
use App\Application\OrderItemType\Command\DeleteOrderItemTypeHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OrderItemTypeController
{
    public function __construct(
        private readonly DeleteOrderItemTypeHandler $deleteOrderItemTypeHandler
    ) {}

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->deleteOrderItemTypeHandler->handle(new DeleteOrderItemType((int)$args["id"]));

        $response->getBody()->write(json_encode([
            "status" => "success",
            "description" => "Success",
            "data" => null
        ]));

        return $response->withHeader("Content-Type", "application/json");
    }
}
