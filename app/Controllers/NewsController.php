

<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/View.php';

class NewsController
{
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
            $stmt->execute(['slug' => $slug]);

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

