
<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/View.php';
require_once __DIR__ . '/../../core/Auth.php';

require_once __DIR__ . '/../Repositories/NewsRepository.php';
require_once __DIR__ . '/../Repositories/CategoryRepository.php';
require_once __DIR__ . '/../Validators/NewsValidator.php';

class NewsController
{
    // Отображение формы создания новости
    public function create(): void
    {
        Auth::requireRole(['ADMIN', 'EDITOR', 'JOURNALIST']);

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        try {
            $categoryRepository = new CategoryRepository();

            View::render('news/create', [
                'title' => 'Илова кардани хабар',
                'categories' => $categoryRepository->getAll(),
                'errors' => [],
                'old' => []
            ]);

        } catch (Throwable $e) {
            error_log($e->getMessage());

            http_response_code(500);
            echo 'Хатогӣ ҳангоми гирифтани категорияҳо.';
        }
    }

    // Сохранение новой новости
    public function store(): void
    {
        Auth::requireRole(['ADMIN', 'EDITOR', 'JOURNALIST']);

        // Проверка CSRF-токена
        if (
            empty($_SESSION['csrf_token']) ||
            !isset($_POST['csrf_token']) ||
            !is_string($_POST['csrf_token']) ||
            !hash_equals(
                $_SESSION['csrf_token'],
                $_POST['csrf_token']
            )
        ) {
            http_response_code(403);
            echo 'Дархости нодуруст (CSRF).';
            return;
        }

        // Получение данных формы
        $fields = [
            'title',
            'slug',
            'excerpt',
            'content',
            'category_id',
            'status'
        ];

        $data = [];

        foreach ($fields as $field) {
            $value = $_POST[$field] ?? '';

            $data[$field] = is_string($value)
                ? trim($value)
                : '';
        }

        // Автор берётся из сессии
        $data['author_id'] = (int) $_SESSION['user_id'];

        // Серверная валидация
        $errors = NewsValidator::validate($data);

        if (!empty($errors)) {
            $this->renderFormWithErrors($errors, $data);
            return;
        }

        try {
            $repository = new NewsRepository();

            $articleId = $repository->create($data);

            // Перенаправление после успешного POST
            header(
                'Location: /NewsPortal/dashboard?created=' . $articleId,
                true,
                303
            );
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());

            if ($e->getCode() === '23000') {
                $this->renderFormWithErrors([
                    'slug' => 'Slug такрорӣ аст ё маълумоти вобаста нодуруст мебошад.'
                ], $data);
                return;
            }

            http_response_code(500);
            echo 'Хатогӣ ҳангоми нигоҳ доштани хабар.';

        } catch (Throwable $e) {
            error_log($e->getMessage());

            http_response_code(500);
            echo 'Хатогии дохилии сервер.';
        }
    }

    // Повторный показ формы с ошибками
    private function renderFormWithErrors(
        array $errors,
        array $old
    ): void {
        try {
            $categoryRepository = new CategoryRepository();

            http_response_code(422);

            View::render('news/create', [
                'title' => 'Илова кардани хабар',
                'categories' => $categoryRepository->getAll(),
                'errors' => $errors,
                'old' => $old
            ]);

        } catch (Throwable $e) {
            error_log($e->getMessage());

            http_response_code(500);
            echo 'Хатогӣ ҳангоми гирифтани категорияҳо.';
        }
    }

    // Просмотр опубликованной новости
    public function show(string $slug): void
    {
        try {
            $pdo = Database::connect();

            $sql = "
                SELECT
                    a.id,
                    a.published_at,
                    t.title,
                    t.slug,
                    t.excerpt,
                    t.content,
                    u.full_name AS author,
                    ct.name AS category
                FROM articles AS a
                INNER JOIN article_translations AS t
                    ON t.article_id = a.id
                INNER JOIN users AS u
                    ON u.id = a.author_id
                LEFT JOIN category_translations AS ct
                    ON ct.category_id = a.category_id
                    AND ct.language_code = 'tg'
                WHERE t.slug = :slug
                    AND t.language_code = 'tg'
                    AND t.translation_status = 'published'
                    AND a.status = 'published'
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'slug' => $slug
            ]);

            $news = $stmt->fetch();

            if (!$news) {
                http_response_code(404);
                echo 'Хабар ёфт нашуд.';
                return;
            }

            View::render('news/show', [
                'title' => $news['title'],
                'news' => $news
            ]);

        } catch (PDOException $e) {
            error_log($e->getMessage());

            http_response_code(500);
            echo 'Хатогӣ ҳангоми гирифтани хабар.';
        }
    }
}
