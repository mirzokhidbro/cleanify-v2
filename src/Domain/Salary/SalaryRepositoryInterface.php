<?php

declare(strict_types=1);

namespace App\Domain\Salary;

interface SalaryRepositoryInterface
{
    public function findAll(?string $companyId = null, ?int $employeeId = null): array;
    public function findById(int $id): ?Salary;
    public function save(Salary $salary): void;
    public function delete(Salary $salary): void;
}
