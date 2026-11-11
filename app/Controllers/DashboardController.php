<?php

require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/View.php';

class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();

        $role = $_SESSION['role'] ?? '';

        $roleTitles = [
            'ADMIN' => 'Администратор',
            'EDITOR' => 'Муҳаррир',
            'JOURNALIST' => 'Журналист',
            'READER' => 'Хонанда'
        ];

        View::render('dashboard/index', [
            'title' => 'Панели идоракунӣ — NewsPortal',
            'fullName' => $_SESSION['full_name'] ?? '',
            'role' => $role,
            'roleTitle' => $roleTitles[$role] ?? 'Корбар'
        ]);
    }
}