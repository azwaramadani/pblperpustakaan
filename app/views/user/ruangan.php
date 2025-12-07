<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Ruangan | Rudy Ruang Study</title>
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleruangan.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<header class="navbar">
  <div class="logo">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40">
  </div>

  <nav class="nav-menu">
    <a href="?route=User/home">Beranda</a>
    <a href="?route=User/ruangan" class="active">Ruangan</a>
    <a href="?route=User/riwayat">Riwayat</a>
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

<main>
    <section class="title-section">
        <h2 class="title">Daftar Ruangan</h2>
        <p class="subtitle">Lihat ketersediaan ruangan untuk belajar individu, diskusi kelompok, atau kegiatan akademik lainnya.</p>

        <div class="room-switch-wrapper">
            <button class="tab-btn active">Ruang Study</button>
            <button class="tab-btn">Ruang Rapat</button>
        </div>
    </section>

    <div class="room-container">
    <?php if (empty($rooms)): ?>
        <p class="no-room">Tidak ada ruangan tersedia saat ini.</p>
    <?php else: ?>
        <?php foreach ($rooms as $r): ?>
            <div class="room-card">
                <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png"
                     alt="<?= htmlspecialchars($r['nama_ruangan']) ?>" class="room-img">
                <div class="room-info">
                    <div class="room-header">
                        <h3><?= htmlspecialchars($r['nama_ruangan']) ?></h3>
                        <span class="status <?= ($r['status'] == 'Tersedia') ? 'available' : 'unavailable' ?>">
                            <?= $r['status'] ?>
                        </span>
                    </div>
                    <div class="room-details">
                        <span class="capacity">
                            <i class="fas fa-user"></i> <?= $r['kapasitas_min'] ?> - <?= $r['kapasitas_max'] ?> Orang
                        </span>
                    </div>
                </div>
                <a href="?route=Booking/step1/<?= $r['room_id'] ?>" class="btn-book">Booking sekarang</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</main>

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

<script>
document.addEventListener("DOMContentLoaded", function() {
  const profileTrigger = document.querySelector('.profile-trigger');
  const profileDropdown = document.querySelector('.profile-dropdown');

  profileTrigger.addEventListener('click', function() {
    profileDropdown.classList.toggle('active');
  });
});
</script>
</body>
</html>
