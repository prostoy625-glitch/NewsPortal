
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
            <a href="#tajikistan">Тоҷикистон</a>
            <a href="#world">Ҷаҳон</a>
            <a href="#politics">Сиёсат</a>
            <a href="#economy">Иқтисод</a>
            <a href="#technology">Технология</a>
            <a href="#sports">Варзиш</a>
        </nav>

        <button class="menu-toggle" id="menuToggle" type="button">
            ☰
        </button>

    </div>
</header>

<main>

    <section class="breaking-news">
        <div class="container">
            <strong>ХАБАРИ МУҲИМ:</strong>
            <span>Ба сомонаи NewsPortal хуш омадед!</span>
        </div>
    </section>

    <section class="hero-section container">
        <div class="hero-content">
            <span class="section-label">ПОРТАЛИ ХАБАРҲО</span>

            <h1>Хабарҳои Тоҷикистон ва ҷаҳон</h1>

            <p>
                Манбаи боэътимоди иттилоот дар бораи
                рӯйдодҳои муҳим, сиёсат, иқтисод,
                технология ва ҳаёти ҷомеа.
            </p>

            <a href="#latest-news" class="primary-button">
                Хондани хабарҳо
            </a>
        </div>
    </section>

    <section class="latest-news container" id="latest-news">

        <div class="section-heading">
            <div>
                <span class="section-label">NEWS PORTAL</span>
                <h2>Хабарҳои охирин</h2>
            </div>
        </div>

        <div class="news-grid">

            <?php if (empty($news)): ?>

                <div class="empty-news">
                    <h3>Ҳоло хабаре нашр нашудааст.</h3>
                    <p>Баъдтар боз ба сомона ташриф оред.</p>
                </div>

            <?php else: ?>

                <?php foreach ($news as $item): ?>

                    <article class="news-card">
                        <div class="news-card-content">

                            <span class="news-category">
                                <?= htmlspecialchars($item['category'] ?? 'Тоҷикистон', ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <h3>
                                <?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p>
                                <?= htmlspecialchars($item['excerpt'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <div class="news-meta">
                                <span>
                                    Муаллиф:
                                    <?= htmlspecialchars($item['author'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </span>

                                <?php if (!empty($item['published_at'])): ?>
                                    <time datetime="<?= htmlspecialchars($item['published_at'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= date('d.m.Y', strtotime($item['published_at'])) ?>
                                    </time>
                                <?php endif; ?>
                            </div>

                            <a href="#" class="read-more">
                                Муфассалтар →
                            </a>

                        </div>
                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="container">

        <h2>NewsPortal</h2>

        <p>Сомонаи иттилоотии Тоҷикистон ва ҷаҳон.</p>

        <p>
            © <?= date('Y') ?> NewsPortal.
            Ҳамаи ҳуқуқҳо ҳифз шудаанд.
        </p>

    </div>
</footer>
