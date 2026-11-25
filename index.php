
<?php



// Composer PSR-4 autoload
require_once __DIR__ . '/vendor/autoload.php';
// Базовые компоненты MVC



// Временная загрузка существующих классов NewsPortal
require_once __DIR__ . '/core/Auth.php';


$router = new \Core\Router();

$homeController = new \App\Controllers\HomeController();
$authController = new \App\Controllers\AuthController();
$dashboardController = new \App\Controllers\DashboardController();
$newsController = new \App\Controllers\NewsController();

// Главная страница
$router->get('/', [$homeController, 'index']);


 // Форма создания новости
$router->get('/news/create', [$newsController, 'create']);

// Сохранение новости
$router->post('/news/create', [$newsController, 'store']);

// Просмотр отдельной новости
$router->get('/news/:slug', [$newsController, 'show']);

// Авторизация
$router->get('/login', [$authController, 'showLogin']);
$router->post('/login', [$authController, 'login']);
// Выход
$router->post('/logout', [$authController, 'logout']);

// Панель управления
$router->get('/dashboard', [$dashboardController, 'index']);

$router->dispatch();





