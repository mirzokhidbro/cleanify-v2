<?php

declare(strict_types=1);

namespace App\Domain\Employee;

class Employee
{
    public function __construct(
        public readonly int $id,
        public string $name,
        public string $phone,
        public string $role,
        public ?string $description,
        public ?string $telegramId
    ) {}
}
