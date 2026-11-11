
<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/View.php';


class HomeController
{
    public function index(): void
    {
        $news = [];
        $databaseStatus = 'Подключение не проверено';

        try {
            $pdo = Database::connect();

            $sql = "
                SELECT
                    a.id,
                    a.status,
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
                WHERE a.status = 'published'
                    AND t.language_code = 'tg'
                    AND t.translation_status = 'published'
                ORDER BY a.published_at DESC, a.id DESC
                LIMIT 12
            ";

            $news = $pdo->query($sql)->fetchAll();

            $databaseStatus = 'MySQL подключён успешно!';

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $databaseStatus = 'Хатогӣ ҳангоми пайвастшавӣ ба базаи маълумот.';
        }

        View::render('home', [
            'title' => 'NewsPortal',
            'databaseStatus' => $databaseStatus,
            'news' => $news
        ]);
    }
}
