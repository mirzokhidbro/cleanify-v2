<?php

declare(strict_types=1);

namespace App\Application\Lead\Query;

use App\Domain\Lead\LeadRepositoryInterface;
use RuntimeException;

class GetLeadHandler
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository
    ) {}

    public function handle(GetLeadQuery $query): array
    {
        $lead = $this->repository->findById($query->id)
            ?? throw new RuntimeException('Lead not found');

        $comments = $lead->getComments();

        if ($comments instanceof \Doctrine\ORM\PersistentCollection) {
            $comments->initialize();
        }

        $createdAt = $lead->getCreatedAt();

        return [
            'id' => $lead->getId(),
            'company_id' => $lead->getCompanyId(),
            'name' => $lead->getName(),
            'address' => $lead->getAddress(),
            'phone_number' => $lead->getPhoneNumber(),
            'source' => $lead->getSource(),
            'status' => $lead->getStatus(),
            'created_at' => $createdAt instanceof \DateTimeImmutable ? $createdAt->format('Y-m-d H:i:s') : null,
            'comments' => array_map(function ($comment) {
                return [
                    'id' => $comment->getId(),
                    'content' => $comment->getContent(),
                    'type' => $comment->getType(),
                    'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }, $comments->toArray())
        ];
    }
}
