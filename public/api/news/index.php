<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../core/Database.php';

try {
    $pdo = Database::connect();

    $q = trim((string) ($_GET['q'] ?? ''));

    $sql = "
        SELECT
            a.id,
            t.title,
            t.slug,
            t.excerpt,
            a.published_at
        FROM articles AS a
        INNER JOIN article_translations AS t
            ON t.article_id = a.id
        WHERE a.status = 'published'
            AND t.language_code = 'tg'
            AND t.translation_status = 'published'
    ";

    $params = [];

    if ($q !== '') {
        $sql .= " AND t.title LIKE :q";
        $params['q'] = '%' . $q . '%';
    }

    $sql .= " ORDER BY a.published_at DESC LIMIT 20";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC),
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

} catch (Throwable $e) {
    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        'error' => 'Не удалось загрузить новости.'
    ], JSON_UNESCAPED_UNICODE);
}
