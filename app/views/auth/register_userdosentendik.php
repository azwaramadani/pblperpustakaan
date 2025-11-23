<?php
$errors = $errors ?? [];
$success = $success ?? null;
$old = $old ?? ['nim_nip' => '', 'jurusan' => '', 'nama' => '', 'no_hp' => '', 'email' => ''];
$jurusanList = $jurusanList ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Dosen/Tendik - Rudy Ruang Study</title>
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
            <h2>Daftar (Dosen/Tendik)</h2>
            <p>Isi data dosen atau tenaga pendidik sesuai identitas.</p>
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

        <?php if ($success): ?>
            <div class="auth-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="?route=Auth/registerDosen">
            <label for="nim_nip">NIM/NIP</label>
            <input id="nim_nip" type="text" name="nim_nip" placeholder="Masukkan NIM/NIP" value="<?= htmlspecialchars($old['nim_nip']) ?>" required>

            <label for="jurusan">Jurusan</label>
            <select id="jurusan" name="jurusan" class="select-input" required>
                <option value="">Pilih Jurusan</option>
                <?php foreach ($jurusanList as $jurusan): ?>
                    <option value="<?= htmlspecialchars($jurusan) ?>" <?= $old['jurusan'] === $jurusan ? 'selected' : '' ?>>
                        <?= htmlspecialchars($jurusan) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="nama">Nama</label>
            <input id="nama" type="text" name="nama" placeholder="Masukkan Nama" value="<?= htmlspecialchars($old['nama']) ?>" required>

            <label for="no_hp">No. Hp</label>
            <input id="no_hp" type="text" name="no_hp" placeholder="Masukkan Nomor HP" value="<?= htmlspecialchars($old['no_hp']) ?>" required>

            <label for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="Masukkan Email" autocomplete="off" value="<?= htmlspecialchars($old['email']) ?>" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Masukkan Password" autocomplete="new-password" required>

            <label for="confirm_password">Konfirmasi Password</label>
            <input id="confirm_password" type="password" name="confirm_password" placeholder="Konfirmasi Password" autocomplete="new-password" required>

            <button type="submit" class="btn-login">Daftar</button>
        </form>

        <p class="register-text">Sudah punya akun? <a href="?route=Auth/login">Masuk</a></p>
    </section>
</div>

<?php if ($success): ?>
<div class="modal-backdrop" id="successModal">
    <div class="modal-card">
        <h3>Berhasil Membuat Akun!</h3>
        <p>Silakan login dengan akun yang baru kamu buat.</p>
        <a href="?route=Auth/login" class="btn-login">Login</a>
    </div>
</div>
<?php endif; ?>

</body>
</html>