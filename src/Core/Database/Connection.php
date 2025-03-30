<?php

declare(strict_types=1);

namespace App\Core\Database;

use PDO;

class Connection extends PDO
{
    public function __construct(
        private readonly string $dsn,
        private readonly string $username,
        private readonly string $password
    ) {
        parent::__construct($dsn, $username, $password);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}
