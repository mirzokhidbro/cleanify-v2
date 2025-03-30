<?php

declare(strict_types=1);

namespace App\Domain\Employee;

use App\Core\Database\Connection;

class EmployeeRepository
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM employees WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function getById(int $id): Employee
    {
        $sql = 'SELECT * FROM employees WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            throw new \RuntimeException('Employee not found');
        }

        return new Employee(
            $data['id'],
            $data['name'],
            $data['phone'],
            $data['role'],
            $data['description'],
            $data['telegram_id']
        );
    }

    public function save(Employee $employee): void
    {
        $sql = 'UPDATE employees SET 
            name = :name,
            phone = :phone,
            role = :role,
            description = :description,
            telegram_id = :telegram_id
            WHERE id = :id';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'id' => $employee->id,
            'name' => $employee->name,
            'phone' => $employee->phone,
            'role' => $employee->role,
            'description' => $employee->description,
            'telegram_id' => $employee->telegramId
        ]);
    }
}
