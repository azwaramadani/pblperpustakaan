<?php
$user     = $data['user'];
$booking  = $data['booking'];
$feedback = $data['feedback']; // null jika belum ada
$err      = Session::get('flash_error');
Session::set('flash_error', null);
$success  = Session::get('flash_success');
Session::set('flash_success', null);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Feedback - <?= htmlspecialchars($booking['kode_booking']) ?></title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleriwayat.css">
</head>

<body>
<header class="navbar">
  <div class="logo">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40">
  </div>
  <nav class="nav-menu">
    <a href="?route=User/home">Beranda</a>
    <a href="?route=User/ruangan">Ruangan</a>
    <a href="?route=User/riwayat" class="active">Riwayat</a>
  </nav>
  <div class="profile-dropdown">
    <div class="profile-trigger">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
      <div class="user-name"><a href="?route=User/viewProfile"><p><?= htmlspecialchars($user['nama']) ?></p></a></div>
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
  <section class="title-section" style="text-align:center; margin:30px 0;">
    <h2 class="title">Beri Feedback</h2>
    <p class="subtitle">Kode Booking: <strong><?= htmlspecialchars($booking['kode_booking']) ?></strong> </p>
  </section>

  <div class="feedback-wrap">
    <?php if ($err): ?><div class="alert error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <p>
      <strong>Ruangan:</strong> <?= htmlspecialchars($booking['nama_ruangan']) ?> | 
      <strong>Tanggal Booking:</strong> <?= htmlspecialchars($booking['tanggal']) ?> | 
      <strong>Jam Booking:</strong> <?= htmlspecialchars($booking['jam_mulai']) ?> - <?= htmlspecialchars($booking['jam_selesai']) ?>
    </p>

    <?php if ($feedback): ?>
      <?php $statusText = !empty($feedback['puas']) ? 'Puas' : 'Tidak Puas'; ?>
        <p><strong>Feedback Anda (sudah terkirim):</strong></p>
        <p>Status: <?= htmlspecialchars($statusText) ?></p>
        <p><?= nl2br(htmlspecialchars($feedback['komentar'])) ?></p>
    <?php else: ?>
      <form action="?route=Feedback/store" method="POST">
        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>">

        <p><strong>Apakah Anda puas?</strong></p>
        <div style="margin-bottom:14px;">
          <label>
            <input type="radio" name="rating" value="Puas" checked> Puas
          </label>
          <label style="margin-left:12px;">
            <input type="radio" name="rating" value="Tidak Puas"> Tidak Puas
          </label>
        </div>

        <p><strong>Ada saran atau masukan?</strong></p>
        <textarea class="textarea" name="komentar" placeholder="Tulis feedback di sini..." required></textarea>

        <div style="margin-top:16px;">
          <a href="?route=User/riwayat" class="btn" style="margin-left:8px; text-decoration:none; padding:10px 16px; border-radius:10px; background:#4FD1C5; color:#000;">Kembali</a>
          <button type="submit" class="btn-submit">Simpan</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</main>

<footer class="footer">
    <div class="footer-content-wrapper">
        <div class="footer-left">
            <div class="footer-brand">
                <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy Ruang Study" class="footer-logo">
            </div>
            <p class="footer-description">
                Rudi Ruangan Studi adalah platform peminjaman ruangan perpustakaan yang membantu mahasiswa dan staf mengatur penggunaan ruang belajar dengan mudah dan efisien.
            </p>
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
    </div>
</footer>
<!-- MODAL LOGOUT POP-UP -->
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

<!-- JAVASCRIPT LOGOUT -->
<script>
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
