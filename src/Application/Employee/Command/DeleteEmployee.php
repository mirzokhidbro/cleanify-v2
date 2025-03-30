<?php

declare(strict_types=1);

namespace App\Application\Employee\Command;

class DeleteEmployee
{
    public function __construct(
        public readonly int $id
    ) {}
}
