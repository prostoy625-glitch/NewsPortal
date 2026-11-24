
<!DOCTYPE html>
<html lang="tg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'NewsPortal', ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="stylesheet" href="/NewsPortal/public/assets/css/style.css">
</head>
<body>

    <?= $content ?? '' ?>

    <script src="/NewsPortal/public/assets/js/main.js"></script>
<script type="module" src="/NewsPortal/public/assets/js/app.js"></script>

</body>
</html>
