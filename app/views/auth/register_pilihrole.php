<?php
// Pastikan session dimulai di file init/index utama, atau uncomment jika perlu:
// session_start();
$error = Session::get('flash_error');
Session::set('flash_error', null);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - Rudy Ruang Study</title>
    <!-- Menggunakan CSS yang sama dengan login -->
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleregister.css?v=1.5">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Work+Sans:wght@500;600&display=swap" rel="stylesheet">
</head>

<body class="auth-body">

<div class="auth-wrapper">

    <!-- BAGIAN KIRI: GAMBAR (Sama dengan Login) -->
    <section class="auth-card image-panel">
        <div class="image-overlay">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy" class="panel-logo">
        </div>
    </section>

    <!-- BAGIAN KANAN: FORM -->
    <section class="auth-card form-panel">

        <div class="form-header">
            <!-- Teks asli dipertahankan -->
            <h2>Pilih Role</h2>
            <p>Pilih tipe akun sebelum melanjutkan registrasi.</p>
        </div>

        <?php if ($error): ?>
            <div class="auth-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="?route=Auth/chooseRole">
            
            <label for="role">Daftar sebagai</label>
            <div class="select-wrapper">
                <select id="role" name="role" class="select-input" required>
                    <option value="" disabled selected>-- Pilih role --</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="tenaga kependidikan">Tenaga Kependidikan</option>
                </select>
                <!-- Ikon panah kustom bisa ditambahkan via CSS -->
            </div>

            <button type="submit" class="btn-login">Lanjut</button>
        </form>

        <div class="register-footer">
            Sudah punya akun? <a href="?route=Auth/login" class="btn-guest">Masuk</a>
        </div>

    </section>
</div>

</body>
</html>