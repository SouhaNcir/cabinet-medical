<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT id, full_name, email, password, role FROM users WHERE email = :email LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function doctors(): array
    {
        $stmt = $this->db->prepare('SELECT id, full_name FROM users WHERE role = :role ORDER BY full_name ASC');
        $stmt->execute(['role' => 'medecin']);

        return $stmt->fetchAll();
    }
}
