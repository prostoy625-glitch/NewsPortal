


USE news_portal;
SET NAMES utf8mb4;

-- 1. Roles
INSERT IGNORE INTO roles (name, description) VALUES
('administrator', 'Full access to portal management'),
('editor', 'Reviews and publishes articles'),
('author', 'Creates and edits articles'),
('reader', 'Reads and comments on articles');

-- 2.  role IDs
SET @role_author = (
    SELECT id FROM roles WHERE name = 'author' LIMIT 1
);

SET @role_editor = (
    SELECT id FROM roles WHERE name = 'editor' LIMIT 1
);

SET @role_reader = (
    SELECT id FROM roles WHERE name = 'reader' LIMIT 1
);

-- 3. Demo users
-- Replace password placeholders with real PHP password_hash() values.
INSERT IGNORE INTO users
(role_id, full_name, email, password_hash, is_active)
VALUES
(@role_author, 'Demo Author', 'demo.author@newsportal.local',
 'REPLACE_WITH_PASSWORD_HASH', 1),

(@role_editor, 'Demo Editor', 'demo.editor@newsportal.local',
 'REPLACE_WITH_PASSWORD_HASH', 1),

(@role_reader, 'Demo Reader', 'demo.reader@newsportal.local',
 'REPLACE_WITH_PASSWORD_HASH', 1);

-- 4. Get user IDs
SET @author_id = (
    SELECT id FROM users
    WHERE email = 'demo.author@newsportal.local'
    LIMIT 1
);

SET @editor_id = (
    SELECT id FROM users
    WHERE email = 'demo.editor@newsportal.local'
    LIMIT 1
);

SET @reader_id = (
    SELECT id FROM users
    WHERE email = 'demo.reader@newsportal.local'
    LIMIT 1
);

-- 5. Create demo category only if it does not exist
INSERT INTO categories ()
SELECT
WHERE NOT EXISTS (
    SELECT 1
    FROM category_translations
    WHERE language_code = 'ru'
      AND slug = 'demo-tekhnologii'
);

SET @category_id = (
    SELECT category_id
    FROM category_translations
    WHERE language_code = 'ru'
      AND slug = 'demo-tekhnologii'
    LIMIT 1
);

-- 6. Category translations
INSERT IGNORE INTO category_translations
(category_id, language_code, name, slug)
VALUES
(@category_id, 'ru', 'Технологии', 'demo-tekhnologii'),
(@category_id, 'en', 'Technology', 'demo-technology');

-- 7. Create article if it does not exist
INSERT INTO articles (category_id, author_id, status, published_at)
SELECT @category_id, @author_id, 'published', CURRENT_TIMESTAMP
WHERE NOT EXISTS (
    SELECT 1
    FROM article_translations
    WHERE language_code = 'ru'
      AND slug = 'demo-newsportal-article'
);

SET @article_id = (
    SELECT article_id
    FROM article_translations
    WHERE language_code = 'ru'
      AND slug = 'demo-newsportal-article'
    LIMIT 1
);

-- 8. Article translations
INSERT IGNORE INTO article_translations
(article_id, language_code, title, slug, excerpt, content)
VALUES
(@article_id, 'ru',
 'Демонстрационная новость NewsPortal',
 'demo-newsportal-article',
 'Пример новости для проверки базы данных.',
 'Это демонстрационная статья для проекта NewsPortal.'),

(@article_id, 'en',
 'NewsPortal Demo Article',
 'demo-newsportal-article-en',
 'A sample article for testing.',
 'This is a demo article for NewsPortal.');

-- 9. Tags
INSERT IGNORE INTO tags (name, slug) VALUES
('Технологии', 'demo-technology-tag'),
('Новости', 'demo-news-tag');

-- 10. Connect tags with article
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT @article_id, id
FROM tags
WHERE slug IN ('demo-technology-tag', 'demo-news-tag');

-- 11. Demo comment
INSERT INTO comments (article_id, user_id, content, status)
SELECT @article_id, @reader_id,
       'Демонстрационный комментарий.', 'approved'
WHERE NOT EXISTS (
    SELECT 1
    FROM comments
    WHERE article_id = @article_id
      AND user_id = @reader_id
      AND content = 'Демонстрационный комментарий.'
);

-- 12. Editorial decision
INSERT INTO editorial_decisions
(article_id, reviewer_id, decision, reason)
SELECT @article_id, @editor_id, 'approved',
       'Демонстрационное решение редактора.'
WHERE NOT EXISTS (
    SELECT 1
    FROM editorial_decisions
    WHERE article_id = @article_id
      AND reviewer_id = @editor_id
      AND decision = 'approved'
);

-- 13. Notification
INSERT INTO notifications (user_id, message)
SELECT @author_id, 'Добро пожаловать в NewsPortal!'
WHERE NOT EXISTS (
    SELECT 1
    FROM notifications
    WHERE user_id = @author_id
      AND message = 'Добро пожаловать в NewsPortal!'
);
