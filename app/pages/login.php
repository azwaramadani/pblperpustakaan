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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - RUDY Ruang Study</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">

    <!-- CARD LOGO -->
    <div class="card logo-box">
        <img src="../../public/assets/image/Logo 1.png" alt="Rudy Logo">
    </div>

    <!-- CARD FORM -->
    <div class="card right-section">
        <h2>Masuk</h2>

        <form method="POST">

            <label>NIM/NIP</label>
            <input type="text" name="nim" placeholder="Masukkan NIM/NIP" autocomplete="off" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan Password" autocomplete="new-password" required>

            <button type="submit" name="submit" class="btn-login">Masuk</button>
        </form>

        <p class="register-text">
            Belum Punya Akun? <a href="signup.php">Daftar</a>
        </p>

        <p class="register-text">Atau</p>

        <button class="btn-Tamu">Masuk Sebagai Tamu</button>

    </div>

</div>

</body>
</html>
