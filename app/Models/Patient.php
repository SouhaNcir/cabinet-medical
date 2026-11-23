<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Patient extends Model
{
    public function allForSelect(): array
    {
        return $this->db->query('SELECT id, full_name FROM patients ORDER BY full_name ASC')->fetchAll();
    }

    public function paginate(int $page, int $perPage, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = ' WHERE full_name LIKE :search OR cin LIKE :search OR phone LIKE :search ';
            $params['search'] = '%' . $search . '%';
        }

        $countSql = 'SELECT COUNT(*) AS total FROM patients' . $where;
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];

        $sql = 'SELECT id, full_name, cin, phone, email, gender, birth_date, address, created_at
                FROM patients' . $where . '
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll();
        $totalPages = max(1, (int) ceil($total / $perPage));

        return [
            'data' => $data,
            'total' => $total,
            'page' => min($page, $totalPages),
            'totalPages' => $totalPages,
        ];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM patients WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $patient = $stmt->fetch();

        return $patient ?: null;
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO patients (full_name, cin, phone, email, gender, birth_date, address)
                VALUES (:full_name, :cin, :phone, :email, :gender, :birth_date, :address)';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE patients
                SET full_name = :full_name,
                    cin = :cin,
                    phone = :phone,
                    email = :email,
                    gender = :gender,
                    birth_date = :birth_date,
                    address = :address
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array_merge($data, ['id' => $id]));
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM patients WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    public function getGenderStats(): array
    {
        $sql = 'SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN gender = "M" THEN 1 ELSE 0 END) AS men,
                    SUM(CASE WHEN gender = "F" THEN 1 ELSE 0 END) AS women
                FROM patients';

        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();

        return [
            'total' => (int) ($row['total'] ?? 0),
            'men' => (int) ($row['men'] ?? 0),
            'women' => (int) ($row['women'] ?? 0),
        ];
    }
}
