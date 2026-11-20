

<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, callable $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $uri = parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        );

        $basePath = '/NewsPortal';

        if (str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = '/' . trim($uri, '/');

        if ($uri === '//') {
            $uri = '/';
        }

        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $parts = explode('/', trim($route, '/'));
            $pattern = '';

            foreach ($parts as $part) {
                if (str_starts_with($part, ':')) {
                    $pattern .= '/([^/]+)';
                } else {
                    $pattern .= '/' . preg_quote($part, '#');
                }
            }

            if ($route === '/') {
                $pattern = '/';
            }

            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches);

                $params = array_map('urldecode', $matches);

                call_user_func_array($callback, $params);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page not found';
    }
}
