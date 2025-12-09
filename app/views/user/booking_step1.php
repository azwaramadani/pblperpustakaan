<?php
$badgeText = $puasPercent > 0 ? $puasPercent . '% Orang Puas' : 'Belum ada feedback';
$err       = Session::get('flash_error');
Session::set('flash_error', null);

$isEdit = !empty($payload['booking_id'] ?? null);

// Helper base_url untuk preview
if (!function_exists('app_config')) {
    function app_config() { return ['base_url' => '']; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Ubah Peminjaman' : 'Pilih Tanggal & Jam' ?> - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking1.css">
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

      <?php if ($err): ?>
        <div class="alert-error"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>
      
      <!-- Informasi jadwal terpakai hari ini -->
      <div class="schedule-box">
        <h4>Waktu yang sudah dipinjam.</h4>
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
                <input type="date" name="tanggal" class="input-line" required
                       value="<?= htmlspecialchars($payload['tanggal'] ?? '') ?>">
            </div>

            <div class="time-wrapper">
                <div class="form-group time-box">
                    <label>Jam mulai</label>
                    <input type="time" name="jam_mulai" class="input-line" required
                           value="<?= htmlspecialchars($payload['jam_mulai'] ?? '') ?>">
                </div>
                
                <span class="sampai-text">Sampai</span>

                <div class="form-group time-box">
                    <label>Jam selesai</label>
                    <input type="time" name="jam_selesai" class="input-line" required
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

  <footer>
      <div class="footer-content">
          <div class="footer-brand">
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Rudy" style="height:30px;">
            </div>
            <p>
                Rudi Ruangan Studi adalah platform peminjaman ruangan perpustakaan yang membantu mahasiswa dan staf mengatur penggunaan ruang belajar dengan mudah dan efisien.
            </p>
          </div>
          <div class="footer-links">
              <div class="link-group">
                  <h4>Navigasi</h4>
                  <ul>
                      <li><a href="#">Beranda</a></li>
                      <li><a href="#">Daftar Ruangan</a></li>
                      <li><a href="#">Panduan</a></li>
                      <li><a href="#">Masuk</a></li>
                  </ul>
              </div>
              <div class="link-group">
                  <h4>Bantuan</h4>
                  <ul>
                      <li><a href="#">FAQ</a></li>
                      <li><a href="#">Aturan ruangan</a></li>
                  </ul>
              </div>
              <div class="link-group">
                  <h4>Kontak</h4>
                  <ul>
                      <li><a href="#">PerpusPNJ@email.com</a></li>
                      <li><a href="#">0822123456780</a></li>
                      <li><a href="#">Kampus PNJ, Depok</a></li>
                  </ul>
              </div>
          </div>
      </div>
  </footer>
</body>
</html>
