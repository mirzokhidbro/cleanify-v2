<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Query;

/**
 * Query object for retrieving a single lead status
 */
class GetLeadStatus
{
    public function __construct(
        public readonly int $id
    ) {}
}
