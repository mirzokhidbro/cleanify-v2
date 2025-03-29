<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Command;

/**
 * Data Transfer Object (DTO) for creating a lead status
 */
class CreateLeadStatus
{
    public function __construct(
        public readonly string $name,
        public readonly string $companyId,
        public readonly int $order
    ) {}
}
