<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function save(Comment $comment): void
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function findByEntity(string $entityType, int $entityId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Comment::class, 'c')
            ->where('c.entityType = :entityType')
            ->andWhere('c.entityId = :entityId')
            ->setParameter('entityType', $entityType)
            ->setParameter('entityId', $entityId)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
