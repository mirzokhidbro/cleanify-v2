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
    public int $id;

    #[ORM\Column(name: 'model_type')]
    public string $entityType = 'lead';

    #[ORM\ManyToOne(targetEntity: Lead::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id')]
    public ?Lead $lead = null;

    #[ORM\Column(name: 'model_id', insertable: false, updatable: false)]
    public ?int $entityId = null;

    #[ORM\Column(name: 'message')]
    public string $content;

    #[ORM\Column(name: 'type')]
    public string $type;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;

    public function __construct()
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
}
