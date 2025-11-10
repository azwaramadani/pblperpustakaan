<?php
session_start();

// Jika nanti kamu sudah punya database → login akan dicek di sini.
// Untuk sekarang, aku buat pengecekan password dummy dulu sebagai contoh.

if (isset($_POST["submit"])) {

    $nim = $_POST["nim"];
    $password = $_POST["password"];

    // Contoh cek awal (sementara, sebelum ada DB)
    if ($nim == "" || $password == "") {
        echo "<script>alert('NIM/NIP dan Password wajib diisi!');</script>";
    } else {
        // Nanti di sini cek ke database
        echo "<script>alert('Login berhasil (sementara)!'); window.location='dashboard.php';</script>";
    }
}

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
        <form method="POST" class="login-form">
            <label for="nim">NIM/NIP</label>
            <input id="nim" type="text" name="nim" placeholder="Masukkan NIM/NIP" autocomplete="off" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Masukkan Password" autocomplete="new-password" required>

            <button type="submit" name="submit" class="btn-login">Masuk</button>
        </form>

        <p class="register-text">
            Belum Punya Akun? <a href="register_user.php">Daftar</a>
        </p>
        <div class="divider">
            <span>Atau</span>
        </div>
        <a href="../guest/home.php" class="btn-guest">Masuk sebagai Tamu</a>
    </section>
</div>

</body>
</html>
