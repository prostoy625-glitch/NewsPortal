
<?php

require_once __DIR__ . '/../../core/Database.php';

class MediaController
{
    public function upload(): void
    {
        // Проверяем метод запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Метод не разрешён.');
        }

        // Проверяем наличие файла
        if (!isset($_FILES['image'])) {
            exit('Файл не выбран.');
        }

        $file = $_FILES['image'];

        // Проверяем ошибки загрузки
        if ($file['error'] !== UPLOAD_ERR_OK) {
            exit('Ошибка при загрузке файла.');
        }

        // Ограничиваем размер: 5 МБ
        if ($file['size'] > 5 * 1024 * 1024) {
            exit('Размер файла не должен превышать 5 МБ.');
        }

        // Проверяем настоящий MIME-тип
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowedTypes[$mimeType])) {
            exit('Разрешены только JPG, PNG и WebP.');
        }

        // Проверяем, что файл действительно является изображением
        if (getimagesize($file['tmp_name']) === false) {
            exit('Некорректное изображение.');
        }

        // Получаем данные из формы
        $articleId = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);

        if (!$articleId || $articleId < 1) {
            exit('Некорректный ID новости.');
        }

        // Пока используем тестовый ID пользователя.
        // Позже заменим его авторизацией и сессией.
        $userId = 1;

        $extension = $allowedTypes[$mimeType];
        $fileName = bin2hex(random_bytes(16)) . '.' . $extension;

        $uploadDirectory = __DIR__ . '/../../storage/uploads/';
        $filePath = $uploadDirectory . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            exit('Не удалось сохранить изображение.');
        }

        try {
            $pdo = Database::connect();

            $check = $pdo->prepare(
                'SELECT id FROM articles WHERE id = :id'
            );
            $check->execute(['id' => $articleId]);

            if (!$check->fetch()) {
                unlink($filePath);
                exit('Новость не найдена.');
            }

            $sql = "
                INSERT INTO media
                    (article_id, user_id, file_name, file_path, mime_type)
                VALUES
                    (:article_id, :user_id, :file_name, :file_path, :mime_type)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'article_id' => $articleId,
                'user_id' => $userId,
                'file_name' => $fileName,
                'file_path' => 'storage/uploads/' . $fileName,
                'mime_type' => $mimeType
            ]);

            echo 'Изображение успешно загружено!';

        } catch (PDOException $e) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            error_log($e->getMessage());
            http_response_code(500);
            echo 'Ошибка сохранения изображения.';
        }
    }
}
