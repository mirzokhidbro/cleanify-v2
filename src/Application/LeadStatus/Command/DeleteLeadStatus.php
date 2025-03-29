<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Command;

class DeleteLeadStatus
{
    public function __construct(
        public readonly int $id
    ) {}
}
