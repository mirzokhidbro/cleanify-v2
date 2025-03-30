<?php

declare(strict_types=1);

namespace App\Application\Employee\Command;

use App\Domain\Employee\EmployeeRepository;

class EditEmployeeHandler
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository
    ) {}

    public function handle(EditEmployee $command): void
    {
        $employee = $this->employeeRepository->getById($command->id);

        if ($command->name !== null) {
            $employee->name = $command->name;
        }
        if ($command->phone !== null) {
            $employee->phone = $command->phone;
        }
        if ($command->role !== null) {
            $employee->role = $command->role;
        }
        if ($command->description !== null) {
            $employee->description = $command->description;
        }
        if ($command->telegramId !== null) {
            $employee->telegramId = $command->telegramId;
        }

        $this->employeeRepository->save($employee);
    }
}
