<?php

declare(strict_types=1);

namespace App\Application\Lead\Command;

use App\Domain\Lead\LeadRepositoryInterface;
use RuntimeException;

class DeleteLeadHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository
    ) {}

    public function handle(DeleteLead $command): void
    {
        $lead = $this->repository->findById($command->id)
            ?? throw new RuntimeException('Lead not found');

        $this->repository->delete($lead);
    }
}
