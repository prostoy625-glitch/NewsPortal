
<?php

$news = $news ?? [];
$categories = $categories ?? [];
$search = $search ?? '';
$categoryId = $categoryId ?? null;
$page = $page ?? 1;
$totalNews = $totalNews ?? 0;
$totalPages = $totalPages ?? 0;

// Безопасный вывод текста
$escape = static function ($value): string {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

// Создание ссылки с сохранением фильтров
$pageUrl = static function (int $pageNumber) use (
    $search,
    $categoryId
): string {
    $query = http_build_query([
        'search' => $search,
        'category' => $categoryId ?? '',
        'page' => $pageNumber
    ]);

    return '/NewsPortal/?' . $query . '#latest-news';
};

?>

<!-- HEADER -->
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

        <button
            class="menu-toggle"
            id="menuToggle"
            type="button"
            aria-label="Кушодани меню"
        >
            ☰
        </button>

    </div>
</header>

<main>

    <!-- BREAKING NEWS -->
    <section class="breaking-news">
        <div class="container">
            <strong>ХАБАРИ МУҲИМ:</strong>
            <span>Ба сомонаи NewsPortal хуш омадед!</span>
        </div>
    </section>

    <!-- HERO -->
    <section class="hero-section container">
        <div class="hero-content">

            <span class="section-label">
                ПОРТАЛИ ХАБАРҲО
            </span>

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

    <!-- LATEST NEWS -->
    <section
        class="latest-news container"
        id="latest-news"
    >

        <div class="section-heading">
            <div>
                <span class="section-label">
                    NEWS PORTAL
                </span>

                <h2>Хабарҳои охирин</h2>
            </div>
        </div>

        <!-- PRACTICE 13: SERVER SEARCH -->
        <form
            action="/NewsPortal/"
            method="GET"
            class="news-filters"
        >

            <label for="server-search">
                Ҷустуҷӯи хабарҳо
            </label>

            <input
                type="search"
                id="server-search"
                name="search"
                value="<?= $escape($search) ?>"
                placeholder="Калимаи ҷустуҷӯ..."
                maxlength="100"
            >

            <!-- CATEGORY FILTER -->
            <label for="category-filter">
                Категория
            </label>

            <select
                id="category-filter"
                name="category"
            >

                <option value="">
                    Ҳамаи категорияҳо
                </option>

                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?= (int) $category['id'] ?>"
                        <?= $categoryId === (int) $category['id']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= $escape($category['name']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <button
                type="submit"
                class="primary-button"
            >
                Ҷустуҷӯ
            </button>

            <a
                href="/NewsPortal/#latest-news"
                class="reset-filters"
            >
                Тоза кардан
            </a>

        </form>

        <!-- TOTAL NEWS -->
        <div class="news-results-info">
            <p>
                Шумораи хабарҳо:
                <strong><?= (int) $totalNews ?></strong>
            </p>
        </div>

        <!-- PRACTICE 11: LIVE SEARCH -->
        <div class="news-search">

            <label for="news-search-input">
                Ҷустуҷӯи фаврии хабарҳо
            </label>

            <input
                type="search"
                id="news-search-input"
                placeholder="Номи хабарро нависед..."
                autocomplete="off"
            >

            <p
                id="news-search-status"
                role="status"
                aria-live="polite"
            ></p>

            <div
                id="news-search-results"
                class="news-grid"
                hidden
            ></div>

        </div>

        <!-- NEWS CARDS -->
        <div class="news-grid">

            <?php if (empty($news)): ?>

                <div class="empty-news">

                    <h3>
                        Хабарҳо ёфт нашуданд.
                    </h3>

                    <p>
                        Калимаи дигарро ҷустуҷӯ кунед
                        ё филтрҳоро тоза намоед.
                    </p>

                </div>

            <?php else: ?>

                <?php foreach ($news as $item): ?>

                    <article class="news-card">

                        <div class="news-card-content">

                            <!-- CATEGORY -->
                            <span class="news-category">
                                <?= $escape(
                                    $item['category'] ?? 'Тоҷикистон'
                                ) ?>
                            </span>

                            <!-- TITLE -->
                            <h3>
                                <?= $escape($item['title'] ?? '') ?>
                            </h3>

                            <!-- EXCERPT -->
                            <p>
                                <?= $escape($item['excerpt'] ?? '') ?>
                            </p>

                            <!-- META -->
                            <div class="news-meta">

                                <span>
                                    Муаллиф:
                                    <?= $escape(
                                        $item['author'] ?? ''
                                    ) ?>
                                </span>

                                <?php if (!empty($item['published_at'])): ?>

                                    <time
                                        datetime="<?= $escape(
                                            $item['published_at']
                                        ) ?>"
                                    >
                                        <?= date(
                                            'd.m.Y',
                                            strtotime(
                                                $item['published_at']
                                            )
                                        ) ?>
                                    </time>

                                <?php endif; ?>

                            </div>

                            <!-- DETAILS -->
                            <a
                                href="/NewsPortal/news/<?= rawurlencode(
                                    $item['slug']
                                ) ?>"
                                class="read-more"
                            >
                                Муфассалтар →
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

        <!-- PRACTICE 13: PAGINATION -->
        <?php if ($totalPages > 1): ?>

            <nav
                class="pagination"
                aria-label="Саҳифаҳои хабарҳо"
            >

                <!-- PREVIOUS -->
                <?php if ($page > 1): ?>

                    <a
                        href="<?= $escape(
                            $pageUrl($page - 1)
                        ) ?>"
                        class="pagination-prev"
                    >
                        ← Қаблӣ
                    </a>

                <?php endif; ?>

                <!-- PAGE NUMBERS -->
                <?php for (
                    $i = 1;
                    $i <= $totalPages;
                    $i++
                ): ?>

                    <a
                        href="<?= $escape(
                            $pageUrl($i)
                        ) ?>"
                        class="<?= $i === $page
                            ? 'active'
                            : '' ?>"
                        <?= $i === $page
                            ? 'aria-current="page"'
                            : '' ?>
                    >
                        <?= $i ?>
                    </a>

                <?php endfor; ?>

                <!-- NEXT -->
                <?php if ($page < $totalPages): ?>

                    <a
                        href="<?= $escape(
                            $pageUrl($page + 1)
                        ) ?>"
                        class="pagination-next"
                    >
                        Баъдӣ →
                    </a>

                <?php endif; ?>

            </nav>

            <p class="pagination-info">
                Саҳифаи <?= (int) $page ?>
                аз <?= (int) $totalPages ?>
            </p>

        <?php endif; ?>

    </section>

</main>

<!-- FOOTER -->
<footer class="site-footer">

    <div class="container">

        <h2>NewsPortal</h2>

        <p>
            Сомонаи иттилоотии Тоҷикистон ва ҷаҳон.
        </p>

        <p>
            © <?= date('Y') ?> NewsPortal.
            Ҳамаи ҳуқуқҳо ҳифз шудаанд.
        </p>

    </div>

</footer>
