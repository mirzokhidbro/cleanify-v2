<?php

declare(strict_types=1);

namespace App\Application\Lead\Command;

class DeleteLead
{
    public function __construct(
        public readonly int $id
    ) {}
}
