<?php

declare(strict_types=1);

namespace App\Application\Lead\Query;

class GetLeadListQuery
{
    public function __construct(
        public readonly string $companyId,
        public readonly ?string $phone = null,
        public readonly ?int $status = null
    ) {}
}
