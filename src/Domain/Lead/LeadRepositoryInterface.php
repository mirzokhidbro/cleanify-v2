<?php

declare(strict_types=1);

namespace App\Domain\Lead;

interface LeadRepositoryInterface
{
    public function findById(int $id): ?Lead;
    
    public function findByCompanyId(
        string $companyId,
        ?string $phone = null,
        ?int $status = null
    ): array;
    
    public function save(Lead $lead): void;
    
    public function delete(Lead $lead): void;
}
