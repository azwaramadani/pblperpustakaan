<?php
$badgeText   = $puasPercent > 0 ? $puasPercent . '% Orang Puas' : 'Belum ada feedback';
$err  = Session::get('flash_error');
Session::set('flash_error', null);

// Helper function untuk base_url jika belum didefinisikan (untuk preview)
if (!function_exists('app_config')) {
    function app_config() { return ['base_url' => '']; }
}
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

    <!-- persentase puas -->
    <div class="badge-wrapper">
        <div class="puas-badge">
            <?= htmlspecialchars($badgeText) ?>
        </div>
    </div>

    <!-- 3. Form Card (White Box) -->
    <div class="booking-card">
      <h3>Pilih tanggal dan jam peminjaman</h3>

      <?php if ($err): ?>
        <div class="alert-error"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <form action="?route=Booking/adminStep2" method="POST">
        <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
        
        <div class="form-grid">
            <!-- Tanggal -->
            <div class="form-group">
                <label>Pilih tanggal</label>
                <input type="date" name="tanggal" class="input-line" required>
            </div>

            <!-- Jam Mulai & Selesai -->
            <div class="time-wrapper">
                <div class="form-group time-box">
                    <label>Pilih jam</label>
                    <input type="time" name="jam_mulai" class="input-line" required>
                </div>
                
                <span class="sampai-text">Sampai</span>

                <div class="form-group time-box">
                    <label>Pilih jam</label>
                    <input type="time" name="jam_selesai" class="input-line" required>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="btn-action-row">
            <a href="?route=admin/dataruangan" class="btn btn-back">Kembali</a>
            <button type="?route=Booking/adminStep2" class="btn btn-next">Lanjut</button>
        </div>
      </form>
    </div>
  </main>

</body>
</html>