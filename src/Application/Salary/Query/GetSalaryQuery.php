<?php

declare(strict_types=1);

namespace App\Application\Salary\Query;

class GetSalaryQuery
{
    public function __construct(public readonly int $id) {}
}
