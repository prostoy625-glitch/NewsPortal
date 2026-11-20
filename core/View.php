
<?php

class View
{
    public static function render(
        string $view,
        array $data = []
    ): void {

        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../views/layouts/main.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'View file not found: ' .
                htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }

        if (!file_exists($layoutFile)) {
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
}
