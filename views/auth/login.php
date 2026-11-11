
<?php
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="tg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Воридшавӣ') ?></title>

    <link rel="stylesheet" href="/NewsPortal/public/assets/css/style.css">
</head>
<body>

<div class="login-container">

    <div class="login-card">

        <h1>NewsPortal</h1>
        <h2>Воридшавӣ ба система</h2>

        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/NewsPortal/login" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken) ?>"
            >

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password">Парол</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit">Ворид шудан</button>

        </form>

        <a href="/NewsPortal/">Бозгашт ба саҳифаи асосӣ</a>

    </div>

</div>

</body>
</html>
