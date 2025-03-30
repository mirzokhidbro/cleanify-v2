<?php

declare(strict_types=1);

namespace App\Domain\Lead;

use App\Domain\Comment\Comment;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: 'leads')]
class Lead
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'company_id')]
    public string $companyId;

    #[ORM\Column(nullable: true)]
    public ?string $name = null;

    #[ORM\Column(nullable: true)]
    public ?string $address = null;

    #[ORM\Column(name: 'phone_number')]
    public string $phoneNumber;

    #[ORM\Column(nullable: true)]
    public ?string $source = null;

    #[ORM\Column]
    public int $status;

    #[ORM\Column(name: 'created_at', nullable: true)]
    public ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'lead')]
    public Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        string $companyId,
        string $phoneNumber,
        int $status,
        ?string $name = null,
        ?string $address = null,
        ?string $source = null
    ): self {
        $lead = new self();
        $lead->companyId = $companyId;
        $lead->phoneNumber = $phoneNumber;
        $lead->status = $status;
        $lead->name = $name;
        $lead->address = $address;
        $lead->source = $source;

        return $lead;
    }

    public function edit(
        ?string $name,
        ?string $address,
        ?string $phoneNumber,
        ?string $source,
        ?int $status
    ): void {
        if ($name !== null) {
            $this->name = $name;
        }
        if ($address !== null) {
            $this->address = $address;
        }
        if ($phoneNumber !== null) {
            $this->phoneNumber = $phoneNumber;
        }
        if ($source !== null) {
            $this->source = $source;
        }
        if ($status !== null && $status !== $this->status) {
            $this->status = $status;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }


}
