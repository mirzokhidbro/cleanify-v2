<?php

declare(strict_types=1);

namespace App\Application\Lead\Command;

class AddComment
{
    public function __construct(
        public readonly int $leadId,
        public readonly string $comment
    ) {}
}
