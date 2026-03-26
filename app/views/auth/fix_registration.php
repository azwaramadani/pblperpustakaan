<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbaiki Data Registrasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleforgotpassword.css?v=1.2">
</head>
<body>

    <div class="card">

        <div class="logo-area">
            <div class="logo-icon">L</div>
            <span class="logo-text">LibRoomPNJ</span>
        </div>

        <h2 class="heading">Perbaiki Data</h2>

        <div class="info-box">
            Silakan upload ulang bukti aktivasi Kubaca agar akun dapat diverifikasi kembali.
        </div>

        <!-- Flash Messages -->
        <?php if (!empty($success = $flash['success'])): ?>
            <div class="flash success">
                <span class="flash-icon">✓</span>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($error = $flash['error'])): ?>
            <div class="flash error">
                <span class="flash-icon">!</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Alasan Ditolak -->
        <?php if (!empty($user['rejection_reason'])): ?>
            <div class="rejection-box">
                <span class="rejection-icon">⚠</span>
                <span>Ditolak karena: <?= htmlspecialchars($user['rejection_reason']) ?></span>
            </div>
        <?php endif; ?>

        <form action="?route=Auth/submitFixRegistration" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-group label">Upload Bukti Aktivasi Kubaca</label>
                <div class="file-wrapper">
                    <span class="file-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                    </span>
                    <div class="file-label-text">
                        <span>Pilih file gambar</span>
                        <span>JPG, PNG, PDF — maks. 5MB</span>
                    </div>
                    <input type="file" name="bukti" required>
                </div>
            </div>

            <button type="submit" class="btn-submit-full">Submit Ulang</button>
        </form>

    </div>

    <footer class="page-footer">
        <a href="#">Bantuan</a>
        <a href="#">Privasi</a>
        <a href="#">Ketentuan</a>
    </footer>

</body>
</html>