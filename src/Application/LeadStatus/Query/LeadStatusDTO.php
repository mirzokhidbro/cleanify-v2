<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Query;

class LeadStatusDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $color = null
    ) {}

    public static function fromEntity(\App\Domain\LeadStatus\LeadStatus $status): self
    {
        return new self(
            $status->id ?? 0,
            $status->name,
            $status->color
        );
    }
}
