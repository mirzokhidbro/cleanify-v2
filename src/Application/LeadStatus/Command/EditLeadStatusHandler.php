<?php

declare(strict_types=1);

namespace App\Application\LeadStatus\Command;

use App\Domain\LeadStatus\LeadStatusRepositoryInterface;
use RuntimeException;

class EditLeadStatusHandler
{
    public function __construct(
        private readonly LeadStatusRepositoryInterface $repository
    ) {}

    public function handle(EditLeadStatus $command): void
    {
        $leadStatus = $this->repository->findById($command->id)
            ?? throw new RuntimeException('Lead status not found');

        $leadStatus->edit($command->name, $command->color);
        
        $this->repository->save($leadStatus);
    }
}
