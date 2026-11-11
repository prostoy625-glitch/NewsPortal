
<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/View.php';

class AuthController
{
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Показываем страницу входа
    public function showLogin(): void
    {
        $this->startSession();

       if (isset($_SESSION['user_id'])) {
    header('Location: /NewsPortal/dashboard');
    exit;
}

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        View::render('auth/login', [
            'title' => 'Воридшавӣ ба NewsPortal',
            'csrfToken' => $_SESSION['csrf_token']
        ]);
    }

    // Обрабатываем вход пользователя
    public function login(): void
    {
        $this->startSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Метод иҷозат дода нашудааст.');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';

        if (
            !is_string($csrfToken) ||
            !hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)
        ) {
            http_response_code(403);
            exit('Дархости нодуруст.');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !is_string($password) ||
            $password === '') {
            $_SESSION['login_error'] = 'Email ё парол нодуруст аст.';
            header('Location: /NewsPortal/login');
            exit;
        }

        try {
            $pdo = Database::connect();

            $sql = "
                SELECT
                    u.id,
                    u.full_name,
                    u.email,
                    u.password_hash,
                    u.is_active,
                    r.name AS role_name
                FROM users AS u
                INNER JOIN roles AS r ON r.id = u.role_id
                WHERE u.email = :email
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);

            $user = $stmt->fetch();
            


            if (
                !$user ||
                (int)$user['is_active'] !== 1 ||
                !password_verify($password, $user['password_hash'])
            ) {
                $_SESSION['login_error'] = 'Email ё парол нодуруст аст.';
                header('Location: /NewsPortal/login');
                exit;
            }

            session_regenerate_id(true);

            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role_name'];

            unset($_SESSION['login_error']);

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header('Location: /NewsPortal/');
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            exit('Хатои дохилии сервер.');
        }
    }

    // Выход пользователя
    public function logout(): void
    {
        $this->startSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Метод иҷозат дода нашудааст.');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';

        if (
            !is_string($csrfToken) ||
            !hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)
        ) {
            http_response_code(403);
            exit('Дархости нодуруст.');
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: /NewsPortal/login');
        exit;
    }
}
