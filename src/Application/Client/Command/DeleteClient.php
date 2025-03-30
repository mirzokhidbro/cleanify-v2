<?php

declare(strict_types=1);

namespace App\Application\Client\Command;

class DeleteClient
{
    public function __construct(
        public readonly int $id
    ) {}
}
