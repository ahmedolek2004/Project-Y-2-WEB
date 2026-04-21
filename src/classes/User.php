<?php
require_once __DIR__ . '/Model.php';

class User extends Model {

    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function register(string $name, string $email, string $password, string $role): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$name, $email, $hash, $role]);
    }

    public function updateProfile(int $id, string $name, string $email, ?string $password): bool {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare(
                "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?"
            );
            return $stmt->execute([$name, $email, $hash, $id]);
        }
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $id]);
    }

    public function getAllUsers(): array {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, created_at FROM users ORDER BY role, name"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUsersByRole(string $role): array {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, created_at FROM users WHERE role = ? ORDER BY name"
        );
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function deleteUser(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function searchPatients(string $query): array {
        $like = '%' . $query . '%';
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, created_at FROM users
             WHERE role = 'patient' AND (name LIKE ? OR email LIKE ?)
             ORDER BY name"
        );
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public function searchByRole(string $query, string $role): array {
        $like = '%' . $query . '%';
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, created_at FROM users
             WHERE role = ? AND (name LIKE ? OR email LIKE ?)
             ORDER BY name"
        );
        $stmt->execute([$role, $like, $like]);
        return $stmt->fetchAll();
    }

    public function searchByQuery(string $query): array {
        $like = '%' . $query . '%';
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, created_at FROM users
             WHERE (name LIKE ? OR email LIKE ?)
             ORDER BY role, name"
        );
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public function countByRole(string $role): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return (int)$stmt->fetchColumn();
    }

    public function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }
}
