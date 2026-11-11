<?php

class Auth
{
    // Проверка авторизации
    public static function requireLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: /NewsPortal/login');
            exit;
        }
    }

    // Проверка роли пользователя
    public static function requireRole(array $allowedRoles): void
    {
        self::requireLogin();

        $userRole = $_SESSION['role'] ?? '';

        if (!in_array($userRole, $allowedRoles, true)) {
            http_response_code(403);
            exit('Шумо барои дастрасӣ ба ин саҳифа иҷозат надоред.');
        }
    }
}