<?php
$badgeText = $puasPercent > 0 ? $puasPercent . '% Orang Puas' : 'Belum ada feedback';
$isEdit    = !empty($payload['booking_id'] ?? null);

// Helper base_url untuk preview
if (!function_exists('app_config')) {
    function app_config() { return ['base_url' => '']; }
}

// Batas minimal tanggal = hari ini (Asia/Jakarta)
$todayMin = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

//URL gambar ruangan (fallback ke pict contoh kalo kosong)
$imgPath = !empty($room['gambar_ruangan']) ? $room['gambar_ruangan'] : 'public/assets/image/contohruangan.png';
$imgUrl = preg_match('#^https?://#i', $imgPath) ? $imgPath: app_config()['base_url'] . '/' . ltrim($imgPath, '/');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $isEdit ? 'Ubah Peminjaman' : 'Pilih Tanggal & Jam' ?>
        - <?= htmlspecialchars($room['nama_ruangan']) ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking1.css?v=1.3">
</head>

<body>
    <header class="navbar">
        <div class="nav-left">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" alt="Logo PNJ">
            <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy">
        </div>

        <nav class="nav-menu">
            <a href="?route=User/home">Beranda</a>
            <a href="?route=User/ruangan" class="active">Ruangan</a>
            <a href="?route=User/riwayat">Riwayat</a>
        </nav>

        <div class="profile-dropdown">
            <div class="profile-trigger">
                <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
                <div class="user-name">
                    <a href="?route=User/viewProfile" style="text-decoration: none; color: black;">
                        <p><?= htmlspecialchars($user['nama']) ?></p>
                    </a>
                </div>
            </div>
            <div class="profile-card">
                <p><strong><?= htmlspecialchars($user['nama']) ?></strong></p>
                <p><?= htmlspecialchars($user['role']) ?></p>
                <p><?= htmlspecialchars($user['unit'] ?? '') ?></p>
                <p><?= htmlspecialchars($user['jurusan'] ?? '') ?></p>
                <p><?= htmlspecialchars($user['program_studi'] ?? '') ?></p>
                <a class="btn-logout" href="#" onclick="showLogoutModal(); return false;">Keluar</a>
            </div>
        </div>
    </header>

    <!-- =========================================================
        MAIN CONTENT
    ========================================================= -->
    <main class="main-container">
        <!-- Info Ruangan -->
        <div class="room-header">
            <div class="room-img-container">
                <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($room['nama_ruangan']) ?>">
            </div>
            <div class="room-info">
                <h1><?= htmlspecialchars($room['nama_ruangan']) ?></h1>
                <p><?= htmlspecialchars($room['deskripsi']) ?></p>
                <p class="capacity">
                    <strong>Kapasitas : <?= htmlspecialchars($room['kapasitas_min']) ?>
                    - <?= htmlspecialchars($room['kapasitas_max']) ?> orang
                    </strong>
                </p>
            </div>
        </div>

        <!-- persentase puas -->
        <div class="badge-wrapper">
            <div class="puas-badge">
                <?= htmlspecialchars($badgeText) ?>
            </div>
        </div>

        <!-- Form Booking -->
        <div class="booking-card">
            <h3><?= $isEdit ? 'Ubah jadwal peminjaman' : 'Pilih tanggal dan jam peminjaman' ?></h3>

            <!-- Flash error dari php (Session::set('flash_error')) dititipkan ke JS untuk ditampilkan sebagai modal -->
            <?php if (!empty($error = $flash['error'])): ?>
                <script>
                    window.__flashError = <?= json_encode(htmlspecialchars($error)) ?>;
                </script>
            <?php endif; ?>

            <!-- Jadwal yang sudah terpakai -->
            <div class="schedule-box">
                <h4>Waktu yang sudah dipinjam:</h4>
                <p>Anda tidak bisa memilih jam pada rentang di bawah ini.</p>
                <?php if (!empty($todayIntervals)): ?>
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($todayIntervals as $ti): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('H:i', strtotime($ti['jam_mulai']))) ?></td>
                                    <td><?= htmlspecialchars(date('H:i', strtotime($ti['jam_selesai']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="schedule-empty">Belum ada peminjaman hari ini.</p>
                <?php endif; ?>
            </div>

            <!-- Form Input Tanggal & Jam -->
            <form action="<?= $isEdit ? '?route=Booking/editStep2' : '?route=Booking/step2' ?>" method="POST">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($payload['booking_id']) ?>">
                <?php endif; ?>

                <!-- Room id dari ruangan yang dipilih -->
                <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Pilih tanggal</label>
                        <!-- Min di-set hari ini, weekend di-blok via JS -->
                        <input
                            type="date"
                            name="tanggal"
                            class="input-line"
                            required
                            min="<?= htmlspecialchars($todayMin) ?>"
                            value="<?= htmlspecialchars($payload['tanggal'] ?? '') ?>">
                    </div>
                    
                    <!-- Jam mulai & selesai -->
                    <div class="time-wrapper">
                        <div class="form-group time-box">
                            <label>Jam mulai</label>
                            <input
                                type="time"
                                name="jam_mulai"
                                class="input-line"
                                required
                                value="<?= htmlspecialchars($payload['jam_mulai'] ?? '') ?>">
                        </div>

                        <span class="sampai-text">-</span>

                        <div class="form-group time-box">
                            <label>Jam selesai</label>
                            <input
                                type="time"
                                name="jam_selesai"
                                class="input-line"
                                required
                                value="<?= htmlspecialchars($payload['jam_selesai'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Tombol aksi -->
                <div class="btn-action-row">
                    <a href="?route=<?= $isEdit ? 'User/riwayat' : 'User/ruangan' ?>" class="btn btn-back">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-next">
                        <?= $isEdit ? 'Lanjut Ubah' : 'Lanjut' ?>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content-wrapper">

            <div class="footer-left">
                <div class="footer-brand">
                    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png"
                         alt="Logo Rudy Ruang Study"
                         class="footer-logo">
                </div>
                <p class="footer-description">
                    Rudi Ruangan Studi adalah platform peminjaman ruangan perpustakaan yang membantu
                    mahasiswa dan staf mengatur penggunaan ruang belajar dengan mudah dan efisien.
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

    <!-- =========================================================
         MODAL: KONFIRMASI LOGOUT
    ========================================================= -->
    <div id="logoutModal" class="modal-overlay">
        <div class="modal-content">
            <div class="icon-box-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
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

    <!-- =========================================================
         MODAL: WARNING (flash error & validasi tanggal/jam)
    ========================================================= -->
    <div id="warningModal" class="modal-warning">
        <div class="modal-card">
            <div class="modal-icon">!</div>
            <p class="modal-title">Perhatian</p>
            <p class="modal-text" id="warningText">Pesan peringatan.</p>
            <div class="modal-actions">
                <button class="btn-modal-primary" type="button" onclick="closeWarning()">OK</button>
            </div>
        </div>
    </div>

    <script>
        // ---------------------------------------------------------
        // 1. MODAL WARNING (dipakai oleh flash error & validasi form)
        // ---------------------------------------------------------
        const warningModal = document.getElementById('warningModal');
        const warningText  = document.getElementById('warningText');

        function showWarning(msg) {
            warningText.textContent = msg;
            warningModal.classList.add('active');
        }

        function closeWarning() {
            warningModal.classList.remove('active');
        }

        // Tutup modal jika klik di luar area kartu
        warningModal.addEventListener('click', function (e) {
            if (e.target === warningModal) closeWarning();
        });

        
        // ---------------------------------------------------------
        // 2. FLASH ERROR — auto-trigger modal jika ada pesan dari server
        //    window.__flashError diisi PHP di dalam .booking-card
        // ---------------------------------------------------------
        if (typeof window.__flashError !== 'undefined' && window.__flashError) {
            showWarning(window.__flashError);
        }

        // ---------------------------------------------------------
        // 3. MODAL LOGOUT
        // ---------------------------------------------------------
        const logoutModal = document.getElementById('logoutModal');

        function showLogoutModal() {
            logoutModal.classList.add('active');
        }

        function closeLogoutModal() {
            logoutModal.classList.remove('active');
        }

        // Tutup modal jika klik di luar area kartu
        logoutModal.addEventListener('click', function (e) {
            if (e.target === logoutModal) closeLogoutModal();
        });
    </script>

</body>
</html>