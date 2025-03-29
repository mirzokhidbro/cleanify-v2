<?php

declare(strict_types=1);

namespace App\Application\Lead\Command;

class EditLead
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name,
        public readonly ?string $address,
        public readonly ?string $phoneNumber,
        public readonly ?string $source,
        public readonly ?int $status
    ) {}
}
