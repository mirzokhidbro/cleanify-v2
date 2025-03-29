<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Query;

class GetLeadStatusListQuery
{
    public function __construct(
        public readonly string $companyId
    ) {}
}
