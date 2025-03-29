<?php

declare(strict_types=1);

namespace App\Domain\LeadStatus;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'lead_statuses')]
class LeadStatus
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\Column(name: 'company_id', type: 'string', length: 255)]
        private string $companyId,

        #[ORM\Column(name: '"order"', type: 'integer')]
        private int $sortOrder,

        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private ?int $id = null
    ) {}

    public static function create(string $name, string $companyId, int $sortOrder): self
    {
        return new self($name, $companyId, $sortOrder);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }



    public function isNew(): bool
    {
        return $this->id === null;
    }

    public function edit(string $name): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
        
        $this->name = $name;
    }
}
