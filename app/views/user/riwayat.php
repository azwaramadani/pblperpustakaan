<?php
$user = $data['user'];
$ruangan = $data['rooms'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman Ruangan</title>
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleriwayat.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- ===== Navbar ===== -->
<header class="navbar">
  <div class="logo">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" alt="Logo PNJ" height="40">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy" height="40">
  </div>
  <nav class="nav-menu">
    <a href="?route=User/home">Beranda</a>
    <a href="?route=User/ruangan">Ruangan</a>
    <a href="?route=User/riwayat" class="active">Riwayat</a>
  </nav>
  <div class="profile">
        <a href="profile.php">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
        </a>
        <div class="user-name">
            <p><?= htmlspecialchars($user_data['nama']) ?></p>
        </div>
    </div>
</header>

<!-- ===== Judul ===== -->
<h2 class="title">Riwayat Peminjaman Saya</h2>

<!-- ===== Konten Riwayat ===== -->
<div class="container">
    <?php foreach ($riwayat as $r): ?>
        <div class="card">
            <div class="info">
                <h3><?= htmlspecialchars($r['nama_ruangan']) ?></h3>
                <p><strong>Kode Booking:</strong> <?= htmlspecialchars($r['kode_booking']) ?></p>
                <p><strong>Waktu Peminjaman:</strong> <?= htmlspecialchars($r['tanggal']) ?></p>
                <p><strong>Jam Peminjaman:</strong> <?= htmlspecialchars($r['jam']) ?></p>
                <p><strong>Nama Penanggung Jawab:</strong> <?= htmlspecialchars($r['penanggung']) ?></p>
                <p><strong>NIM Penanggung Jawab:</strong> <?= htmlspecialchars($r['nim']) ?></p>
                <p><strong>Email Penanggung Jawab:</strong> <?= htmlspecialchars($r['email']) ?></p>
                <p><strong>NIM Peminjam Ruangan:</strong> <?= htmlspecialchars($r['nim_ruangan']) ?></p>
                <p><strong>Status:</strong> 
                    <span class="status <?= strtolower($r['status']) ?>">
                        <?= htmlspecialchars($r['status']) ?>
                    </span>
                </p>
            </div>

            <div class="gambar">
                <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="Ruangan">
                <div class="btn-group">
                    <?php if ($r['status'] == 'Disetujui'): ?>
                        <a href="?route=Booking/editForm/<?= urlencode($r['booking_id']) ?>" class="btn ubah">Ubah</a>
                        <a href="?route=Booking/cancel/<?= urlencode($r['booking_id']) ?>" class="btn batal btn-cancel">Batalkan</a>
                    <?php elseif ($r['status'] == 'Selesai' && !$r['sudah_feedback']): ?>
                        <a href="feedback.php?kode=<?= urlencode($r['kode_booking']) ?>" class="btn feedback">Beri Feedback</a>
                    <?php elseif ($r['status'] == 'Selesai' && $r['sudah_feedback']): ?>
                        <a href="feedback.php?kode=<?= urlencode($r['kode_booking']) ?>&lihat=true" class="btn feedback">Lihat Feedback Saya</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ===== Footer ===== -->
<footer class="footer">
    <div class="footer-brand">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy">
        <p>Rudi Ruangan Studi adalah platform peminjaman ruangan perpustakaan yang membantu mahasiswa dan staf mengatur penggunaan ruang belajar
dengan mudah dan efisien.</p>
    </div>

    <div class="footer-nav">
        <div>
            <h4>Navigasi</h4>
            <a href="#">Beranda</a>
            <a href="#">Daftar Ruangan</a>
            <a href="#">Panduan</a>
            <a href="#">Masuk</a>
        </div>
        <div>
            <h4>Bantuan</h4>
            <a href="#">FAQ</a>
            <a href="#">Aturan Ruangan</a>
        </div>
        <div>
            <h4>Kontak</h4>
            <p>PerpusPNJ@gmail.com</p>
            <p>0822-3245-6780</p>
            <p>Kampus PNJ, Depok</p>
        </div>
    </div>
</footer>

</body>
</html>
