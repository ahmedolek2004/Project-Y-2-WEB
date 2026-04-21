<?php
// ============================================================
//  classes/User.php
//  Base class — all roles extend this
// ============================================================
class User {
    protected ?int    $id       = null;
    protected ?string $name     = null;
    protected ?string $email    = null;
    protected ?string $password = null;
    protected ?string $role     = null;
    protected ?string $phone    = null;
    protected PDO     $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Getters ─────────────────────────────────────────────
    public function getId():    ?int    { return $this->id; }
    public function getName():  ?string { return $this->name; }
    public function getEmail(): ?string { return $this->email; }
    public function getRole():  ?string { return $this->role; }

    // ── Register ─────────────────────────────────────────────
    public function register(array $data): bool|string {
        // Validate
        $required = ['name','email','password','role'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return "All required fields must be filled.";
            }
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }
        if (strlen($data['password']) < 8) {
            return "Password must be at least 8 characters.";
        }
        if (!in_array($data['role'], ['admin','doctor','patient'])) {
            return "Invalid role selected.";
        }

        // Check duplicate email
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return "Email already exists.";
        }

        // Insert
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, phone)
             VALUES (?, ?, ?, ?, ?)"
        );
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt->execute([
            htmlspecialchars(trim($data['name'])),
            strtolower(trim($data['email'])),
            $hashed,
            $data['role'],
            $data['phone'] ?? null
        ]);
        return true;
    }

    // ── Login ────────────────────────────────────────────────
    public function login(string $email, string $password): bool {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, password, role FROM users WHERE email = ?"
        );
        $stmt->execute([strtolower(trim($email))]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        return false;
    }

    // ── Logout ───────────────────────────────────────────────
    public function logout(): void {
        session_unset();
        session_destroy();
        header("Location: /login.php");
        exit;
    }

    // ── Update Profile ───────────────────────────────────────
    public function updateProfile(array $data): bool|string {
        $userId = $_SESSION['user_id'];

        if (empty($data['name']) || empty($data['email'])) {
            return "Name and email are required.";
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }

        // Check email not taken by another user
        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE email = ? AND id != ?"
        );
        $stmt->execute([$data['email'], $userId]);
        if ($stmt->fetch()) {
            return "Email already used by another account.";
        }

        // Password change requested?
        if (!empty($data['new_password'])) {
            if (strlen($data['new_password']) < 8) {
                return "New password must be at least 8 characters.";
            }
            // Verify old password first
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $row = $stmt->fetch();
            if (!password_verify($data['current_password'] ?? '', $row['password'])) {
                return "Current password is incorrect.";
            }
            $newHash = password_hash($data['new_password'], PASSWORD_BCRYPT);
            $stmt = $this->db->prepare(
                "UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=?"
            );
            $stmt->execute([
                htmlspecialchars(trim($data['name'])),
                strtolower(trim($data['email'])),
                $data['phone'] ?? null,
                $newHash,
                $userId
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE users SET name=?, email=?, phone=? WHERE id=?"
            );
            $stmt->execute([
                htmlspecialchars(trim($data['name'])),
                strtolower(trim($data['email'])),
                $data['phone'] ?? null,
                $userId
            ]);
        }

        $_SESSION['user_name'] = htmlspecialchars(trim($data['name']));
        return true;
    }

    // ── Get current user data ────────────────────────────────
    public function getCurrentUser(): ?array {
        if (empty($_SESSION['user_id'])) return null;
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, phone, created_at FROM users WHERE id = ?"
        );
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch() ?: null;
    }
}
