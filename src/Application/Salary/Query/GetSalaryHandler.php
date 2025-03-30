<?php

declare(strict_types=1);

namespace App\Application\Salary\Query;

use App\Domain\Salary\SalaryRepositoryInterface;

class GetSalaryHandler
{
    public function __construct(private readonly SalaryRepositoryInterface $repository) {}

    public function handle(GetSalaryQuery $query): array
    {
        $salary = $this->repository->findById($query->id);

        if ($salary === null) {
            throw new \RuntimeException('Salary not found');
        }

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
    }
}
