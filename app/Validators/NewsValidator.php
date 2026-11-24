
<?php

class NewsValidator
{
    public static function validate(array $data): array
    {
        $errors = [];

        $title = trim((string) ($data['title'] ?? ''));
        $slug = trim((string) ($data['slug'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $excerpt = trim((string) ($data['excerpt'] ?? ''));
        $status = $data['status'] ?? '';

        if ($title === '') {
            $errors['title'] = 'Заголовок обязателен.';
        } elseif (mb_strlen($title, 'UTF-8') > 255) {
            $errors['title'] = 'Заголовок слишком длинный.';
        }

        if ($slug === '') {
            $errors['slug'] = 'Slug обязателен.';
        } elseif (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            $errors['slug'] = 'Некорректный slug.';
        }

        if ($content === '') {
            $errors['content'] = 'Содержание обязательно.';
        }

        if (mb_strlen($excerpt, 'UTF-8') > 1000) {
            $errors['excerpt'] = 'Описание слишком длинное.';
        }

        $categoryId = filter_var(
            $data['category_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($categoryId === false) {
            $errors['category_id'] = 'Некорректная категория.';
        }

        $authorId = filter_var(
            $data['author_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($authorId === false) {
            $errors['author_id'] = 'Некорректный автор.';
        }

        if (!in_array($status, ['draft', 'review', 'published'], true)) {
            $errors['status'] = 'Некорректный статус.';
        }

        return $errors;
    }
}
