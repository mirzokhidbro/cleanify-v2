<?php

declare(strict_types=1);

namespace App\Application\Employee\Command;

class EditEmployee
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name,
        public readonly ?string $phone,
        public readonly ?string $role,
        public readonly ?string $description,
        public readonly ?string $telegramId
    ) {}
}
