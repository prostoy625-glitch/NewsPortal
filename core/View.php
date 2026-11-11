
<?php

class View
{
    public static function render(
        string $view,
        array $data = []
    ): void {

        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'View file not found: ' .
                htmlspecialchars($view);
            return;
        }

        extract($data, EXTR_SKIP);

        require $viewFile;
    }
}
