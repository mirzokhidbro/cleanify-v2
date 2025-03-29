<?php

declare(strict_types=1);

namespace App\Domain\LeadStatus;

interface LeadStatusRepositoryInterface
{
    public function findById(int $id): ?LeadStatus;
    public function findByCompanyId(string $companyId): array;
    public function save(LeadStatus $status): void;
    public function delete(int $id): void;
}
