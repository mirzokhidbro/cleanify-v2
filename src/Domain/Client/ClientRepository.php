<?php

declare(strict_types=1);

namespace App\Domain\Client;

use App\Core\Database\Connection;

class ClientRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM clients WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
