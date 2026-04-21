<?php
// ============================================================
//  classes/Middleware.php
//  ⭐ Bonus +2 — Role-Based Access Control middleware
// ============================================================
class Middleware {

    // ── Check if user is logged in ───────────────────────────
    public static function isLoggedIn(): bool {
        return !empty($_SESSION['user_id']);
    }

    // ── Require login — redirect if not ─────────────────────
    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header("Location: /login.php?error=Please+login+first");
            exit;
        }
    }

    // ── Check single role ────────────────────────────────────
    public static function checkRole(string $requiredRole): void {
        self::requireLogin();
        if ($_SESSION['user_role'] !== $requiredRole) {
            self::deny();
        }
    }

    // ── Check multiple allowed roles ─────────────────────────
    public static function checkRoles(array $allowedRoles): void {
        self::requireLogin();
        if (!in_array($_SESSION['user_role'], $allowedRoles)) {
            self::deny();
        }
    }

    // ── Deny access ──────────────────────────────────────────
    public static function deny(): void {
        http_response_code(403);
        include __DIR__ . '/../includes/403.php';
        exit;
    }

    // ── Redirect by role after login ─────────────────────────
    public static function redirectByRole(): void {
        if (!self::isLoggedIn()) {
            header("Location: /login.php");
            exit;
        }
        $routes = [
            'admin'   => '/pages/admin/dashboard.php',
            'doctor'  => '/pages/doctor/dashboard.php',
            'patient' => '/pages/patient/dashboard.php',
        ];
        $role = $_SESSION['user_role'];
        header("Location: " . ($routes[$role] ?? '/login.php'));
        exit;
    }
}
