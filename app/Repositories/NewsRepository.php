
<?php

require_once __DIR__ . '/../../core/Database.php';

class NewsRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    // 1. Получить все новости
    public function getAll(): array
    {
        $sql = "
            SELECT
                a.id,
                a.category_id,
                a.author_id,
                a.status,
                a.published_at,
                t.title,
                t.slug,
                t.excerpt,
                t.content
            FROM articles AS a
            INNER JOIN article_translations AS t
                ON t.article_id = a.id
            WHERE t.language_code = :language
            ORDER BY a.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['language' => 'tg']);

        return $stmt->fetchAll();
    }

    // 2. Получить новость по ID
    public function getById(int $id): array|false
    {
        $sql = "
            SELECT
                a.id,
                a.category_id,
                a.author_id,
                a.status,
                a.published_at,
                t.title,
                t.slug,
                t.excerpt,
                t.content
            FROM articles AS a
            INNER JOIN article_translations AS t
                ON t.article_id = a.id
            WHERE a.id = :id
              AND t.language_code = :language
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id,
            'language' => 'tg'
        ]);

        return $stmt->fetch();
    }

    // 3. Создать новость
    public function create(array $data): int
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "
                INSERT INTO articles
                    (category_id, author_id, status, published_at)
                VALUES
                    (:category_id, :author_id, :status, :published_at)
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'category_id' => $data['category_id'],
                'author_id' => $data['author_id'],
                'status' => $data['status'],
                'published_at' => $data['status'] === 'published'
                    ? date('Y-m-d H:i:s')
                    : null
            ]);

            $articleId = (int) $this->pdo->lastInsertId();

            $sql = "
                INSERT INTO article_translations
                    (article_id, language_code, title,
                     slug, excerpt, content)
                VALUES
                    (:article_id, :language_code, :title,
                     :slug, :excerpt, :content)
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'article_id' => $articleId,
                'language_code' => 'tg',
                'title' => $data['title'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'] ?? null,
                'content' => $data['content']
            ]);

            $this->pdo->commit();

            return $articleId;

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    // 4. Обновить новость
    public function update(int $id, array $data): bool
    {
        try {
            $this->pdo->beginTransaction();

            $article = $this->getById($id);

            if ($article === false) {
                $this->pdo->rollBack();
                return false;
            }

            $sql = "
                UPDATE articles
                SET category_id = :category_id,
                    status = :status,
                    published_at = CASE
                        WHEN :new_status = 'published'
                        THEN COALESCE(published_at, NOW())
                        ELSE NULL
                    END
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'category_id' => $data['category_id'],
                'status' => $data['status'],
                'new_status' => $data['status'],
                'id' => $id
            ]);

            $sql = "
                UPDATE article_translations
                SET title = :title,
                    slug = :slug,
                    excerpt = :excerpt,
                    content = :content
                WHERE article_id = :article_id
                  AND language_code = :language
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'] ?? null,
                'content' => $data['content'],
                'article_id' => $id,
                'language' => 'tg'
            ]);

            $this->pdo->commit();

            return true;

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    // 5. Удалить новость
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM articles WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // 6. Поиск новостей
    public function search(string $keyword): array
    {
        $sql = "
            SELECT
                a.id,
                a.category_id,
                a.author_id,
                a.status,
                t.title,
                t.slug,
                t.excerpt,
                t.content
            FROM articles AS a
            INNER JOIN article_translations AS t
                ON t.article_id = a.id
            WHERE t.language_code = :language
              AND (
                  t.title LIKE :title ESCAPE '!'
                  OR t.content LIKE :content ESCAPE '!'
              )
            ORDER BY a.created_at DESC
        ";

        // Экранируем специальные символы LIKE
        $escaped = str_replace(
            ['!', '%', '_'],
            ['!!', '!%', '!_'],
            $keyword
        );

        $pattern = '%' . $escaped . '%';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'language' => 'tg',
            'title' => $pattern,
            'content' => $pattern
        ]);

        return $stmt->fetchAll();
    }

public function getFiltered(
    string $keyword = '',
    ?int $categoryId = null,
    ?string $status = null
): array {
    $sql = "
        SELECT
            a.id,
            a.category_id,
            a.author_id,
            a.status,
            a.published_at,
            t.title,
            t.slug,
            t.excerpt,
            t.content
        FROM articles AS a
        INNER JOIN article_translations AS t
            ON t.article_id = a.id
        WHERE t.language_code = :language
    ";

    $params = [
        'language' => 'tg'
    ];

    // Фильтр по ключевому слову
    if ($keyword !== '') {
        $sql .= "
            AND (
                t.title LIKE :title ESCAPE '!'
                OR t.content LIKE :content ESCAPE '!'
            )
        ";

        $escaped = str_replace(
            ['!', '%', '_'],
            ['!!', '!%', '!_'],
            $keyword
        );

        $pattern = '%' . $escaped . '%';

        $params['title'] = $pattern;
        $params['content'] = $pattern;
    }

    // Фильтр по категории
    if ($categoryId !== null) {
        $sql .= " AND a.category_id = :category_id";
        $params['category_id'] = $categoryId;
    }

    // Фильтр по статусу
    if ($status !== null) {
        $sql .= " AND a.status = :status";
        $params['status'] = $status;
    }

    $sql .= " ORDER BY a.created_at DESC, a.id DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

public function getPaginated(int $page = 1, int $perPage = 5): array
{
    // Защита от неправильных значений
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    // Вычисляем смещение
    $offset = ($page - 1) * $perPage;

    $sql = "
        SELECT
            a.id,
            a.category_id,
            a.status,
            t.title,
            t.slug
        FROM articles AS a
        INNER JOIN article_translations AS t
            ON t.article_id = a.id
        WHERE t.language_code = :language
        ORDER BY a.created_at DESC, a.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':language', 'tg', PDO::PARAM_STR);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
}

