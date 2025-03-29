<?php

declare(strict_types=1);

namespace App\Application\Lead\Command;

use App\Domain\Lead\LeadRepositoryInterface;
use RuntimeException;

class EditLeadHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository
    ) {}

    public function handle(EditLead $command): void
    {
        $lead = $this->repository->findById($command->id)
            ?? throw new RuntimeException('Lead not found');

        $lead->edit(
            $command->name,
            $command->address,
            $command->phoneNumber,
            $command->source,
            $command->status
        );

        $this->repository->save($lead);
    }
}
