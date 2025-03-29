<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\LeadStatus\LeadStatus;
use App\Domain\LeadStatus\LeadStatusRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineLeadStatusRepository implements LeadStatusRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function findById(int $id): ?LeadStatus
    {
        return $this->entityManager->find(LeadStatus::class, $id);
    }
    
    public function findByCompanyId(string $companyId): array
    {
        return $this->entityManager
            ->getRepository(LeadStatus::class)
            ->findBy(['companyId' => $companyId]);
    }

    public function save(LeadStatus $status): void
    {
        $this->entityManager->persist($status);
        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $status = $this->findById($id);
        if ($status) {
            $this->entityManager->remove($status);
            $this->entityManager->flush();
        }
    }
}