public function countFiltered(
    string $keyword = '',
    ?int $categoryId = null,
    ?string $status = null
): int {
    $sql = "
        SELECT COUNT(*)
        FROM articles AS a
        INNER JOIN article_translations AS t
            ON t.article_id = a.id
        WHERE t.language_code = :language
    ";

    $params = [
        'language' => 'tg'
    ];

    // Поиск по заголовку и содержимому
    if ($keyword !== '') {
        $sql .= "
            AND (
                t.title LIKE :title ESCAPE '!'
                OR t.content LIKE :content ESCAPE '!'
            )
        ";

        $escaped = str_replace(
            ['!', '%', '_'],
            ['!!', '!%', '!_'],
            $keyword
        );

        $pattern = '%' . $escaped . '%';

        $params['title'] = $pattern;
        $params['content'] = $pattern;
    }

    // Фильтр по категории
    if ($categoryId !== null) {
        $sql .= " AND a.category_id = :category_id";
        $params['category_id'] = $categoryId;
    }

    // Фильтр по статусу
    if ($status !== null) {
        $sql .= " AND a.status = :status";
        $params['status'] = $status;
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

public function getFilteredPaginated(
    string $keyword = '',
    ?int $categoryId = null,
    ?string $status = null,
    int $page = 1,
    int $perPage = 5
): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    $offset = ($page - 1) * $perPage;

    $sql = "
        SELECT
            a.id,
            a.category_id,
            a.author_id,
            a.status,
            a.published_at,
            t.title,
            t.slug,
            t.excerpt,
            t.content
        FROM articles AS a
        INNER JOIN article_translations AS t
            ON t.article_id = a.id
        WHERE t.language_code = :language
    ";

    $params = [
        ':language' => 'tg'
    ];

    if ($keyword !== '') {
        $sql .= "
            AND (
                t.title LIKE :title ESCAPE '!'
                OR t.content LIKE :content ESCAPE '!'
            )
        ";

        $escaped = str_replace(
            ['!', '%', '_'],
            ['!!', '!%', '!_'],
            $keyword
        );

        $pattern = '%' . $escaped . '%';

        $params[':title'] = $pattern;
        $params[':content'] = $pattern;
    }

    if ($categoryId !== null) {
        $sql .= " AND a.category_id = :category_id";
        $params[':category_id'] = $categoryId;
    }

    if ($status !== null) {
        $sql .= " AND a.status = :status";
        $params[':status'] = $status;
    }

    $sql .= "
        ORDER BY a.created_at DESC, a.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->pdo->prepare($sql);

    foreach ($params as $name => $value) {
        $stmt->bindValue(
            $name,
            $value,
            is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
        );
    }

    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
}

public function getPublishedPaginated(
    string $keyword = '',
    ?int $categoryId = null,
    int $page = 1,
    int $perPage = 5
): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $offset = ($page - 1) * $perPage;

    $sql = "
        SELECT
            a.id,
            a.status,
            a.published_at,
            a.category_id,
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
    ";

    $params = [];

    if ($keyword !== '') {
        $escaped = str_replace(
            ['!', '%', '_'],
            ['!!', '!%', '!_'],
            $keyword
        );

        $pattern = '%' . $escaped . '%';

        $sql .= "
            AND (
                t.title LIKE :title ESCAPE '!'
                OR t.content LIKE :content ESCAPE '!'
            )
        ";

        $params[':title'] = $pattern;
        $params[':content'] = $pattern;
    }

    if ($categoryId !== null) {
        $sql .= " AND a.category_id = :category_id";
        $params[':category_id'] = $categoryId;
    }

    $sql .= "
        ORDER BY a.published_at DESC, a.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->pdo->prepare($sql);

    foreach ($params as $name => $value) {
        $stmt->bindValue(
            $name,
            $value,
            is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
        );
    }

    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll();
}

// Подсчёт опубликованных новостей
public function countPublished(
    string $keyword = '',
    ?int $categoryId = null
): int {
    $sql = "
        SELECT COUNT(*)
        FROM articles AS a
        INNER JOIN article_translations AS t
            ON t.article_id = a.id
        INNER JOIN users AS u
            ON u.id = a.author_id
        WHERE a.status = 'published'
          AND t.language_code = 'tg'
          AND t.translation_status = 'published'
    ";

    $params = [];

    if ($keyword !== '') {
        $escaped = str_replace(
            ['!', '%', '_'],
            ['!!', '!%', '!_'],
            $keyword
        );

        $pattern = '%' . $escaped . '%';

        $sql .= "
            AND (
                t.title LIKE :title ESCAPE '!'
                OR t.content LIKE :content ESCAPE '!'
            )
        ";

        $params['title'] = $pattern;
        $params['content'] = $pattern;
    }

    if ($categoryId !== null) {
        $sql .= " AND a.category_id = :category_id";
        $params['category_id'] = $categoryId;
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

}
