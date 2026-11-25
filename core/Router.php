<?php

namespace Core;

class Router
{
    private array $routes = [];

    // Регистрация GET-маршрута
    public function get(string $path, callable $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    // Регистрация POST-маршрута
    public function post(string $path, callable $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    // Нормализация URL
    private function normalizePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        $basePath = '/NewsPortal';

        if (
            $path === $basePath ||
            str_starts_with($path, $basePath . '/')
        ) {
            $path = substr($path, strlen($basePath));
        }

        $path = '/' . trim($path, '/');

        return $path === '//' ? '/' : $path;
    }

    // Проверка соответствия маршрута
    private function matches(string $route, string $uri): array|false
    {
        if ($route === '/') {
            return $uri === '/' ? [] : false;
        }

        $parts = explode('/', trim($route, '/'));
        $pattern = '';

        foreach ($parts as $part) {
            if (str_starts_with($part, ':')) {
                $pattern .= '/([^/]+)';
            } else {
                $pattern .= '/' . preg_quote($part, '#');
            }
        }

        if (!preg_match('#^' . $pattern . '$#', $uri, $matches)) {
            return false;
        }

        array_shift($matches);

        return array_map('urldecode', $matches);
    }

    // Обработка HTTP-запроса
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $uri = $this->normalizePath(
            $_SERVER['REQUEST_URI'] ?? '/'
        );

        $allowedMethods = [];

        foreach ($this->routes as $routeMethod => $routes) {
            foreach ($routes as $route => $callback) {
                $params = $this->matches($route, $uri);

                if ($params === false) {
                    continue;
                }

                if ($routeMethod !== $method) {
                    $allowedMethods[] = $routeMethod;
                    continue;
                }

                call_user_func_array($callback, $params);
                return;
            }
        }

        // Метод HTTP не разрешён
        if ($allowedMethods !== []) {
            http_response_code(405);

            header(
                'Allow: ' .
                implode(', ', array_unique($allowedMethods))
            );

            echo '405 - Method Not Allowed';
            return;
        }

        // Страница не найдена
        http_response_code(404);
        echo '404 - Page Not Found';
    }
}
