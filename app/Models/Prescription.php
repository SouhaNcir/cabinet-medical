<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;
use PDOException;

class Prescription extends Model
{
    public function create(array $data): bool
    {
        $payload = [
            'consultation_id' => (int) ($data['consultation_id'] ?? 0),
            'medications_json' => (string) ($data['medications_json'] ?? '[]'),
            'notes' => $data['notes'] ?? null,
        ];

        try {
            return $this->insertPrescription($payload);
        } catch (PDOException $exception) {
            if (str_contains($exception->getMessage(), 'MySQL server has gone away') || str_contains($exception->getMessage(), 'server has gone away')) {
                Database::resetInstance();
                $this->db = Database::getInstance();

                return $this->insertPrescription($payload);
            }

            throw $exception;
        }
    }

    private function insertPrescription(array $payload): bool
    {
        $stmt = $this->db->prepare('INSERT INTO prescriptions (consultation_id, medications_json, notes)
                                    VALUES (:consultation_id, :medications_json, :notes)');

        return $stmt->execute($payload);
    }

    public function all(): array
    {
        $sql = 'SELECT pr.id, pr.consultation_id, pr.created_at,
                       c.consultation_date,
                       p.full_name AS patient_name,
                       u.full_name AS doctor_name
                FROM prescriptions pr
                INNER JOIN consultations c ON c.id = pr.consultation_id
                INNER JOIN patients p ON p.id = c.patient_id
                INNER JOIN users u ON u.id = c.doctor_id
                ORDER BY pr.created_at DESC';

        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT pr.*, c.consultation_date, c.symptoms, c.diagnosis, c.treatment,
                       p.full_name AS patient_name, p.birth_date, p.gender,
                       u.full_name AS doctor_name
                FROM prescriptions pr
                INNER JOIN consultations c ON c.id = pr.consultation_id
                INNER JOIN patients p ON p.id = c.patient_id
                INNER JOIN users u ON u.id = c.doctor_id
                WHERE pr.id = :id LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM prescriptions WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}
