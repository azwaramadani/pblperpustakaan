<?php
$badgeText = $puasPercent > 0 ? $puasPercent . '% Orang Puas' : 'Belum ada feedback';
$err       = Session::get('flash_error');
$success   = Session::get('flash_success');
Session::set('flash_error', null);
Session::set('flash_success', null);

$isEdit = !empty($payload['booking_id'] ?? null);

// Helper base_url untuk preview
if (!function_exists('app_config')) {
    function app_config() { return ['base_url' => '']; }
}

// Batas minimal tanggal = hari ini (Asia/Jakarta)
$todayMin = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

// Batas jam diperbolehkan
$minTime = '09:00';
$maxTime = '15:00';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Ubah Peminjaman' : 'Pilih Tanggal & Jam' ?> - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking1.css?v=1.2">
  <style>
    /* Flash message */
    .flash-message {
      padding: 12px 14px;
      border-radius: 4px;
      margin: 0 0 12px 0;
      font-weight: 600;
      font-size: 14px;
      line-height: 1.4;
    }
    .flash-success {
      background: #e5f6f3;
      color: #0f766e;
      border: 1px solid #b7e4dc;
    }
    .flash-warning {
      background: #e5f6f3;
      color: #0f766e;
      border: 1px solid #b7e4dc;
    }

    /* Modal warning custom */
    .modal-warning {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.6);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    .modal-warning.active { display: flex; }
    .modal-card {
      width: 320px;
      background: #fff;
      border-radius: 14px;
      padding: 20px 18px 16px;
      text-align: center;
      box-shadow: 0 20px 45px rgba(0,0,0,0.18);
      animation: pop 0.18s ease-out;
    }
    @keyframes pop { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-icon {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      margin: 0 auto 12px;
      display: grid;
      place-items: center;
      background: #ff5c5c;
      color: #fff;
      font-size: 28px;
      font-weight: 700;
    }
    .modal-title {
      font-size: 17px;
      font-weight: 700;
      margin: 0 0 10px;
      color: #222;
    }
    .modal-text {
      font-size: 14px;
      margin: 0 0 16px;
      color: #444;
      line-height: 1.5;
    }
    .modal-actions {
      display: flex;
      gap: 8px;
      justify-content: center;
    }
    .btn-modal-primary {
      flex: 1;
      background: #ff5c5c;
      color: #fff;
      border: none;
      border-radius: 10px;
      padding: 10px 12px;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.1s ease, box-shadow 0.1s ease;
      box-shadow: 0 6px 16px rgba(255,92,92,0.35);
    }
    .btn-modal-primary:hover { transform: translateY(-1px); }
  </style>
</head>
<body>

  <!-- NAVBAR -->
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
        <div class="user-name"><a href="?route=User/viewProfile" style="text-decoration: none; color: black;"><p><?= htmlspecialchars($user['nama']) ?></p></a></div>
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

  <main class="main-container">  
    <div class="room-header">
      <div class="room-img-container">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="<?= htmlspecialchars($room['nama_ruangan']) ?>">
      </div>
      <div class="room-info">
        <h1><?= htmlspecialchars($room['nama_ruangan']) ?></h1>
        <p><?= htmlspecialchars($room['deskripsi']) ?></p>
        <p class="capacity">Kapasitas : <?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</p>
      </div>
    </div>

    <div class="badge-wrapper">
        <div class="puas-badge">
            <?= htmlspecialchars($badgeText) ?>
        </div>
    </div>

    <div class="booking-card">
      <h3><?= $isEdit ? 'Ubah jadwal peminjaman' : 'Pilih tanggal dan jam peminjaman' ?></h3>

      <?php if ($success): ?>
        <div class="flash-message flash-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($err): ?>
        <div class="flash-message flash-warning"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>
      
      <!-- Informasi jadwal terpakai hari ini -->
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

      <form action="<?= $isEdit ? '?route=Booking/editStep2' : '?route=Booking/step2' ?>" method="POST">
        <?php if ($isEdit): ?>
          <input type="hidden" name="booking_id" value="<?= htmlspecialchars($payload['booking_id']) ?>">
        <?php endif; ?>
        <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>"> <!-- ruangan tidak diubah -->

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

            <div class="time-wrapper">
                <div class="form-group time-box">
                    <label>Jam mulai</label>
                    <input
                        type="time"
                        name="jam_mulai"
                        class="input-line"
                        required
                        min="<?= htmlspecialchars($minTime) ?>"
                        max="<?= htmlspecialchars($maxTime) ?>"
                        value="<?= htmlspecialchars($payload['jam_mulai'] ?? '') ?>">
                </div>
                
                <span class="sampai-text">Sampai</span>

                <div class="form-group time-box">
                    <label>Jam selesai</label>
                    <input
                        type="time"
                        name="jam_selesai"
                        class="input-line"
                        required
                        min="<?= htmlspecialchars($minTime) ?>"
                        max="<?= htmlspecialchars($maxTime) ?>"
                        value="<?= htmlspecialchars($payload['jam_selesai'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="btn-action-row">
            <a href="?route=<?= $isEdit ? 'User/riwayat' : 'User/ruangan' ?>" class="btn btn-back">Kembali</a>
            <button type="submit" class="btn btn-next"><?= $isEdit ? 'Lanjut Ubah' : 'Lanjut' ?></button>
        </div>
      </form>
    </div>
  </main>

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

<!-- MODAL WARNING (untuk validasi tanggal/jam) -->
<div id="warningModal" class="modal-warning">
    <div class="modal-card">
        <div class="modal-icon">!</div>
        <div class="modal-title">Perhatian</div>
        <p class="modal-text" id="warningText">Pesan peringatan.</p>
        <div class="modal-actions">
            <button class="btn-modal-primary" type="button" onclick="closeWarning()">OK</button>
        </div>
    </div>
</div>

<!-- JAVASCRIPT LOGOUT + BLOK WEEKEND & JAM -->
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

    // Modal Warning
    const warningModal = document.getElementById('warningModal');
    const warningText = document.getElementById('warningText');
    function showWarning(msg) {
        warningText.textContent = msg;
        warningModal.classList.add('active');
    }
    function closeWarning() {
        warningModal.classList.remove('active');
    }
    warningModal.addEventListener('click', (e) => {
        if (e.target === warningModal) closeWarning();
    });

    // Blokir weekend & tanggal lampau di browser (frontend guard)
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    const minDateStr = '<?= htmlspecialchars($todayMin) ?>';
    const weekendMsg = 'Peminjaman tidak diperbolehkan pada hari Sabtu atau Minggu.';
    const pastMsg = 'Tanggal peminjaman tidak boleh sebelum hari ini.';

    function isWeekend(dateStr) {
        const d = new Date(dateStr + 'T00:00:00');
        const day = d.getDay(); // 0 = Minggu, 6 = Sabtu
        return day === 0 || day === 6;
    }

    function isPast(dateStr) {
        return dateStr < minDateStr;
    }

    tanggalInput?.addEventListener('change', () => {
        const val = tanggalInput.value;
        if (!val) return;
        if (isPast(val)) {
            tanggalInput.value = '';
            showWarning(pastMsg);
            return;
        }
        if (isWeekend(val)) {
            tanggalInput.value = '';
            showWarning(weekendMsg);
            return;
        }
    });

    // Guard jam: hanya 09:00 - 15:00, jam selesai > jam mulai, durasi <= 3 jam
    const jamMulaiInput = document.querySelector('input[name="jam_mulai"]');
    const jamSelesaiInput = document.querySelector('input[name="jam_selesai"]');
    const minTime = '<?= htmlspecialchars($minTime) ?>';
    const maxTime = '<?= htmlspecialchars($maxTime) ?>';
    const timeMsg = 'Peminjaman hanya boleh antara 09:00 - 15:00.';
    const orderMsg = 'Jam selesai harus setelah jam mulai.';
    const durationMsg = 'Durasi peminjaman maksimal 3 jam.';

    function toMinutes(hhmm) {
        const [h, m] = hhmm.split(':').map(Number);
        return h * 60 + m;
    }

    function validateTime() {
        const jm = jamMulaiInput.value;
        const js = jamSelesaiInput.value;
        if (!jm || !js) return;

        if (jm < minTime || js > maxTime) {
            jamMulaiInput.value = '';
            jamSelesaiInput.value = '';
            showWarning(timeMsg);
            return;
        }
        if (js <= jm) {
            jamSelesaiInput.value = '';
            showWarning(orderMsg);
            return;
        }
        const diff = toMinutes(js) - toMinutes(jm);
        if (diff > 180) { // lebih dari 3 jam
            jamSelesaiInput.value = '';
            showWarning(durationMsg);
            return;
        }
    }

    jamMulaiInput?.addEventListener('change', validateTime);
    jamSelesaiInput?.addEventListener('change', validateTime);
</script>

</body>
</html>
