<?php

declare(strict_types=1);

namespace App\Application\Salary\Command;

use App\Domain\Salary\Salary;
use App\Domain\Salary\SalaryRepositoryInterface;

class CreateSalaryHandler
{
    public function __construct(private readonly SalaryRepositoryInterface $repository) {}

    public function handle(CreateSalary $command): void
    {
        $salary = Salary::create(
            $command->companyId,
            $command->employeeId,
            $command->salaryAmount,
            $command->amountReceived,
            $command->description,
            $command->logs
        );

        $this->repository->save($salary);
    }
}
