<?php
require_once __DIR__ . '/Auth.php';

/**
 * Middleware-style permission guard.
 * Call Permission::require('role') at the top of any restricted page.
 */
class Permission {
    private static ?Auth $auth = null;

    private static function auth(): Auth {
        if (self::$auth === null) {
            self::$auth = new Auth();
        }
        return self::$auth;
    }

    /**
     * Require the user to be logged in and have one of the given roles.
     * Redirects to login or dashboard on failure.
     */
    public static function require(string ...$roles): void {
        $auth = self::auth();

        if (!$auth->isLoggedIn()) {
            header('Location: /index.php?error=Please+log+in+to+continue');
            exit;
        }

        if (!empty($roles) && !in_array($auth->getRole(), $roles, true)) {
            // Redirect to appropriate dashboard
            self::redirectToDashboard($auth->getRole());
        }
    }

    /** Redirect already-logged-in users away from login/register pages */
    public static function redirectIfLoggedIn(): void {
        $auth = self::auth();
        if ($auth->isLoggedIn()) {
            self::redirectToDashboard($auth->getRole());
        }
    }

    public static function can(string $action, array $context = []): bool {
        $auth  = self::auth();
        $role  = $auth->getRole();
        $myId  = $auth->getId();

        return match ($action) {
            'view_record'    => ($role === 'doctor'  && ($context['doctor_id']  ?? 0) === $myId)
                             || ($role === 'patient' && ($context['patient_id'] ?? 0) === $myId)
                             || $role === 'admin',
            'edit_record'    => $role === 'doctor'  && ($context['doctor_id']  ?? 0) === $myId,
            'delete_user'    => $role === 'admin'   && ($context['user_id']    ?? 0) !== $myId,
            'manage_users'   => $role === 'admin',
            'add_record'     => $role === 'doctor',
            'view_all_users' => $role === 'admin',
            default          => false,
        };
    }

    private static function redirectToDashboard(string $role): void {
        $map = [
            'admin'   => '/pages/admin/dashboard.php',
            'doctor'  => '/pages/doctor/dashboard.php',
            'patient' => '/pages/patient/dashboard.php',
        ];
        header('Location: ' . ($map[$role] ?? '/index.php'));
        exit;
    }
}
