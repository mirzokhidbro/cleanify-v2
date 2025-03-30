<?php

declare(strict_types=1);

namespace App\Application\Salary\Query;

class GetSalaryListQuery
{
    public function __construct(
        public readonly ?string $companyId = null,
        public readonly ?int $employeeId = null
    ) {}
}
