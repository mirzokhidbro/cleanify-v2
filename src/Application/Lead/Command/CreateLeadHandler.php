<?php

declare(strict_types=1);

namespace App\Application\Lead\Command;

use App\Domain\Lead\Lead;
use App\Domain\Lead\LeadRepositoryInterface;

class CreateLeadHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository
    ) {}

    public function handle(CreateLead $command): void
    {
        $lead = Lead::create(
            $command->companyId,
            $command->phoneNumber,
            $command->status,
            $command->name,
            $command->address,
            $command->source
        );

        $this->repository->save($lead);
    }
}
