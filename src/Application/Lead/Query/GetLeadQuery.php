<?php

declare(strict_types=1);

namespace App\Application\Lead\Query;

class GetLeadQuery
{
    public function __construct(
        public readonly int $id
    ) {}
}
