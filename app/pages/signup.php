<?php
session_start();

// Generate captcha saat halaman terbuka / reload
if (!isset($_SESSION["captcha"])) {
    $_SESSION["captcha"] = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
}

// Jika tombol daftar ditekan
if (isset($_POST["submit"])) {

    // Cek captcha
    if ($_POST["kode_captcha"] != $_SESSION["captcha"]) {
        echo "<script>alert('Captcha salah!'); window.location='signup.php';</script>";
        exit;
    }

    echo "<script>alert('Captcha benar! Pendaftaran berhasil.'); window.location='signup.php';</script>";

    $_SESSION["captcha"] = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - RUDY Ruang Study</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">

    <!-- CARD LOGO -->
    <div class="card logo-box">
        <img src="Logo 1.png" alt="Rudy Logo">
    </div>

    <!-- CARD FORM -->
    <div class="card right-section">
        <h2>Daftar</h2>

        <form method="POST" enctype="multipart/form-data">

            <label>NIM/NIP</label>
            <input type="text" name="nim" placeholder="Masukkan NIM/NIP" required>

            <label>Nama</label>
            <input type="text" name="nama" placeholder="Masukkan Nama anda" required>

            <label>No.Hp</label>
            <input type="text" name="nohp" placeholder="Masukkan Nomor ponsel anda" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email anda" autocomplete="off" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan Password anda" autocomplete="new-password" required>

            <label>Konfirmasi Password</label>
            <input type="password" name="konfirm_password" placeholder="Konfirmasi password anda" autocomplete="new-password" required>

            <label>Bukti sudah aktivasi akun kubaca</label>
            <label class="file-label">
                Pilih File
                <input type="file" name="bukti_aktivasi" accept="image/*" required hidden>
            </label>
            <span id="file-chosen">Belum ada file</span>

            <label>Kode Keamanan</label>
            <div style="font-size: 24px; font-weight:bold; letter-spacing: 4px; background:#e4e4e4; padding:8px; width:max-content; border-radius:5px;">
                <?php echo $_SESSION["captcha"]; ?>
            </div>
            <input type="text" name="kode_captcha" placeholder="Masukkan kode di atas" required>

            <button type="submit" name="submit" class="btn-login">Daftar</button>

        </form>

        <p class="register-text">
            Sudah Punya Akun? <a href="login.php">Masuk</a>
        </p>
    </div>

</div>

<script>
const fileInput = document.querySelector('input[type="file"]');
const fileText = document.getElementById('file-chosen');
fileInput.addEventListener('change', () => {
    fileText.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : "Belum ada file";
});
</script>

</body>
</html>
