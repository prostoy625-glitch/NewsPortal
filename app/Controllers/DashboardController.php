<?php

namespace App\Controllers;

require_once __DIR__ . '/../../core/Auth.php';

class DashboardController extends Controller


{
    public function index(): void
    {
        // Проверяем, авторизован ли пользователь
        \Auth::requireLogin();

        // Получаем роль пользователя из сессии
        $role = $_SESSION['role'] ?? '';

        // Названия ролей
        $roleTitles = [
            'ADMIN' => 'Администратор',
            'EDITOR' => 'Муҳаррир',
            'JOURNALIST' => 'Журналист',
            'READER' => 'Хонанда'
        ];

        // Отображаем панель управления через базовый Controller
        $this->render('dashboard/index', [
            'title' => 'Панели идоракунӣ — NewsPortal',
            'fullName' => $_SESSION['full_name'] ?? '',
            'role' => $role,
            'roleTitle' => $roleTitles[$role] ?? 'Корбар'
        ]);
    }
}
