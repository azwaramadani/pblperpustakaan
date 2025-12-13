<?php
// Kita komentari dulu bagian session success agar tidak auto-trigger saat load
$err     = Session::get('flash_error');
// $success = Session::get('flash_success'); 
Session::set('flash_error', null);
// Session::set('flash_success', null); 

$isEdit = !empty($payload['booking_id'] ?? null);

if (!isset($initialMembers) || !is_array($initialMembers)) {
    $initialMembers = [''];
}

$defaultJumlah = $booking['jumlah_peminjam'] ?? (1 + max(1, count($initialMembers)));
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $isEdit ? 'Ubah Data Peminjaman' : 'Lengkapi Data Peminjaman' ?> - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking2.css?v=1.7">
</head>
<body>

  <!-- Navbar -->
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

  <main>
    <div class="room-header">
      <div class="room-image-container">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="Ruangan" class="room-image">
      </div>
      <div class="room-details">
        <h2><?= htmlspecialchars($room['nama_ruangan']) ?></h2>
        <p><?= htmlspecialchars($room['deskripsi'] ?? 'Ruangan Study.') ?></p>
        <p class="capacity">Kapasitas: <?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</p>
        <h3>Waktu Peminjaman:</h3>
        <p><strong><?= htmlspecialchars($payload['tanggal']) ?></strong> (<?= htmlspecialchars($payload['jam_mulai']) ?> - <?= htmlspecialchars($payload['jam_selesai']) ?>)</p>
      </div>
    </div>

    <div class="card">
      <h1><?= $isEdit ? 'Ubah Data Peminjaman' : 'Lengkapi Data Peminjaman' ?></h1>

      <?php if ($err): ?>
        <div class="alert-error"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <!-- Tambahkan ID pada Form -->
      <form action="<?= $isEdit ? '?route=Booking/update' : '?route=Booking/store' ?>" method="POST" id="bookingForm">
        <?php if ($isEdit): ?>
          <input type="hidden" name="booking_id" value="<?= htmlspecialchars($payload['booking_id']) ?>">
        <?php endif; ?>
        <input type="hidden" name="room_id" value="<?= htmlspecialchars($payload['room_id']) ?>"> 
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($payload['tanggal']) ?>">
        <input type="hidden" name="jam_mulai" value="<?= htmlspecialchars($payload['jam_mulai']) ?>">
        <input type="hidden" name="jam_selesai" value="<?= htmlspecialchars($payload['jam_selesai']) ?>">
        <input type="hidden" name="jumlah_peminjam" id="jumlahPeminjam" value="<?= htmlspecialchars($defaultJumlah) ?>">

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
          <label>Jumlah Peminjam (1 PJ + anggota)</label>
          <input class="input-line" type="number" name="jumlah_peminjam_display" min="2" value="<?= htmlspecialchars($defaultJumlah) ?>" required>
        </div>

        <div class="anggota-wrap" id="anggotaList">
          <?php $idx = 1; foreach ($initialMembers as $val): ?>
            <div class="form-group anggota-item">
              <label>NIM Anggota <?= $idx ?></label>
              <input class="input-line anggota-input" type="text" name="nim_anggota[]" value="<?= htmlspecialchars($val) ?>" <?= $idx === 1 ? 'required' : '' ?>>
            </div>
          <?php $idx++; endforeach; ?>
        </div>

        <button type="button" class="add-btn" id="addAnggota">+ Tambah Anggota</button>

        <div class="actions">
          <a href="?route=<?= $isEdit ? ('Booking/editForm/' . urlencode($payload['booking_id'])) : ('Booking/step1/' . urlencode($payload['room_id'])) ?>" class="btn-back">Kembali</a>
          <!-- Button Type Submit akan ditangkap JS -->
          <button type="submit" class="btn-save"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Perubahan' ?></button>
        </div>
      </form>
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

  <!-- MODAL SUCCESS -->
  <div id="successModal" class="modal-overlay">
    <div class="modal-content">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <div class="success-icon-container">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <h2 class="modal-title">Booking berhasil disimpan</h2>
        <div class="modal-actions">
            <a href="?route=User/home" class="btn-modal btn-modal-yellow">Kembali ke beranda</a>
            <a href="?route=User/riwayat" class="btn-modal btn-modal-white">Lihat riwayat peminjaman</a>
        </div>
    </div>
  </div>

  <script>
    const anggotaList = document.getElementById('anggotaList');
    const addBtn = document.getElementById('addAnggota');
    const jumlahHidden = document.getElementById('jumlahPeminjam');
    const jumlahDisplay = document.querySelector('input[name="jumlah_peminjam_display"]');
    const bookingForm = document.getElementById('bookingForm'); // Ambil elemen form
    let anggotaCount = <?= $idx - 1 ?>;

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

    // --- LOGIKA UTAMA PERBAIKAN ---
    // Menggunakan Event Listener pada Form Submit
    bookingForm.addEventListener('submit', function(event) {
        // 1. Hitung jumlah anggota sebelum submit
        const filledMembers = Array.from(document.querySelectorAll('.anggota-input'))
            .map(i => i.value.trim())
            .filter(v => v !== '');
        const total = 1 + filledMembers.length;
        jumlahHidden.value  = total;
        jumlahDisplay.value = total;

        // 2. Cegah reload halaman (prevent submit asli PHP) agar modal muncul
        // Catatan: Jika nanti backend sudah siap redirect, hapus baris 'event.preventDefault()' ini
        // dan gunakan logika session PHP seperti kode sebelumnya.
        event.preventDefault(); 
        
        // 3. Tampilkan modal
        openModal();
    });

    // --- MODAL FUNCTIONS ---
    const successModal = document.getElementById('successModal');

    function openModal() {
        successModal.classList.add('active');
    }

    function closeModal() {
        successModal.classList.remove('active');
    }

    successModal.addEventListener('click', (e) => {
        if (e.target === successModal) {
            closeModal();
        }
    });
  </script>
</body>
</html>