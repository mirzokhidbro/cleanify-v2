<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Command;

use App\Domain\LeadStatus\LeadStatus;
use App\Domain\LeadStatus\LeadStatusRepositoryInterface;

class CreateLeadStatusHandler
{
    public function __construct(
        private readonly LeadStatusRepositoryInterface $repository
    ) {}

    public function handle(CreateLeadStatus $command): void
    {
        $status = LeadStatus::create(
            $command->name,
            $command->companyId,
            $command->order
        );

        $this->repository->save($status);
    }
}
