<?php

declare(strict_types=1);

namespace App\Domain\Comment;

use App\Core\Database\Connection;
use App\Domain\Lead\Lead;

class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function findAll(): array
    {
        $sql = 'SELECT * FROM comments ORDER BY created_at DESC';
        $data = $this->connection->query($sql)->fetchAll();
        return array_map([$this, 'hydrate'], $data);
    }

    public function findByEntity(string $entity, int $entityId): array
    {
        $sql = 'SELECT * FROM comments WHERE model_type = :entity AND model_id = :entity_id ORDER BY created_at DESC';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'entity' => $entity,
            'entity_id' => $entityId
        ]);
        $data = $stmt->fetchAll();
        return array_map([$this, 'hydrate'], $data);
    }

    public function findById(int $id): ?Comment
    {
        $sql = 'SELECT * FROM comments WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function save(Comment $comment): void
    {
        $sql = 'INSERT INTO comments (model_type, model_id, message, type, created_at) 
               VALUES (:model_type, :model_id, :message, :type, :created_at)
               ON CONFLICT (id) DO UPDATE SET 
               model_type = EXCLUDED.model_type,
               model_id = EXCLUDED.model_id,
               message = EXCLUDED.message,
               type = EXCLUDED.type';

        $this->connection->prepare($sql)->execute([
            'model_type' => $comment->entityType,
            'model_id' => $comment->entityId,
            'message' => $comment->content,
            'type' => $comment->type,
            'created_at' => $comment->createdAt->format('Y-m-d H:i:s')
        ]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM comments WHERE id = :id';
        $this->connection->prepare($sql)->execute(['id' => $id]);
    }

    private function hydrate(array $data): Comment
    {
        $comment = new Comment();
        $comment->id = (int)$data['id'];
        $comment->entityType = $data['model_type'];
        $comment->entityId = (int)$data['model_id'];
        $comment->content = $data['message'];
        $comment->type = $data['type'];
        $comment->createdAt = new \DateTimeImmutable($data['created_at']);
        
        return $comment;
    }
}
