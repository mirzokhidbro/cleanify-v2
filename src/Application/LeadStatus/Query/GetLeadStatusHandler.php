<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Query;

use App\Domain\LeadStatus\LeadStatusRepositoryInterface;
use RuntimeException;

class GetLeadStatusHandler
{
    public function __construct(
        private readonly LeadStatusRepositoryInterface $repository
    ) {}

    public function handle(GetLeadStatus $query): LeadStatusDTO
    {
        $status = $this->repository->findById($query->id);

        if (!$status) {
            throw new RuntimeException("Lead status not found");
        }

        return LeadStatusDTO::fromEntity($status);
    }
}
