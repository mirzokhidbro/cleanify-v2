<?php

declare(strict_types=1);

namespace App\Domain\Salary;

use App\Core\Database\Connection;

class SalaryRepository implements SalaryRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function findAll(?string $companyId = null, ?int $employeeId = null): array
    {
        $sql = 'SELECT * FROM salaries';
        $params = [];
        $conditions = [];

        if ($companyId !== null) {
            $conditions[] = 'company_id = :company_id';
            $params['company_id'] = $companyId;
        }

        if ($employeeId !== null) {
            $conditions[] = 'employee_id = :employee_id';
            $params['employee_id'] = $employeeId;
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return array_map([$this, 'hydrate'], $data);
    }

    public function findById(int $id): ?Salary
    {
        $sql = 'SELECT * FROM salaries WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function save(Salary $salary): void
    {
        $sql = 'INSERT INTO salaries (company_id, employee_id, salary_amount, amount_received, description, logs, created_at) 
               VALUES (:company_id, :employee_id, :salary_amount, :amount_received, :description, :logs, :created_at)
               ON CONFLICT (id) DO UPDATE SET 
               company_id = EXCLUDED.company_id,
               employee_id = EXCLUDED.employee_id,
               salary_amount = EXCLUDED.salary_amount,
               amount_received = EXCLUDED.amount_received,
               description = EXCLUDED.description,
               logs = EXCLUDED.logs';

        $this->connection->prepare($sql)->execute([
            'company_id' => $salary->companyId,
            'employee_id' => $salary->employeeId,
            'salary_amount' => $salary->salaryAmount,
            'amount_received' => $salary->amountReceived,
            'description' => $salary->description,
            'logs' => json_encode($salary->logs),
            'created_at' => $salary->createdAt->format('Y-m-d H:i:s')
        ]);
    }

    public function delete(Salary $salary): void
    {
        $sql = 'DELETE FROM salaries WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $salary->id]);
    }

    private function hydrate(array $data): Salary
    {
        $salary = new Salary();
        $salary->id = (int)$data['id'];
        $salary->companyId = $data['company_id'];
        $salary->employeeId = $data['employee_id'];
        $salary->salaryAmount = (float)$data['salary_amount'];
        $salary->amountReceived = (float)$data['amount_received'];
        $salary->description = $data['description'];
        $salary->logs = json_decode($data['logs'], true) ?? [];
        $salary->createdAt = new \DateTimeImmutable($data['created_at']);
        
        return $salary;
    }
}
