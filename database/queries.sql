


USE news_portal;
SET NAMES utf8mb4;

-- 1. Получить опубликованные новости
-- вместе с автором и названием категории.
SELECT
    a.id,
    at.title,
    at.slug,
    u.full_name AS author,
    ct.name AS category,
    a.published_at
FROM articles AS a
JOIN article_translations AS at
    ON at.article_id = a.id
    AND at.language_code = 'ru'
JOIN users AS u
    ON u.id = a.author_id
LEFT JOIN category_translations AS ct
    ON ct.category_id = a.category_id
    AND ct.language_code = 'ru'
WHERE a.status = 'published'
ORDER BY a.published_at DESC;


-- 2. Поиск новостей по названию.
SELECT
    a.id,
    at.title,
    at.slug
FROM articles AS a
JOIN article_translations AS at
    ON at.article_id = a.id
WHERE at.language_code = 'ru'
  AND at.title LIKE '%технологии%'
ORDER BY a.created_at DESC;


-- 3. Количество новостей в каждой категории.
SELECT
    c.id,
    ct.name AS category,
    COUNT(a.id) AS article_count
FROM categories AS c
LEFT JOIN category_translations AS ct
    ON ct.category_id = c.id
    AND ct.language_code = 'ru'
LEFT JOIN articles AS a
    ON a.category_id = c.id
GROUP BY c.id, ct.name
ORDER BY article_count DESC;


-- 4. Новости, ожидающие проверки редактора.
SELECT
    a.id,
    at.title,
    a.status,
    u.full_name AS author,
    a.created_at
FROM articles AS a
JOIN article_translations AS at
    ON at.article_id = a.id
    AND at.language_code = 'ru'
JOIN users AS u
    ON u.id = a.author_id
WHERE a.status IN ('pending', 'submitted')
ORDER BY a.created_at ASC;


-- 5. Количество комментариев к каждой новости.
SELECT
    a.id,
    at.title,
    COUNT(c.id) AS comments_count
FROM articles AS a
LEFT JOIN article_translations AS at
    ON at.article_id = a.id
    AND at.language_code = 'ru'
LEFT JOIN comments AS c
    ON c.article_id = a.id
GROUP BY a.id, at.title
ORDER BY comments_count DESC;


-- 6. Пагинация: показать новости со второй страницы.
SELECT
    a.id,
    at.title,
    a.published_at
FROM articles AS a
JOIN article_translations AS at
    ON at.article_id = a.id
    AND at.language_code = 'ru'
WHERE a.status = 'published'
ORDER BY a.published_at DESC
LIMIT 10 OFFSET 10;


-- 7. Проверить план выполнения запроса.
EXPLAIN
SELECT
    id,
    status,
    created_at
FROM articles
WHERE status = 'published'
ORDER BY created_at DESC
LIMIT 20;
