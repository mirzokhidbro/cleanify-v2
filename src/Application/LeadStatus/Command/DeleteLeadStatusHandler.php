<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Command;

use App\Domain\LeadStatus\LeadStatusRepositoryInterface;
use RuntimeException;

class DeleteLeadStatusHandler
{
    public function __construct(
        private readonly LeadStatusRepositoryInterface $repository
    ) {}

    public function handle(DeleteLeadStatus $command): void
    {
        $leadStatus = $this->repository->findById($command->id)
            ?? throw new RuntimeException('Lead status not found');

        $this->repository->delete($leadStatus->id);
    }
}
