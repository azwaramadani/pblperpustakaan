<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman Ruangan</title>
    <!-- Link ke CSS Eksternal -->
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
      <div class="user-name"><a href="?route=User/viewProfile" style="text-decoration: none; color: black;"><p><?= htmlspecialchars($user['nama']) ?></p></a></div>
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
      
      <!-- MODIFIKASI 1: Link Keluar memicu Modal -->
      <a class="btn-logout" href="#" onclick="showLogoutModal(); return false;">Keluar</a>
    </div>
  </div>
</header>

<h2 class="title">Riwayat Peminjaman Saya</h2>
<div class="container">
    <?php if (empty($riwayat)): ?>
        <div class="empty-state">
            <h3>Belum Ada Riwayat Peminjaman.</h3>
            <a href="?route=User/ruangan" class="btn feedback">Lihat Ruangan</a>
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
                            <a href="#" onclick="showCancelModal('<?= $r['booking_id'] ?>'); return false;" class="btn batal btn-cancel">Batalkan</a>
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
    <div class="footer-content-wrapper">
        <div class="footer-left">
            <div class="footer-brand">
                    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy Ruang Study"class="footer-logo">
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
                    <a id="navigasipanduan"href="#">Panduan</a>
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

<!-- MODAL CANCEL POP-UP (Yang Sebelumnya) -->
<div id="cancelModal" class="modal-overlay">
    <div class="modal-content">
        <div class="icon-box-red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>
        <h2 class="modal-title">Apakah anda yakin ingin membatalkan booking ini?</h2>
        <div class="modal-actions">
            <a id="btnConfirmCancel" href="#" class="btn-modal-red">Ya</a>
            <button onclick="closeCancelModal()" class="btn-modal-white">Tidak</button>
        </div>
    </div>
</div>

<!-- MODIFIKASI 2: MODAL LOGOUT POP-UP (Baru) -->
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

        <!-- Teks -->
        <h2 class="modal-title">Apakah anda yakin ingin keluar dari akun ini?</h2>

        <!-- Tombol Aksi -->
        <div class="modal-actions">
            <!-- Tombol YA mengarah ke fungsi logout -->
            <a href="?route=Auth/logout" class="btn-modal-red">Ya</a>
            
            <!-- Tombol TIDAK menutup modal -->
            <button onclick="closeLogoutModal()" class="btn-modal-white">Tidak</button>
        </div>
    </div>
</div>

<!-- JAVASCRIPT -->
<script>
    /* LOGIC MODAL CANCEL (YANG LAMA) */
    const cancelModal = document.getElementById('cancelModal');
    const confirmBtn = document.getElementById('btnConfirmCancel');

    function showCancelModal(bookingId) {
        confirmBtn.href = "?route=Booking/cancel/" + encodeURIComponent(bookingId);
        cancelModal.classList.add('active');
    }

    function closeCancelModal() {
        cancelModal.classList.remove('active');
    }

    cancelModal.addEventListener('click', (e) => {
        if (e.target === cancelModal) closeCancelModal();
    });

    /* MODIFIKASI 3: LOGIC MODAL LOGOUT (BARU) */
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