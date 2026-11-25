<?php

namespace App\Controllers;

use PDOException;
use Throwable;
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';

require_once __DIR__ . '/../Repositories/NewsRepository.php';
require_once __DIR__ . '/../Repositories/CategoryRepository.php';
require_once __DIR__ . '/../Validators/NewsValidator.php';

class NewsController extends Controller
{
    // Отображение формы создания новости
    public function create(): void
    {
        \Auth::requireRole(['ADMIN', 'EDITOR', 'JOURNALIST']);

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        try {
            $categoryRepository = new \CategoryRepository();

            $this->render('news/create', [
                'title' => 'Илова кардани хабар',
                'categories' => $categoryRepository->getAll(),
                'errors' => [],
                'old' => []
            ]);

        } catch (Throwable $e) {
            error_log($e->getMessage());

            $this->abort(
                500,
                'Хатогӣ ҳангоми гирифтани категорияҳо.'
            );
        }
    }

    // Сохранение новой новости
    public function store(): void
    {
        \Auth::requireRole(['ADMIN', 'EDITOR', 'JOURNALIST']);

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
            $this->abort(
                403,
                'Дархости нодуруст (CSRF).'
            );
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
        $errors = \NewsValidator::validate($data);

        if (!empty($errors)) {
            $this->renderFormWithErrors($errors, $data);
            return;
        }

        try {
            $repository = new \NewsRepository();

            $articleId = $repository->create($data);

            // Перенаправление после успешного POST
            $this->redirect(
                '/dashboard?created=' . $articleId,
                303
            );

        } catch (PDOException $e) {
            error_log($e->getMessage());

            if ($e->getCode() === '23000') {
                $this->renderFormWithErrors([
                    'slug' => 'Slug такрорӣ аст ё маълумоти вобаста нодуруст мебошад.'
                ], $data);

                return;
            }

            $this->abort(
                500,
                'Хатогӣ ҳангоми нигоҳ доштани хабар.'
            );

        } catch (Throwable $e) {
            error_log($e->getMessage());

            $this->abort(
                500,
                'Хатогии дохилии сервер.'
            );
        }
    }

    // Повторный показ формы с ошибками
    private function renderFormWithErrors(
        array $errors,
        array $old
    ): void {
        try {
            $categoryRepository = new \CategoryRepository();

            $this->render('news/create', [
                'title' => 'Илова кардани хабар',
                'categories' => $categoryRepository->getAll(),
                'errors' => $errors,
                'old' => $old
            ], 422);

        } catch (Throwable $e) {
            error_log($e->getMessage());

            $this->abort(
                500,
                'Хатогӣ ҳангоми гирифтани категорияҳо.'
            );
        }
    }

    // Просмотр опубликованной новости
    public function show(string $slug): void
    {
        try {
            $pdo = \Database::connect();

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
                $this->abort(
                    404,
                    'Хабар ёфт нашуд.'
                );
            }

            $this->render('news/show', [
                'title' => $news['title'],
                'news' => $news
            ]);

        } catch (PDOException $e) {
            error_log($e->getMessage());

            $this->abort(
                500,
                'Хатогӣ ҳангоми гирифтани хабар.'
            );
        }
    }
}
