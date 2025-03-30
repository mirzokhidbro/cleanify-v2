<?php

declare(strict_types=1);

namespace App\Application\Employee\Command;

use App\Domain\Employee\EmployeeRepository;

class DeleteEmployeeHandler
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository
    ) {}

    public function handle(DeleteEmployee $command): void
    {
        $this->employeeRepository->delete($command->id);
    }
}
