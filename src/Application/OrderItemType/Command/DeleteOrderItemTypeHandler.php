<?php

declare(strict_types=1);

namespace App\Application\OrderItemType\Command;

use App\Domain\OrderItemType\OrderItemTypeRepository;

class DeleteOrderItemTypeHandler
{
    public function __construct(
        private readonly OrderItemTypeRepository $orderItemTypeRepository
    ) {}

    public function handle(DeleteOrderItemType $command): void
    {
        $this->orderItemTypeRepository->delete($command->id);
    }
}
