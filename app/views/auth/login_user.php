<?php
session_start();
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Rudy Ruang Study</title>
    <!-- Memuat file CSS -->
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleregister.css?v=1.5">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Work+Sans:wght@500;600&display=swap" rel="stylesheet">
</head>

<body class="auth-body login-page">

<div class="auth-wrapper">

    <!-- BAGIAN KIRI: GAMBAR -->
    <section class="auth-card image-panel">
        <div class="image-overlay">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy" class="panel-logo">
        </div>
    </section>

    <!-- BAGIAN KANAN: FORM -->
    <section class="auth-card form-panel">

        <h2>Masuk</h2>

        <?php if ($error): ?>
            <div class="auth-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form" action="?route=Auth/loginProcess">
            
            
            <label for="nim">NIM/NIP</label>
            <input id="nim"
                   type="text"
                   name="nim_nip"
                   autocomplete="off"
                   required>

            <label for="password">Password</label>
            <input id="password"
                   type="password"
                   name="password"
                   autocomplete="new-password"
                   required>

            <!-- Lupa Password (Rata Kanan) -->
            <a href="?route=Auth/forgotPassword" class="forgot-password-link">Lupa Password?</a>

            <!-- Tombol Masuk (Center) -->
            <button type="submit" name="submit" class="btn-login">Masuk</button>
        </form>

        <!-- Footer (Center & Satu Baris) -->
        <div class="register-footer">
            Belum Punya Akun? <a href="?route=Auth/registerRole" class="btn-guest">Daftar</a>
        </div>

    </section>
</div>

</body>
</html>