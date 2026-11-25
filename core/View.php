<?php

namespace Core;

use RuntimeException;

class View
{
    public static function render(
        string $view,
        array $data = []
    ): void {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../views/layouts/main.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View file not found.';
            return;
        }

        if (!is_file($layoutFile)) {
            http_response_code(500);
            echo 'Layout file not found.';
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }

    // Безопасный вывод текста в HTML
    public static function e(mixed $value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }

    // Формирование URL для NewsPortal
    public static function url(string $path = '/'): string
    {
        return '/NewsPortal/' . ltrim($path, '/');
    }

    // Подключение части шаблона
    public static function partial(
        string $name,
        array $data = []
    ): void {
        $file = __DIR__ . '/../views/partials/' . $name . '.php';

        if (!is_file($file)) {
            throw new RuntimeException(
                'Partial template not found: ' . $name
            );
        }

        extract($data, EXTR_SKIP);

        require $file;
    }
}
