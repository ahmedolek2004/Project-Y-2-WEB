<?php
// ============================================================
//  classes/Admin.php
//  extends User — admin-only operations
// ============================================================
class Admin extends User {

    // ── Get all users ────────────────────────────────────────
    public function getUsers(): array {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, phone, created_at
             FROM users ORDER BY created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── Add doctor or patient ────────────────────────────────
    public function addUser(array $data): bool|string {
        return $this->register($data);
    }

    // ── Delete user (cannot delete self) ─────────────────────
    public function deleteUser(int $id): bool|string {
        if ($id === (int)$_SESSION['user_id']) {
            return "You cannot delete your own account.";
        }
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    // ── Dashboard statistics ⭐ Bonus ─────────────────────────
    public function getDashboardStats(): array {
        $stats = [];

        $stmt = $this->db->prepare(
            "SELECT
               COUNT(*) AS total,
               SUM(role='doctor')  AS doctors,
               SUM(role='patient') AS patients,
               SUM(role='admin')   AS admins
             FROM users"
        );
        $stmt->execute();
        $stats['users'] = $stmt->fetch();

        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM medical_records");
        $stmt->execute();
        $stats['records'] = $stmt->fetch()['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM prescriptions");
        $stmt->execute();
        $stats['prescriptions'] = $stmt->fetch()['total'];

        // Latest 5 users
        $stmt = $this->db->prepare(
            "SELECT name, role, created_at FROM users
             ORDER BY created_at DESC LIMIT 5"
        );
        $stmt->execute();
        $stats['recent_users'] = $stmt->fetchAll();

        return $stats;
    }

    // ── Search users ⭐ Bonus ─────────────────────────────────
    public function searchUsers(string $keyword): array {
        $like = '%' . $keyword . '%';
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, phone
             FROM users
             WHERE (name LIKE ? OR email LIKE ?)
             AND role != 'admin'
             ORDER BY name"
        );
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }
}
