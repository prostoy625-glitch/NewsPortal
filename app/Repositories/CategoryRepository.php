
<?php

require_once __DIR__ . '/../../core/Database.php';

class CategoryRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    // Получить все категории на таджикском языке
    public function getAll(): array
    {
        $sql = "
            SELECT
                c.id,
                ct.name
            FROM categories AS c
            INNER JOIN category_translations AS ct
                ON ct.category_id = c.id
            WHERE ct.language_code = :language
            ORDER BY c.id ASC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'language' => 'tg'
        ]);

        return $stmt->fetchAll();
    }

    // Получить категорию по ID
    public function getById(int $id): array|false
    {
        $sql = "
            SELECT
                c.id,
                ct.name
            FROM categories AS c
            INNER JOIN category_translations AS ct
                ON ct.category_id = c.id
            WHERE c.id = :id
              AND ct.language_code = :language
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id,
            'language' => 'tg'
        ]);

        return $stmt->fetch();
    }
}
