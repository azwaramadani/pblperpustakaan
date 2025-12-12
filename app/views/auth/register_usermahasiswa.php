<?php
$errors = $errors ?? [];
$success = $success ?? null;
$old = $old ?? ['nim_nip' => '', 'jurusan' => '', 'program_studi' => '', 'nama' => '', 'no_hp' => '', 'email' => ''];
$jurusanList = $jurusanList ?? [];
$prodiList = $prodiList ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa - Rudy Ruang Study</title>
    <!-- Versi CSS -->
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleregister.css?v=2.2">
</head>

<body class="auth-body register-page">

<div class="auth-wrapper">
    <!-- BAGIAN KIRI: GAMBAR -->
    <section class="auth-card image-panel">
        <div class="image-overlay">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy" class="panel-logo">
        </div>
    </section>

    <!-- BAGIAN KANAN: FORM -->
    <section class="auth-card form-panel">
        
        <h2>Daftar Mahasiswa</h2>
        
        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="auth-error">
                <ul style="margin:0; padding-left:18px;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="auth-success" style="background:#d1fae5; color:#065f46; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST" enctype="multipart/form-data" action="?route=Auth/registerMahasiswa">
            
            <label for="nim_nip">NIM</label>
            <input id="nim_nip" type="text" name="nim_nip" placeholder="Masukkan NIM" value="<?= htmlspecialchars($old['nim_nip']) ?>" required>

            <label for="nama">Nama Lengkap</label>
            <input id="nama" type="text" name="nama" placeholder="Masukkan Nama Lengkap" value="<?= htmlspecialchars($old['nama']) ?>" required>

            <label for="no_hp">No. Handphone</label>
            <input id="no_hp" type="text" name="no_hp" placeholder="Contoh: 08123456789" value="<?= htmlspecialchars($old['no_hp']) ?>" required>

            <label for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="email@contoh.com" autocomplete="off" value="<?= htmlspecialchars($old['email']) ?>" required>

            <label for="jurusan">Jurusan</label>
            <select id="jurusan" name="jurusan" class="select-input" required>
                <option value="">Pilih Jurusan</option>
                <?php foreach ($jurusanList as $jurusan): ?>
                    <option value="<?= htmlspecialchars($jurusan) ?>" <?= $old['jurusan'] === $jurusan ? 'selected' : '' ?>>
                        <?= htmlspecialchars($jurusan) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="program_studi">Program Studi</label>
            <select id="program_studi" name="program_studi" class="select-input" required>
                <option value="">Pilih Program Studi</option>
                <?php foreach ($prodiList as $prodi): ?>
                    <option value="<?= htmlspecialchars($prodi) ?>" <?= $old['program_studi'] === $prodi ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prodi) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Buat Password" autocomplete="new-password" required>

            <label for="confirm_password">Konfirmasi Password</label>
            <input id="confirm_password" type="password" name="confirm_password" placeholder="Ulangi Password" autocomplete="new-password" required>

            <!-- Custom File Upload: Bukti Aktivasi -->
            <label style="display:block; margin-bottom:8px;">Bukti Aktivasi Akun Kubaca</label>
            <div class="file-upload-wrapper">
                <label for="bukti_aktivasi" class="file-label-btn">Pilih File</label>
                <input type="file" id="bukti_aktivasi" name="bukti_aktivasi" accept="image/*" required hidden>
                <span id="file-chosen">Belum ada file dipilih</span>
            </div>

            <!-- CAPTCHA CUSTOM -->
            <label>Kode Keamanan</label>
            <div class="custom-captcha-wrapper">
                <!-- 1. GAMBAR CAPTCHA -->
                <div class="captcha-img-box">
                    <!-- Saya menggunakan jalur manual yang lebih aman dan menghapus javascript auto-load yang berisiko error -->
                    <img src="<?= app_config()['base_url'] ?>/public/captcha.php?t=<?= mt_rand() ?>" id="captcha-image" alt="CAPTCHA">
                </div>
                <!-- 3. INPUT USER -->
                <input type="text" name="captcha_input" class="input-captcha" placeholder="Masukan kode" autocomplete="off" required>
            </div>

            <button type="submit" class="btn-login">Daftar</button>
        </form>

        <div class="register-footer">
            Sudah punya akun? <a href="?route=Auth/login" class="btn-guest">Masuk</a>
        </div>
        <div class="register-footer">
            Ganti Role <a href="?route=Auth/pilihrole" class="btn-guest">Masuk</a>
        </div>
    </section>
</div>

<?php if ($success): ?>
<div class="modal-backdrop show-modal" id="successModal">
    <div class="modal-card custom-success-card">
        <!-- Close Button (X) -->
        <a href="?route=Auth/login" class="modal-close-icon">&times;</a>
        
        <!-- Icon Centang -->
        <div class="success-icon-wrapper">
            <div class="checkmark-circle">
                <div class="checkmark-stem"></div>
                <div class="checkmark-kick"></div>
            </div>
        </div>

        <!-- Text Content -->
        <h3>Akun berhasil diajukan</h3>
        <p class="modal-desc">
            Akun kamu sedang menunggu verifikasi admin.<br>
            Kamu akan mendapatkan notifikasi setelah disetujui.
        </p>

        <!-- Action Button -->
        <a href="?route=Auth/login" class="btn-understand">Mengerti</a>
    </div>
</div>
<?php endif; ?>

<script>
// Script Upload File
const fileInput = document.getElementById('bukti_aktivasi');
const fileText = document.getElementById('file-chosen');
if (fileInput) {
    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            fileText.textContent = this.files[0].name;
            fileText.style.color = "#333";
        } else {
            fileText.textContent = 'Belum ada file dipilih';
        }
    });
}

// Script Refresh Captcha
function refreshCaptcha() {
    const img = document.getElementById('captcha-image');
    // Ambil URL dasar dari gambar yang sudah ada, lalu ganti timestamp-nya
    // Cara ini lebih aman daripada menyusun ulang URL dari awal di JS
    let currentSrc = img.src.split('?')[0]; 
    img.src = currentSrc + '?t=' + new Date().getTime();
}
</script>

</body>
</html>