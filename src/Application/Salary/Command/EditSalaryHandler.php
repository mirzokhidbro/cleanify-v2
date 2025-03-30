<?php

declare(strict_types=1);

namespace App\Application\Salary\Command;

use App\Domain\Salary\SalaryRepositoryInterface;

class EditSalaryHandler
{
    public function __construct(private readonly SalaryRepositoryInterface $repository) {}

    public function handle(EditSalary $command): void
    {
        $salary = $this->repository->findById($command->id);

        if ($salary === null) {
            throw new \RuntimeException('Salary not found');
        }

        $salary->edit(
            $command->salaryAmount,
            $command->amountReceived,
            $command->description,
            $command->logs
        );

        $this->repository->save($salary);
    }
}
