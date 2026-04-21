<?php
require_once __DIR__ . '/User.php';

class Auth {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $email, string $password): array {
        $user = $this->userModel->findByEmail($email);
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        session_regenerate_id(true);
        return ['success' => true, 'role' => $user['role']];
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public function register(string $name, string $email, string $password, string $role): array {
        // Only allow patient self-registration
        if (!in_array($role, ['patient'])) {
            return ['success' => false, 'message' => 'Invalid role for self-registration.'];
        }
        if ($this->userModel->findByEmail($email)) {
            return ['success' => false, 'message' => 'Email already registered.'];
        }
        $this->userModel->register($name, $email, $password, $role);
        return ['success' => true];
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public function getRole(): string {
        return $_SESSION['user_role'] ?? '';
    }

    public function getId(): int {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    public function getName(): string {
        return $_SESSION['user_name'] ?? '';
    }
}
