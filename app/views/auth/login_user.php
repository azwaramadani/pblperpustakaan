<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Rudy Ruang Study</title>
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleregister.css?v=1.6">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Work+Sans:wght@500;600&display=swap" rel="stylesheet">
</head>

<!-- Menggunakan class auth-body agar layout konsisten -->
<body class="auth-body login-page">
<div class="auth-wrapper">

    <!-- BAGIAN KIRI: GAMBAR -->
    <section class="auth-card image-panel">
        <div class="image-overlay">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/libroompnj.png" alt="Logo Rudy" class="panel-logo">
        </div>
    </section>

    <!-- BAGIAN KANAN: FORM -->
    <section class="auth-card form-panel">
        <!-- Wrapper konten form agar rapi -->
        <div class="form-content">
            <div class="form-header">
                <h2>Masuk</h2>
            </div>
            <!-- Flash Messages -->
            <?php if (!empty($success = $flash['success'])): ?>
                <div class="flash success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($error = $flash['error'])): ?>
                <div class="flash error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form" action="?route=Auth/loginProcess">
                
                <div class="form-group">
                    <label for="nim">Username</label>
                    <input id="nim"
                           type="text"
                           name="username"
                           class="form-control"
                           placeholder="Masukkan NIM/NIP..."
                           autocomplete="off"
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password"
                           type="password"
                           name="password"
                           class="form-control"
                           placeholder="Masukkan Password"
                           autocomplete="new-password"
                           required>
                </div>

                <button type="submit" name="submit" class="btn-login">Masuk</button>
            </form>

            <!-- Footer (Center & Satu Baris) -->
            <div class="register-footer">
                Belum Punya Akun? <a href="?route=Auth/registerRole" class="btn-guest">Daftar</a>
            </div>
            
            <div class="register-footer">
                <a href="?route=Auth/forgotPassword" class="btn-guest">Lupa Password?</a>
            </div>
        </div>
    </section>
</div>

</body>
</html>