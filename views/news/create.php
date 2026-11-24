
<?php
$errors = $errors ?? [];
$old = $old ?? [];

function oldValue(array $old, string $key): string
{
    return htmlspecialchars(
        (string) ($old[$key] ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function fieldError(array $errors, string $key): string
{
    return htmlspecialchars(
        (string) ($errors[$key] ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<h1>Илова кардани хабар</h1>

<form
    id="news-create-form"
    method="post"
    action="/NewsPortal/news/create"
    novalidate
>
    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(
            $_SESSION['csrf_token'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <div>
        <label for="news-title">Сарлавҳаи хабар *</label>
        <input
            type="text"
            id="news-title"
            name="title"
            maxlength="255"
            value="<?= oldValue($old, 'title') ?>"
            required
        >
        <div id="news-title-error">
            <?= fieldError($errors, 'title') ?>
        </div>
    </div>

    <div>
        <label for="news-slug">Slug *</label>
        <input
            type="text"
            id="news-slug"
            name="slug"
            maxlength="255"
            placeholder="my-first-news"
            value="<?= oldValue($old, 'slug') ?>"
            required
        >
        <div><?= fieldError($errors, 'slug') ?></div>
    </div>

    <div>
        <label for="news-excerpt">Тавсифи кӯтоҳ</label>
        <textarea
            id="news-excerpt"
            name="excerpt"
            maxlength="1000"
        ><?= oldValue($old, 'excerpt') ?></textarea>
        <div id="news-excerpt-error">
            <?= fieldError($errors, 'excerpt') ?>
        </div>
    </div>

    <div>
        <label for="news-content">Матни хабар *</label>
        <textarea
            id="news-content"
            name="content"
            required
        ><?= oldValue($old, 'content') ?></textarea>
        <div id="news-content-error">
            <?= fieldError($errors, 'content') ?>
        </div>
    </div>

    <div>
        <label for="news-category">Категория *</label>
        <select
            id="news-category"
            name="category_id"
            required
        >
            <option value="">Категорияро интихоб кунед</option>
            <?php foreach (($categories ?? []) as $category): ?>
                <option
                    value="<?= (int) $category['id'] ?>"
                    <?= (string) ($old['category_id'] ?? '') ===
                        (string) $category['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars(
                        $category['name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div><?= fieldError($errors, 'category_id') ?></div>
    </div>

    <div>
        <label for="news-status">Статус *</label>
        <select id="news-status" name="status" required>
            <?php foreach (['draft', 'review', 'published'] as $status): ?>
                <option
                    value="<?= $status ?>"
                    <?= ($old['status'] ?? 'draft') === $status
                        ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div><?= fieldError($errors, 'status') ?></div>
    </div>

    <button type="submit">Нигоҳ доштани хабар</button>
</form>
