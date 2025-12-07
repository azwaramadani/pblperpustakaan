<?php
// Simulasi Data (jika tidak ada data dari controller)
// Anda bisa menghapus blok ini jika sudah terintegrasi dengan framework
$user = $data['user'] ?? [
  'nama'    => Session::get('nama') ?? 'Mahasiswa PNJ',
  'nim_nip' => Session::get('nim_nip') ?? '12345678',
  'no_hp'   => Session::get('no_hp') ?? '08123456789',
  'email'   => Session::get('email') ?? 'user@pnj.ac.id',
  'role'    => 'Mahasiswa'
];

// Pastikan data room ada isinya agar tidak error saat testing
$room = $data['room'] ?? [
    'nama_ruangan' => 'Lentera Edukasi',
    'deskripsi' => 'Ruangan khusus bimbingan dan konseling dengan suasana tenang dan privat. Cocok untuk sesi diskusi, pendampingan akademik, atau konsultasi pribadi.',
    'kapasitas_min' => 2,
    'kapasitas_max' => 4,
    'room_id' => 1
];

$puasPercent = $data['puas_percent'] ?? 90;
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
  
  <!-- Link ke File CSS Eksternal -->
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking1.css">
  
</head>
<body>

  <!-- NAVBAR -->
  <header class="navbar">
    <div class="nav-left">
      <!-- Pastikan path logo benar -->
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
        <p><?= htmlspecialchars($data['user']['role']) ?></p>
        <p><?= htmlspecialchars($user['nim_nip']) ?></p>
        <p><?= htmlspecialchars($user['no_hp']) ?></p>
        <p><?= htmlspecialchars($user['email']) ?></p>
        <a class="btn-logout" href="?route=Auth/logout">Keluar</a>
      </div>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="main-container">
    
    <!-- 1. Room Header (Image + Text) -->
    <div class="room-header">
      <div class="room-img-container">
        <!-- Placeholder image jika data gambar kosong -->
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="<?= htmlspecialchars($room['nama_ruangan']) ?>">
      </div>
      <div class="room-info">
        <h1><?= htmlspecialchars($room['nama_ruangan']) ?></h1>
        <p><?= htmlspecialchars($room['deskripsi']) ?></p>
        <p class="capacity">Kapasitas : <?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</p>
      </div>
    </div>

    <!-- 2. Badge (Centered Pill) -->
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

      <form action="?route=Booking/step2" method="POST">
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
            <a href="?route=User/ruangan" class="btn btn-back">Kembali</a>
            <button type="?route=user/booking_step2" class="btn btn-next">Lanjut</button>
        </div>
      </form>
    </div>

  </main>

  <!-- FOOTER (Hitam) -->
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