<?php

namespace App\Controllers;

use Core\View;

abstract class Controller
{
    protected function render(
        string $view,
        array $data = [],
        int $status = 200
    ): void {
        http_response_code($status);

        View::render($view, $data);
    }

    protected function redirect(
        string $path,
        int $status = 302
    ): never {
        http_response_code($status);

        header('Location: /NewsPortal' . $path);

        exit;
    }

    protected function json(
        array $data,
        int $status = 200
    ): never {
        http_response_code($status);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit;
    }

    protected function abort(
        int $code,
        string $message = ''
    ): never {
        http_response_code($code);

        echo '<h1>' . $code . '</h1>';

        echo '<p>' . htmlspecialchars(
            $message,
            ENT_QUOTES,
            'UTF-8'
        ) . '</p>';

        exit;
    }
}
