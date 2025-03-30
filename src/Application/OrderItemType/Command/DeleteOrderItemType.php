<?php

declare(strict_types=1);

namespace App\Application\OrderItemType\Command;

class DeleteOrderItemType
{
    public function __construct(
        public readonly int $id
    ) {}
}
