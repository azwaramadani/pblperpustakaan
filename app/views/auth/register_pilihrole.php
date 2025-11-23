<?php
$error = Session::get('flash_error');
Session::set('flash_error', null);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - Rudy Ruang Study</title>
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleregister.css">
</head>
<body class="auth-body">
<div class="auth-wrapper">
    <section class="auth-card image-panel">
        <div class="image-overlay">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy" class="panel-logo">
        </div>
    </section>

    <section class="auth-card form-panel">
        <div class="form-header">
            <h2>Pilih Role</h2>
            <p>Pilih tipe akun sebelum melanjutkan registrasi.</p>
        </div>

        <?php if ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="?route=Auth/chooseRole">
            <label for="role">Daftar sebagai</label>
            <select id="role" name="role" class="select-input" required>
                <option value="">-- Pilih role --</option>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen / Tenaga Pendidik</option>
            </select>
            <button type="submit" class="btn-login">Lanjut</button>
        </form>

        <p class="register-text">Sudah punya akun? <a href="?route=Auth/login">Masuk</a></p>
    </section>
</div>
</body>
</html>