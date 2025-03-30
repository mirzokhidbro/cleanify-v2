<?php

declare(strict_types=1);

namespace App\Domain\OrderItemType;

use App\Core\Database\Connection;

class OrderItemTypeRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM order_item_types WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
