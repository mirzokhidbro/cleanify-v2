<?php

declare(strict_types=1);

namespace App\Application\Client\Command;

use App\Domain\Client\ClientRepository;

class DeleteClientHandler
{
    public function __construct(
        private readonly ClientRepository $clientRepository
    ) {}

    public function handle(DeleteClient $command): void
    {
        $this->clientRepository->delete($command->id);
    }
}
