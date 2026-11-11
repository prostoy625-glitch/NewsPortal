
<?php

require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/app/Controllers/HomeController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';

$router = new Router();

$homeController = new HomeController();
$authController = new AuthController();
$dashboardController = new DashboardController();

// Главная страница
$router->get('/', [$homeController, 'index']);

// Авторизация
$router->get('/login', [$authController, 'showLogin']);
$router->post('/login', [$authController, 'login']);

// Выход
$router->post('/logout', [$authController, 'logout']);

// Панель управления
$router->get('/dashboard', [$dashboardController, 'index']);


$router->dispatch();
