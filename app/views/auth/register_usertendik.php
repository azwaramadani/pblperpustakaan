<?php
// 1. LOGIKA FALLBACK: Agar data tidak hilang saat refresh/error validasi
if (empty($old) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
}

$errors = $errors ?? [];
$success = $success ?? null;

// Default values agar tidak error undefined index
$defaults = ['nim_nip' => '', 'unit' => '', 'nama' => '', 'no_hp' => '', 'email' => ''];
$old = array_merge($defaults, $old ?? []);

$unitList = $unitList ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tenaga Kependidikan - Rudy Ruang Study</title>
    <!-- Menggunakan CSS yang sama dengan halaman Dosen -->
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleregister.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<!-- Tambahkan class 'register-page' agar fitur sticky panel berfungsi -->
<body class="auth-body register-page">

<div class="auth-wrapper">
    <!-- BAGIAN KIRI: PANEL GAMBAR (Sticky) -->
    <section class="image-panel">
        <div class="image-overlay">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy" class="panel-logo">
        </div>
    </section>

    <!-- BAGIAN KANAN: FORM (Scrollable) -->
    <section class="form-panel">
        <div class="form-content">
            <div class="form-header">
                <h2>Daftar Tenaga Kependidikan</h2>
                <!-- Subtext dihilangkan agar desain bersih seperti Dosen -->
            </div>

            <?php if (!empty($errors)): ?>
                <div class="auth-error">
                    <ul style="margin:0; padding-left:18px;">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="?route=Auth/registerTendik">
                
                <!-- Field NIP -->
                <div class="form-group">
                    <label for="nim_nip">NIP</label>
                    <input id="nim_nip" type="text" name="nim_nip" class="form-control" placeholder="Masukkan NIP" value="<?= htmlspecialchars($old['nim_nip']) ?>" required>
                </div>

                <!-- Field Unit (Khusus Tendik) -->
                <div class="form-group">
                    <label for="unit">Unit</label>
                    <div class="select-wrapper">
                        <select id="unit" name="unit" class="form-control select-input" required>
                            <option value="">Pilih Unit</option>
                            <?php foreach ($unitList as $unit): ?>
                                <option value="<?= htmlspecialchars($unit) ?>" <?= $old['unit'] === $unit ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($unit) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Field Nama -->
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input id="nama" type="text" name="nama" class="form-control" placeholder="Masukkan Nama" value="<?= htmlspecialchars($old['nama']) ?>" required>
                </div>

                <!-- Field No HP -->
                <div class="form-group">
                    <label for="no_hp">No. Hp</label>
                    <input id="no_hp" type="text" name="no_hp" class="form-control" placeholder="Masukkan Nomor HP" value="<?= htmlspecialchars($old['no_hp']) ?>" required>
                </div>

                <!-- Field Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" class="form-control" placeholder="Masukkan Email" autocomplete="off" value="<?= htmlspecialchars($old['email']) ?>" required>
                </div>

                <!-- Field Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" class="form-control" placeholder="Masukkan Password" autocomplete="new-password" required>
                </div>

                <!-- Field Konfirmasi Password -->
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input id="confirm_password" type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi Password" autocomplete="new-password" required>
                </div>

                <!-- Field Captcha -->
                <div class="form-group">
                    <label>Kode Keamanan</label>
                    <div class="custom-captcha-wrapper">
                        <div class="captcha-img-box">
                            <img src="<?= app_config()['base_url'] ?>/public/captcha.php?t=<?= mt_rand() ?>" id="captcha-image" alt="CAPTCHA" onclick="refreshCaptcha()" title="Klik untuk refresh">
                        </div>
                        <input type="text" name="captcha_input" class="form-control input-captcha" placeholder="Masukan kode" autocomplete="off" required>
                    </div> 
                </div>           

                <button type="submit" class="btn-login">Daftar</button>
            </form>

            <div class="register-footer">
                Sudah punya akun? <a href="?route=Auth/login" class="btn-guest">Masuk</a>
            </div> 
        </div>   
    </section>
</div>

<!-- MODAL SUKSES (Sama dengan Dosen) -->
<?php if ($success): ?>
<div class="modal-backdrop show-modal" id="successModal">
    <div class="modal-card custom-success-card">
        <a href="?route=Auth/login" class="modal-close-icon">&times;</a>
        <div class="success-icon-wrapper">
            <div class="checkmark-circle">
                <div class="checkmark-stem"></div>
                <div class="checkmark-kick"></div>
            </div>
        </div>
        <h3>Akun berhasil dibuat</h3>
        <a href="?route=Auth/login" class="btn-understand">Kembali ke halaman masuk</a>
    </div>
</div>
<?php endif; ?>

<script>
    function refreshCaptcha() {
        const img = document.getElementById('captcha-image');
        let currentSrc = img.src.split('?')[0]; 
        img.src = currentSrc + '?t=' + new Date().getTime();
    }
</script>

</body>
</html>