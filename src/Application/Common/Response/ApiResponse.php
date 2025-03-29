<?php

declare(strict_types=1);

namespace App\Application\Common\Response;

class ApiResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $description,
        public readonly mixed $data
    ) {}

    public static function success(mixed $data, string $description = 'Success'): self
    {
        return new self('success', $description, $data);
    }

    public static function error(string $description, mixed $data = null): self
    {
        return new self('error', $description, $data);
    }
}
