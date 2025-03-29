<?php

declare(strict_types=1);

namespace App\Application\Lead\Command;

class CreateLead
{
    public function __construct(
        public readonly string $companyId,
        public readonly string $phoneNumber,
        public readonly int $status,
        public readonly ?string $name = null,
        public readonly ?string $address = null,
        public readonly ?string $source = null,
        public readonly ?string $comment = null
    ) {}
}
