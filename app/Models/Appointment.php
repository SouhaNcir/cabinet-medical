<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Appointment extends Model
{
    public function allByDate(?string $date = null): array
    {
        $params = [];
        $where = '';

        if (!empty($date)) {
            $where = ' WHERE DATE(a.appointment_at) = :date ';
            $params['date'] = $date;
        }

        $sql = 'SELECT a.id, a.appointment_at, a.duration_minutes, a.status, a.notes,
                       p.full_name AS patient_name,
                       u.full_name AS doctor_name
                FROM appointments a
                INNER JOIN patients p ON p.id = a.patient_id
                INNER JOIN users u ON u.id = a.doctor_id
                ' . $where . '
                ORDER BY a.appointment_at ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM appointments WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function hasConflict(int $doctorId, string $appointmentAt, int $durationMinutes, ?int $excludeId = null): bool
    {
        $start = new \DateTimeImmutable($appointmentAt);
        $end = $start->modify('+' . $durationMinutes . ' minutes');

        $sql = 'SELECT appointment_at, duration_minutes FROM appointments WHERE doctor_id = :doctor_id';
        $params = ['doctor_id' => $doctorId];

        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        foreach ($stmt->fetchAll() as $row) {
            $existingStart = new \DateTimeImmutable($row['appointment_at']);
            $existingEnd = $existingStart->modify('+' . (int) $row['duration_minutes'] . ' minutes');
            if ($start < $existingEnd && $end > $existingStart) {
                return true;
            }
        }

        return false;
    }

    public function create(array $data): bool
    {
        $sql = 'INSERT INTO appointments (patient_id, doctor_id, appointment_at, duration_minutes, status, notes)
                VALUES (:patient_id, :doctor_id, :appointment_at, :duration_minutes, :status, :notes)';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE appointments
                SET patient_id = :patient_id,
                    doctor_id = :doctor_id,
                    appointment_at = :appointment_at,
                    duration_minutes = :duration_minutes,
                    status = :status,
                    notes = :notes
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array_merge($data, ['id' => $id]));
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM appointments WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }
}
