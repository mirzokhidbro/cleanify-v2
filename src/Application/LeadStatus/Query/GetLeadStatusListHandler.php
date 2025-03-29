<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Query;

use App\Domain\LeadStatus\LeadStatusRepositoryInterface;

class GetLeadStatusListHandler
{
    public function __construct(
        private readonly LeadStatusRepositoryInterface $repository
    ) {}

    /**
     * @return LeadStatusDTO[]
     */
    public function handle(GetLeadStatusListQuery $query): array
    {
        $statuses = $this->repository->findByCompanyId($query->companyId);
        
        return array_map(
            fn($status) => LeadStatusDTO::fromEntity($status),
            $statuses
        );
    }
}
