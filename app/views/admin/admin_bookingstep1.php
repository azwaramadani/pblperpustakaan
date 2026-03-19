<?php
$badgeText   = $puasPercent > 0 ? $puasPercent . '% Orang Puas' : 'Belum ada feedback';
$isEdit    = !empty($payload['booking_id'] ?? null);

// Helper function untuk base_url jika belum didefinisikan (untuk preview)
if (!function_exists('app_config')) {
    function app_config() { return ['base_url' => '']; }
}

// Batas minimal tanggal = hari ini (Asia/Jakarta)
$todayMin = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

// Bangun URL gambar ruangan
$imgPath = !empty($room['gambar_ruangan']) ? $room['gambar_ruangan'] : 'public/assets/image/contohruangan.png';
$imgUrl  = preg_match('#^https?://#i', $imgPath) ? $imgPath : app_config()['base_url'].'/'.ltrim($imgPath, '/');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pilih Tanggal & Jam - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking1.css">
</head>
<body>
<header class="navbar">
    <div class="logo">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40">
    </div>

    <div class="profile-dropdown">
      <div class="profile-trigger">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
        <div class="user-name">
          <a href="?route=Admin/dataRuangan" style="text-decoration: none; color: black;"><p><?= htmlspecialchars($admin['username']) ?></p></a>
        </div>
      </div>
    </div>
  </header>

  <main class="main-container">    
    <div class="room-header">
      <div class="room-img-container">
        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($room['nama_ruangan']) ?>">
      </div>
      <div class="room-info">
        <h1><?= htmlspecialchars($room['nama_ruangan']) ?></h1>
        <p><?= htmlspecialchars($room['deskripsi']) ?></p>
        <p class="capacity">Kapasitas : <?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</p>
      </div>
    </div>

    <!-- persentase puas -->
    <div class="badge-wrapper">
        <div class="puas-badge">
            <?= htmlspecialchars($badgeText) ?>
        </div>
    </div>

    <!-- 3. Form Card (White Box) -->
    <div class="booking-card">
      <h3>Pilih tanggal dan jam peminjaman</h3>

      <!-- Flash error dititipkan ke JS untuk ditampilkan sebagai modal -->
            <?php if (!empty($error = $flash['error'])): ?>
                <script>
                    window.__flashError = <?= json_encode(htmlspecialchars($error)) ?>;
                </script>
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

      <form action="<?= $isEdit ? '?route=Booking/adminEditStep2' : '?route=Booking/adminStep2' ?>" method="POST">
        <?php if ($isEdit): ?>
            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($payload['booking_id']) ?>">
        <?php endif; ?>  
          
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

            <!-- Jam Mulai & Selesai -->
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
                
                <span class="sampai-text">Sampai</span>

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

        <!-- Tombol Aksi -->
        <div class="btn-action-row">
            <a href="?route=<?= $isEdit ? 'Admin/dataFromAdminCreateBooking' : 'admin/dataRuangan' ?>" class="btn btn-back">Kembali</a>
            <button type="submit" class="btn btn-next">
              <?= $isEdit ? 'Lanjut ubah' : 'Lanjut' ?>
            </button>
        </div>
      </form>
    </div>
  </main>

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
</script>

</body>
</html>
