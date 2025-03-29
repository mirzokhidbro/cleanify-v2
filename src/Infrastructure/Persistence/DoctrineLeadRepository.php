<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Lead\Lead;
use App\Domain\Lead\LeadRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineLeadRepository implements LeadRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function findById(int $id): ?Lead
    {
        return $this->entityManager->find(Lead::class, $id);
    }

    public function findByCompanyId(
        string $companyId,
        ?string $phone = null,
        ?int $status = null
    ): array {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('l')
            ->from(Lead::class, 'l')
            ->where('l.companyId = :companyId')
            ->setParameter('companyId', $companyId);

        if ($phone !== null) {
            $qb->andWhere('l.phoneNumber LIKE :phone')
                ->setParameter('phone', "%$phone%");
        }

        if ($status !== null) {
            $qb->andWhere('l.status = :status')
                ->setParameter('status', $status);
        }

        $qb->orderBy('l.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function save(Lead $lead): void
    {
        $this->entityManager->persist($lead);
        $this->entityManager->flush();
    }

    public function delete(Lead $lead): void
    {
        $this->entityManager->remove($lead);
        $this->entityManager->flush();
    }
}
