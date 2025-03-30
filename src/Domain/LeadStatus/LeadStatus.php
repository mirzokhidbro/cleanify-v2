<?php

declare(strict_types=1);

namespace App\Domain\LeadStatus;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'lead_statuses')]
class LeadStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    public string $name;

    #[ORM\Column(name: 'company_id', type: 'string', length: 255)]
    public string $companyId;

    #[ORM\Column(name: 'color', type: 'string', length: 255, nullable: true)]
    public ?string $color = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('Asia/Tashkent'));
    }

    public static function create(
        string $name,
        string $companyId
    ): self {
        $status = new self();
        $status->name = $name;
        $status->companyId = $companyId;

        return $status;
    }

    public function edit(
        string $name,
        ?string $color = null
    ): void {
        $this->name = $name;
        $this->color = $color;
    }
}
