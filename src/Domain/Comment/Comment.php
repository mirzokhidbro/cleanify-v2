<?php

declare(strict_types=1);

namespace App\Domain\Comment;

use App\Domain\Lead\Lead;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'comments')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(name: 'model_type')]
    private string $entityType = 'lead';

    #[ORM\ManyToOne(targetEntity: Lead::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id')]
    private ?Lead $lead = null;

    #[ORM\Column(name: 'model_id', insertable: false, updatable: false)]
    private ?int $entityId = null;

    #[ORM\Column(name: 'message')]
    private string $content;

    #[ORM\Column(name: 'type')]
    private string $type;

    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;

    private function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Lead $lead,
        string $content,
        string $type = 'text'
    ): self {
        $comment = new self();
        $comment->lead = $lead;
        $comment->content = $content;
        $comment->type = $type;

        return $comment;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
