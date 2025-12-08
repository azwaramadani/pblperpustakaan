<?php
$err     = Session::get('flash_error');
Session::set('flash_error', null);

// Data dummy member jika kosong
$initialMembers = [''];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lengkapi Data Peminjaman - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking2.css">
</head>
<body>

  <!-- Navbar -->
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
    <!-- Header Ruangan & Gambar -->
    <div class="room-header">
      <div class="room-image-container">
        <!-- Gambar Ruangan -->
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="Ruangan" class="room-image">
      </div>
      <div class="room-details">
        <h2><?= htmlspecialchars($room['nama_ruangan']) ?></h2>
        <p><?= htmlspecialchars($room['deskripsi'] ?? 'Ruangan Study.') ?></p>
        <p class="capacity">Kapasitas: <?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</p>
      </div>
    </div>

    <!-- Form Card -->
    <div class="card">
      <h1>Lengkapi Data Peminjaman</h1>

      <?php if ($err): ?>
        <div class="alert-error"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <form action="?route=Booking/store" method="POST" id="bookingForm">
        <!-- Hidden Inputs dari step1 -->
        <input type="hidden" name="room_id" value="<?= htmlspecialchars($payload['room_id']) ?>">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($payload['tanggal']) ?>">
        <input type="hidden" name="jam_mulai" value="<?= htmlspecialchars($payload['jam_mulai']) ?>">
        <input type="hidden" name="jam_selesai" value="<?= htmlspecialchars($payload['jam_selesai']) ?>">
        <input type="hidden" name="jumlah_peminjam" id="jumlahPeminjam" value="">

        <div class="form-group">
          <label>Nama penanggung jawab</label>
          <input class="input-line" type="text" name="nama_penanggung_jawab" value="<?= htmlspecialchars($user['nama']) ?>" readonly>
        </div>

        <div class="form-group">
          <label>NIM penanggung jawab</label>
          <input class="input-line" type="text" name="nimnip_penanggung_jawab" value="<?= htmlspecialchars($user['nim_nip']) ?>" readonly>
        </div>

        <div class="form-group">
          <label>Email penanggung jawab</label>
          <input class="input-line" type="email" name="email_penanggung_jawab" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label>Jumlah Peminjam</label>
          <input class="input-line" type="number" name="jumlah_peminjam" min="2" value="2" required>
        </div>

        <div class="anggota-wrap" id="anggotaList">

        <!-- List Anggota -->
        <div id="anggotaList">
          <?php $idx = 1; foreach ($initialMembers as $val): ?>
            <div class="form-group anggota-item">
              <label>NIM Anggota <?= $idx ?></label>
              <input class="input-line anggota-input" type="text" name="nim_anggota[]" value="<?= htmlspecialchars($val) ?>" <?= $idx === 1 ? 'required' : '' ?>>
            </div>
          <?php $idx++; endforeach; ?>
        </div>

        <!-- Tombol Tambah Anggota -->
        <button type="button" class="add-btn" id="addAnggota">+ Tambah Anggota</button>

        <!-- Tombol Aksi -->
        <div class="actions">
          <a href="?route=Booking/step1/<?= urlencode($payload['room_id']) ?>" class="btn-back">Kembali</a>
          <button type="submit" class="btn-save">Simpan</button>
        </div>
      </form>
    </div>
  </main>

  <!-- Footer -->
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
  <script>
    // JS Logic Original Anda + Update Tampilan
    const anggotaList = document.getElementById('anggotaList');
    const addBtn = document.getElementById('addAnggota');
    const jumlahInput = document.getElementById('jumlahPeminjam');
    let anggotaCount = <?= $idx - 1 ?>; // Ambil jumlah awal dari PHP

    function addAnggotaField(value = '') {
      anggotaCount += 1;
      const div = document.createElement('div');
      div.className = 'form-group anggota-item';
      div.innerHTML = `
        <label>NIM Anggota ${anggotaCount}</label>
        <input class="input-line anggota-input" type="text" name="nim_anggota[]" value="${value}">
      `;
      anggotaList.appendChild(div);
    }

    addBtn.addEventListener('click', () => addAnggotaField(''));

    // Hitung ulang jumlah peminjam saat submit
    document.getElementById('bookingForm').addEventListener('submit', () => {
      const filledMembers = Array.from(document.querySelectorAll('.anggota-input'))
        .map(i => i.value.trim())
        .filter(v => v !== '');
      jumlahInput.value = 1 + filledMembers.length; // 1 Penanggung jawab + anggota
    });
  </script>
</body>
</html>