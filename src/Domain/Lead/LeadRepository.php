<?php

declare(strict_types=1);

namespace App\Domain\Lead;

use App\Core\Database\Connection;

class LeadRepository implements LeadRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    public function findAll(): array
    {
        $sql = 'SELECT * FROM leads ORDER BY created_at DESC';
        return $this->connection->query($sql)->fetchAll();
    }

    public function findByCompanyId(
        string $companyId,
        ?string $phone = null,
        ?int $status = null
    ): array {
        $sql = 'SELECT * FROM leads WHERE company_id = :company_id';
        $params = ['company_id' => $companyId];

        if ($phone !== null) {
            $sql .= ' AND phone_number LIKE :phone';
            $params['phone'] = '%' . $phone . '%';
        }

        if ($status !== null) {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?Lead
    {
        $sql = 'SELECT * FROM leads WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function save(Lead $lead): void
    {
        $sql = 'INSERT INTO leads (company_id, name, address, phone_number, source, status, created_at) 
               VALUES (:company_id, :name, :address, :phone_number, :source, :status, :created_at)
               ON CONFLICT (id) DO UPDATE SET 
               company_id = EXCLUDED.company_id,
               name = EXCLUDED.name,
               address = EXCLUDED.address,
               phone_number = EXCLUDED.phone_number,
               source = EXCLUDED.source,
               status = EXCLUDED.status';

        $this->connection->prepare($sql)->execute([
            'company_id' => $lead->companyId,
            'name' => $lead->name,
            'address' => $lead->address,
            'phone_number' => $lead->phoneNumber,
            'source' => $lead->source,
            'status' => $lead->status,
            'created_at' => $lead->createdAt?->format('Y-m-d H:i:s')
        ]);
    }

    public function delete(Lead $lead): void
    {
        $sql = 'DELETE FROM leads WHERE id = :id';
        $this->connection->prepare($sql)->execute(['id' => $lead->id]);
    }

    private function hydrate(array $data): Lead
    {
        $lead = new Lead();
        $lead->id = (int)$data['id'];
        $lead->companyId = $data['company_id'];
        $lead->name = $data['name'];
        $lead->address = $data['address'];
        $lead->phoneNumber = $data['phone_number'];
        $lead->source = $data['source'];
        $lead->status = (int)$data['status'];
        $lead->createdAt = $data['created_at'] ? new \DateTimeImmutable($data['created_at']) : null;

        return $lead;
    }
}
