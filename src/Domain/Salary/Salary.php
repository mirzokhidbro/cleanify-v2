<?php

declare(strict_types=1);

namespace App\Domain\Salary;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'salaries')]
class Salary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'company_id', nullable: true)]
    public ?string $companyId = null;

    #[ORM\Column(name: 'employee_id', nullable: true)]
    public ?int $employeeId = null;

    #[ORM\Column(name: 'salary_amount', nullable: true)]
    public ?float $salaryAmount = null;

    #[ORM\Column(name: 'amount_received', nullable: true)]
    public ?float $amountReceived = null;

    #[ORM\Column(nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    public ?array $logs = [];

    #[ORM\Column(name: 'is_edited')]
    public bool $isEdited = false;

    #[ORM\Column(name: 'is_deleted')]
    public bool $isDeleted = false;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        string $companyId,
        int $employeeId,
        ?float $salaryAmount = null,
        ?float $amountReceived = null,
        ?string $description = null,
        ?array $logs = []
    ): self {
        if ($salaryAmount === null && $amountReceived === null) {
            throw new \InvalidArgumentException('Either salaryAmount or amountReceived must be provided');
        }

        $salary = new self();
        $salary->companyId = $companyId;
        $salary->employeeId = $employeeId;
        $salary->salaryAmount = $salaryAmount;
        $salary->amountReceived = $amountReceived;
        $salary->description = $description;
        $salary->logs = $logs;

        return $salary;
    }

    public function edit(
        ?float $salaryAmount = null,
        ?float $amountReceived = null,
        ?string $description = null,
        ?array $logs = null
    ): void {
        if ($salaryAmount !== null) {
            $this->salaryAmount = $salaryAmount;
        }
        if ($amountReceived !== null) {
            $this->amountReceived = $amountReceived;
        }
        if ($description !== null) {
            $this->description = $description;
        }
        if ($logs !== null) {
            $this->logs = $logs;
        }
        $this->isEdited = true;
    }
}
