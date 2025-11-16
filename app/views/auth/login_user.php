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

    <link rel="stylesheet" href="../../../public/assets/css/styleregister.css">
</head>

<body class="auth-body">

<div class="auth-wrapper">

    <section class="auth-card image-panel">
        <div class="image-overlay">
            <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy" class="panel-logo">
        </div>
    </section>

    <section class="auth-card form-panel">

        <div class="form-header">
            <h2>Masuk</h2>
        </div>

        <!-- TAMPILKAN ERROR JIKA ADA -->
        <?php if ($error): ?>
            <div class="auth-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- FORM LOGIN -->
        <form method="POST" class="login-form" action="?route=Auth/loginProcess">

            <label for="nim">NIM/NIP</label>
            <input id="nim"
                   type="text"
                   name="nim_nip"
                   placeholder="Masukkan NIM/NIP"
                   autocomplete="off"
                   required>

            <label for="password">Password</label>
            <input id="password"
                   type="password"
                   name="password"
                   placeholder="Masukkan Password"
                   autocomplete="new-password"
                   required>

            <button type="submit" name="submit" class="btn-login">Masuk</button>
        </form>

        <p class="register-text">Belum punya akun?</p>

        <a href="../guest/home.php" class="btn-guest">Daftar</a>
    </section>
</div>

</body>
</html>
