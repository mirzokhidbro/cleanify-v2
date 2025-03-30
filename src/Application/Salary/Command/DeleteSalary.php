<?php

declare(strict_types=1);

namespace App\Application\Salary\Command;

class DeleteSalary
{
    public function __construct(public readonly int $id) {}
}
