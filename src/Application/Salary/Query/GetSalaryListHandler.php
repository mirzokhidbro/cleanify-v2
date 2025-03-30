<?php

declare(strict_types=1);

namespace App\Application\Salary\Query;

use App\Domain\Salary\SalaryRepositoryInterface;

class GetSalaryListHandler
{
    public function __construct(private readonly SalaryRepositoryInterface $repository) {}

    public function handle(GetSalaryListQuery $query): array
    {
        $salaries = $this->repository->findAll($query->companyId, $query->employeeId);

        return array_map(function ($salary) {
            return [
                'id' => $salary->getId(),
                'company_id' => $salary->getCompanyId(),
                'employee_id' => $salary->getEmployeeId(),
                'salary_amount' => $salary->getSalaryAmount(),
                'amount_received' => $salary->getAmountReceived(),
                'description' => $salary->getDescription(),
                'logs' => $salary->getLogs(),
                'is_edited' => $salary->isEdited(),
                'is_deleted' => $salary->isDeleted(),
                'created_at' => $salary->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }, $salaries);
    }
}
