
<?php
$news = $news ?? [];
?>

<header class="site-header">
    <div class="container header-content">
        <a href="/NewsPortal/" class="logo">
            News<span>Portal</span>
        </a>

        <nav class="navigation" id="navList">
            <a href="/NewsPortal/">Асосӣ</a>
        </nav>

        <button class="menu-toggle" id="menuToggle" type="button">
            ☰
        </button>
    </div>
</header>

<main class="container">
    <article class="news-detail">

        <a href="/NewsPortal/" class="read-more">
            ← Бозгашт ба саҳифаи асосӣ
        </a>

        <div class="news-detail-content">

            <span class="news-category">
                <?= htmlspecialchars($news['category'] ?? 'Тоҷикистон', ENT_QUOTES, 'UTF-8') ?>
            </span>

            <h1>
                <?= htmlspecialchars($news['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <div class="news-meta">
                <span>
                    Муаллиф:
                    <?= htmlspecialchars($news['author'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </span>

                <?php if (!empty($news['published_at'])): ?>
                    <time datetime="<?= htmlspecialchars($news['published_at'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= date('d.m.Y', strtotime($news['published_at'])) ?>
                    </time>
                <?php endif; ?>
            </div>

            <?php if (!empty($news['excerpt'])): ?>
                <p class="news-excerpt">
                    <?= htmlspecialchars($news['excerpt'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <div class="news-detail-text">
                <?= nl2br(htmlspecialchars($news['content'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
            </div>

        </div>
    </article>
</main>

<footer class="site-footer">
    <div class="container">
        <h2>NewsPortal</h2>
        <p>Сомонаи иттилоотии Тоҷикистон ва ҷаҳон.</p>
        <p>© <?= date('Y') ?> NewsPortal. Ҳамаи ҳуқуқҳо ҳифз шудаанд.</p>
    </div>
</footer>
