<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      <p><strong><?= htmlspecialchars($user['role']) ?></strong></p>
      <p><?= htmlspecialchars($user['unit'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['jurusan'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['program_studi'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['nim_nip']) ?></p>
      <p><?= htmlspecialchars($user['no_hp']) ?></p>
      <p><?= htmlspecialchars($user['email']) ?></p>
      <a class="btn-logout" href="#" onclick="showLogoutModal(); return false;">Keluar</a>
    </div>
  </div>
</header>

<main>
    <section class="title-section">
        <h2 class="title">Daftar Ruangan</h2>
        <p class="subtitle">Lihat ketersediaan ruangan untuk belajar individu, diskusi kelompok, atau kegiatan akademik lainnya.</p>
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
                        <span class="status <?= htmlspecialchars($r['status_class'] ?? (($r['status'] == 'Tersedia') ? 'available' : 'unavailable')) ?>">
                            <?= htmlspecialchars($r['status_display'] ?? $r['status']) ?>
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

<!-- FOOTER YANG SUDAH DIPERBAIKI (SAMA DENGAN HOME & RIWAYAT) -->
<footer class="footer">
    <div class="footer-content-wrapper">
        <!-- Bagian Kiri: Logo & Deskripsi -->
        <div class="footer-left">
            <div class="footer-brand">
                <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy Ruang Study" class="footer-logo">
            </div>
            <p class="footer-description">
                Rudi Ruangan Studi adalah platform peminjaman ruangan perpustakaan yang membantu mahasiswa dan staf mengatur penggunaan ruang belajar dengan mudah dan efisien.
            </p>
        </div>

        <!-- Bagian Kanan: Navigasi & Kontak -->
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
    </div>
</footer>
<div id="logoutModal" class="modal-overlay">
    <div class="modal-content">
        <!-- Icon Logout -->
        <div class="icon-box-red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </div>

        <h2 class="modal-title">Apakah anda yakin ingin keluar dari akun ini?</h2>

        <div class="modal-actions">
            <a href="?route=Auth/logout" class="btn-modal-red">Ya</a>
            <button onclick="closeLogoutModal()" class="btn-modal-white">Tidak</button>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
  const profileTrigger = document.querySelector('.profile-trigger');
  const profileDropdown = document.querySelector('.profile-dropdown');

  profileTrigger.addEventListener('click', function() {
    profileDropdown.classList.toggle('active');
  });
});
// <-- JAVASCRIPT LOGOUT -->

    const logoutModal = document.getElementById('logoutModal');

    function showLogoutModal() {
        logoutModal.classList.add('active');
    }

    function closeLogoutModal() {
        logoutModal.classList.remove('active');
    }

    // Tutup jika klik di luar area putih
    logoutModal.addEventListener('click', (e) => {
        if (e.target === logoutModal) {
            closeLogoutModal();
        }
    });
</script>
</body>
</html>