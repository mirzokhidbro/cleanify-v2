<?php

declare(strict_types=1);

namespace App\Domain\Comment;

interface CommentRepositoryInterface
{
    public function save(Comment $comment): void;
    
    public function findByEntity(string $entityType, int $entityId): array;
}
