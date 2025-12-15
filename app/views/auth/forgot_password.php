<?php
session_start();
$error = $_SESSION['flash_error'] ?? null;
$success = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Rudy Ruang Study</title>
    <!-- Memuat file CSS yang sama dengan login -->
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleregister.css?v=1.5">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Work+Sans:wght@500;600&display=swap" rel="stylesheet">
</head>

<body class="auth-body login-page">

<div class="auth-wrapper">

    <!-- BAGIAN KIRI: GAMBAR (Sticky) -->
    <section class="auth-card image-panel">
        <div class="image-overlay">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy" class="panel-logo">
        </div>
    </section>

    <!-- BAGIAN KANAN: FORM -->
    <section class="auth-card form-panel">
        <div class="form-content">
            
            <div class="form-header">
                <h2>Lupa Password?</h2>
                <p style="color: #666; margin-bottom: 20px;">Masukkan email Anda, kami akan mengirimkan link untuk mereset password.</p>
            </div>

            <!-- Menampilkan Pesan Error -->
            <?php if ($error): ?>
                <div class="auth-error" style="color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Menampilkan Pesan Sukses -->
            <?php if ($success): ?>
                <div class="auth-success" style="color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form" action="?route=Auth/processForgotPassword">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email"
                           type="email"
                           name="email"
                           class="form-control"
                           placeholder="Masukkan Email Anda"
                           autocomplete="off"
                           required>
                </div>

                <button type="submit" class="btn-login">Kirim Link Reset</button>
            </form>

            <div class="register-footer">
                Kembali ke <a href="?route=Auth/login" class="btn-guest">Halaman Masuk</a>
            </div>

        </div>
    </section>
</div>

</body>
</html>