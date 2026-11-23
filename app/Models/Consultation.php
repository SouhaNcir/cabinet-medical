<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Consultation extends Model
{
    public function create(array $data): bool
    {
        // Insert without payment fields - DB has defaults for payment_status and amount
        $sql = 'INSERT INTO consultations (patient_id, doctor_id, symptoms, diagnosis, treatment, consultation_date)
            VALUES (:patient_id, :doctor_id, :symptoms, :diagnosis, :treatment, :consultation_date)';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    public function historyByPatient(int $patientId): array
    {
        $sql = 'SELECT c.*, p.full_name AS patient_name, u.full_name AS doctor_name
                FROM consultations c
                INNER JOIN patients p ON p.id = c.patient_id
                INNER JOIN users u ON u.id = c.doctor_id
                WHERE c.patient_id = :patient_id
                ORDER BY c.consultation_date DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['patient_id' => $patientId]);

        return $stmt->fetchAll();
    }

    public function latest(int $limit = 20): array
    {
        $stmt = $this->db->prepare('SELECT c.*, p.full_name AS patient_name, u.full_name AS doctor_name
                                    FROM consultations c
                                    INNER JOIN patients p ON p.id = c.patient_id
                                    INNER JOIN users u ON u.id = c.doctor_id
                                    ORDER BY c.consultation_date DESC
                                    LIMIT :limit');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM consultations WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findDetailedById(int $id): ?array
    {
        $sql = 'SELECT c.*, p.full_name AS patient_name, p.cin, p.phone, p.birth_date, p.gender,
                       u.full_name AS doctor_name
                FROM consultations c
                INNER JOIN patients p ON p.id = c.patient_id
                INNER JOIN users u ON u.id = c.doctor_id
                WHERE c.id = :id
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function updatePaymentStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE consultations SET payment_status = :status WHERE id = :id');

        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM consultations WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}
