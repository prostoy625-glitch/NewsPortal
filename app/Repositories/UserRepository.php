
<?php

require_once __DIR__ . '/../../core/Database.php';

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    // Получить всех пользователей
    public function getAll(): array
    {
        $sql = "
            SELECT id, full_name
            FROM users
            ORDER BY id ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Получить пользователя по ID
    public function getById(int $id): array|false
    {
        $sql = "
            SELECT id, full_name
            FROM users
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Отчёт: количество новостей каждого автора
    public function getAuthorStatistics(): array
    {
        $sql = "
            SELECT
                u.id,
                u.full_name,
                COUNT(a.id) AS total_articles
            FROM users AS u
            LEFT JOIN articles AS a
                ON a.author_id = u.id
            GROUP BY u.id, u.full_name
            ORDER BY total_articles DESC, u.id ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
