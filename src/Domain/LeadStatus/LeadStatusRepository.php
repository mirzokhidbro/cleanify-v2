<?php

declare(strict_types=1);

namespace App\Domain\LeadStatus;

use App\Core\Database\Connection;

class LeadStatusRepository implements LeadStatusRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function findAll(): array
    {
        $sql = 'SELECT * FROM lead_statuses ORDER BY id ASC';
        $data = $this->connection->query($sql)->fetchAll();
        return array_map([$this, 'hydrate'], $data);
    }

    public function findByCompanyId(string $companyId): array
    {
        $sql = 'SELECT * FROM lead_statuses WHERE company_id = :company_id ORDER BY id ASC';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['company_id' => $companyId]);
        $data = $stmt->fetchAll();
        return array_map([$this, 'hydrate'], $data);
    }

    public function findById(int $id): ?LeadStatus
    {
        $sql = 'SELECT * FROM lead_statuses WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function save(LeadStatus $status): void
    {
        $sql = 'INSERT INTO lead_statuses (company_id, name, created_at) 
               VALUES (:company_id, :name, :created_at)
               ON CONFLICT (id) DO UPDATE SET 
               company_id = EXCLUDED.company_id,
               name = EXCLUDED.name';

        $this->connection->prepare($sql)->execute([
            'company_id' => $status->companyId,
            'name' => $status->name,
            'created_at' => $status->createdAt->format('Y-m-d H:i:s')
        ]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM lead_statuses WHERE id = :id';
        $this->connection->prepare($sql)->execute(['id' => $id]);
    }

    private function hydrate(array $data): LeadStatus
    {
        $status = new LeadStatus();
        $status->id = (int)$data['id'];
        $status->name = $data['name'];
        $status->companyId = $data['company_id'];
        $status->color = $data['color'] ?? null;
        $status->createdAt = new \DateTimeImmutable($data['created_at']);
        
        return $status;
    }
}
