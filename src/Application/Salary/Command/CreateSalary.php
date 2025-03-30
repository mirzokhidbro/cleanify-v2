<?php

declare(strict_types=1);

namespace App\Application\Salary\Command;

class CreateSalary
{
    public function __construct(
        public readonly string $companyId,
        public readonly int $employeeId,
        public readonly ?int $salaryAmount = null,
        public readonly ?int $amountReceived = null,
        public readonly ?string $description = null,
        public readonly ?array $logs = null
    ) {}
}
