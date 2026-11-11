
<?php


// Настройки подключения к MySQL
$host = '127.0.0.1';
$dbname = 'news_portal';
$username = 'root';
$password = ''; // Стандартный пароль XAMPP обычно пустой

$charset = 'utf8mb4';

// Формируем строку подключения
$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

// Настройки PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Создаём подключение
$pdo = new PDO($dsn, $username, $password, $options);
