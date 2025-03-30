<?php

declare(strict_types=1);

namespace App\Application\Salary\Command;

use App\Domain\Salary\SalaryRepositoryInterface;

class DeleteSalaryHandler
{
    public function __construct(private readonly SalaryRepositoryInterface $repository) {}

    public function handle(DeleteSalary $command): void
    {
        $salary = $this->repository->findById($command->id);

        if ($salary === null) {
            throw new \RuntimeException('Salary not found');
        }

        $this->repository->delete($salary);
    }
}
