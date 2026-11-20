
<?php
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<div class="login-container">

    <div class="login-card">

        <h1>NewsPortal</h1>

        <h2>Воридшавӣ ба система</h2>

        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="/NewsPortal/login" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
