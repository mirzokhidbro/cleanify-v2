<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Command;

class EditLeadStatus
{
    public function __construct(
        public readonly int $id,
        public readonly string $name
    ) {}
}
