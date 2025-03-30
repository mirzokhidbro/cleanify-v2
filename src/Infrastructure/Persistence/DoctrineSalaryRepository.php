<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Salary\Salary;
use App\Domain\Salary\SalaryRepositoryInterface;
use Doctrine\ORM\EntityManager;

class DoctrineSalaryRepository implements SalaryRepositoryInterface
{
    public function __construct(private readonly EntityManager $entityManager) {}

    public function findAll(?string $companyId = null, ?int $employeeId = null): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('s')
           ->from(Salary::class, 's');

        if ($companyId !== null) {
            $qb->andWhere('s.companyId = :companyId')
               ->setParameter('companyId', $companyId);
        }

        if ($employeeId !== null) {
            $qb->andWhere('s.employeeId = :employeeId')
               ->setParameter('employeeId', $employeeId);
        }

        $qb->orderBy('s.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findById(int $id): ?Salary
    {
        return $this->entityManager->find(Salary::class, $id);
    }

    public function save(Salary $salary): void
    {
        $this->entityManager->persist($salary);
        $this->entityManager->flush();
    }

    public function delete(Salary $salary): void
    {
        $this->entityManager->remove($salary);
        $this->entityManager->flush();
    }
}
