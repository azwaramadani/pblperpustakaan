<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleforgotpassword.css?v=1.2">
</head>
<body>

    <div class="card">

        <div class="logo-area">
            <div class="logo-icon">L</div>
            <span class="logo-text">LibRoomPNJ</span>
        </div>

        <h2 class="heading">Lupa Password</h2>
        <p class="subtext">
            Masukkan email akun Anda. Kami akan mengirimkan link untuk reset password.
        </p>

        <!-- Flash Messages -->
        <?php if (!empty($success = $flash['success'] ?? '')): ?>
            <div class="flash success">
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($error = $flash['error'] ?? '')): ?>
            <div class="flash error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="?route=Auth/sendResetLink" novalidate>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="contoh@email.com"
                        autocomplete="email"
                        required
                    >
                    <span class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="2" y="4" width="20" height="16" rx="3"/>
                            <polyline points="2,4 12,13 22,4"/>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="actions">
                <a href="?route=Auth/login" class="back-link">Kembali ke Login</a>
                <button type="submit" class="btn-submit">Kirim Link Reset</button>
            </div>

        </form>
    </div>

    <footer class="page-footer">
        <a href="#">Bantuan</a>
        <a href="#">Privasi</a>
        <a href="#">Ketentuan</a>
    </footer>

</body>
</html>