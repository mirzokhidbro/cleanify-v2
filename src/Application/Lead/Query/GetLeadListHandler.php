<?php

declare(strict_types=1);

namespace App\Application\Lead\Query;

use App\Domain\Lead\LeadRepositoryInterface;

class GetLeadListHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository
    ) {}

    public function handle(GetLeadListQuery $query): array
    {
        $leads = $this->repository->findByCompanyId(
            $query->companyId,
            $query->phone,
            $query->status
        );

        return array_map(function ($lead) {
            $createdAt = $lead->getCreatedAt();
            return [
                'id' => $lead->getId(),
                'company_id' => $lead->getCompanyId(),
                'name' => $lead->getName(),
                'address' => $lead->getAddress(),
                'phone_number' => $lead->getPhoneNumber(),
                'source' => $lead->getSource(),
                'status' => $lead->getStatus(),
                'created_at' => $createdAt instanceof \DateTimeImmutable ? $createdAt->format('Y-m-d H:i:s') : null
            ];
        }, $leads);
    }
}
