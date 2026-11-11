<!DOCTYPE html>
<html lang="tg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="stylesheet" href="/NewsPortal/public/assets/css/style.css">
</head>
<body>

    <div class="dashboard-container">

        <h1>NewsPortal</h1>

        <h2>Панели идоракунӣ</h2>

        <div class="dashboard-welcome">
            Хуш омадед,
            <strong>
                <?= htmlspecialchars($fullName ?? '', ENT_QUOTES, 'UTF-8') ?>
            </strong>
        </div>

        <div class="dashboard-role">
            <p>Нақши шумо:</p>

            <h3>
                <?= htmlspecialchars($roleTitle ?? '', ENT_QUOTES, 'UTF-8') ?>
            </h3>

            <small>
                <?= htmlspecialchars($role ?? '', ENT_QUOTES, 'UTF-8') ?>
            </small>
        </div>
        <form action="/NewsPortal/logout" method="POST">
    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <button type="submit">Баромадан аз система</button>
</form>

        <a href="/NewsPortal/">Ба саҳифаи асосӣ</a>

    </div>

</body>
</html>