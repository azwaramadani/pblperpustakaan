<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman Ruangan</title>
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleriwayat.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

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
  <div class="profile-dropdown">
    <div class="profile-trigger">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
      <div class="user-name"><p><?= htmlspecialchars($user['nama']) ?></p></div>
    </div>
    <div class="profile-card">
      <p><strong><?= htmlspecialchars($user['nama']) ?></strong></p>
      <p><?= htmlspecialchars($user['role']) ?></p>
      <p><?= htmlspecialchars($user['unit'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['jurusan'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['program_studi'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['nim_nip']) ?></p>
      <p><?= htmlspecialchars($user['no_hp']) ?></p>
      <p><?= htmlspecialchars($user['email']) ?></p>
      <a class="btn-logout" href="?route=Auth/logout">Keluar</a>
    </div>
  </div>
</header>

<h2 class="title">Riwayat Peminjaman Saya</h2>
<div class="container">
    <?php if (empty($riwayat)): ?>
        <div class="empty-state">
            <h3>Belum Ada Riwayat Peminjaman</h3>
            <p>Anda belum pernah melakukan peminjaman ruangan.</p>
            <a href="?route=User/ruangan" class="btn btn-primary">Lihat Ruangan Tersedia</a>
        </div>
    <?php else: ?>
        <?php foreach ($riwayat as $r): ?>
            <div class="card">
                <div class="info">
                    <h3><?= htmlspecialchars($r['nama_ruangan']) ?></h3>
                    <p><strong>Kode Booking:</strong> <?= htmlspecialchars($r['kode_booking']) ?></p>
                    <p><strong>Pembuat Booking:</strong> <?= htmlspecialchars($user['nama']) ?></p>
                    <p><strong>Waktu Peminjaman:</strong> <?= htmlspecialchars($r['tanggal']) ?></p>
                    <p><strong>Jam Peminjaman:</strong> <?= htmlspecialchars($r['jam']) ?></p>
                    <p><strong>Nama Penanggung Jawab:</strong> <?= htmlspecialchars($r['penanggung']) ?></p>
                    <p><strong>NIM Penanggung Jawab:</strong> <?= htmlspecialchars($r['nim']) ?></p>
                    <p><strong>Email Penanggung Jawab:</strong> <?= htmlspecialchars($r['email']) ?></p>
                    <p><strong>NIM Anggota Peminjam Ruangan:</strong> <?= htmlspecialchars($r['nim_ruangan']) ?></p>
                    <p><strong>Waktu Dibuat:</strong> <?= htmlspecialchars($r['created_at']) ?></p>
                    <p><strong>Status:</strong> 
                        <span class="status <?= htmlspecialchars(strtolower($r['status'])) ?>">
                            <?= htmlspecialchars($r['status']) ?>
                        </span>
                    </p>
                </div>

                <div class="gambar">
                    <img src="<?= htmlspecialchars($r['gambar']) ?>" 
                        alt="<?= htmlspecialchars($r['nama_ruangan']) ?>"
                        onerror="this.src='<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png'">
                    <div class="btn-group">
                        <?php if ($r['status'] == 'Disetujui'): ?>
                            <a href="?route=Booking/editForm/<?= urlencode($r['booking_id']) ?>" class="btn ubah">Ubah</a>
                            <a href="?route=Booking/cancel/<?= urlencode($r['booking_id']) ?>" class="btn batal btn-cancel">Batalkan</a>
                        <?php elseif ($r['status'] == 'Selesai' && !$r['sudah_feedback']): ?>
                            <a href="?route=Feedback/form/<?= urlencode($r['booking_id']) ?>" class="btn feedback">Beri Feedback</a>
                        <?php elseif ($r['status'] == 'Selesai' && $r['sudah_feedback']): ?>
                            <a href="?route=Feedback/form/<?= urlencode($r['booking_id']) ?>" class="btn feedback">Lihat Feedback Saya</a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer class="footer">
  <div class="footer-brand">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Rudy">
    <p>Rudy Ruang Study - platform peminjaman ruangan study yang praktis, transparan, dan terintegrasi.</p>
  </div>

  <div class="footer-nav">
    <div>
      <h4>Navigasi</h4>
      <a href="?route=user/home">Beranda</a>
      <a href="?route=user/ruangan">Ruangan</a>
      <a id="navigasipanduan" href="#">Panduan</a>
    </div>
    
    <div>
      <h4>Kontak</h4>
      <a href="mailto:PerpusPNJ@email.com">PerpusPNJ@email.com</a>
      <a href="tel:0822123456780">0822123456780</a>
      <p>Kampus PNJ, Depok</p>
    </div>
  </div>
</footer>

</body>
</html>
