<?php
// Session error handler
$err = Session::get('flash_error');
Session::set('flash_error', null);

$isEdit = !empty($payload['booking_id'] ?? null);

//payload data dari database, total peminjam ketika create booking 
$defaultJumlah = $booking['jumlah_peminjam'] ?? (1 + max(1, count($initialMembers)));

// Batas kapasitas ruangan dari database
$kapasitasMax = (int)($room['kapasitas_max'] ?? 0);
$kapasitasMin = (int)($room['kapasitas_min'] ?? 0);

// Maksimal anggota = kapasitasMax - 1 (karena 1 untuk penanggung jawab). 
$maxAnggota = $kapasitasMax > 0 ? max(0, $kapasitasMax - 1) : PHP_INT_MAX;

// Bangun URL gambar ruangan
$imgPath = !empty($room['gambar_ruangan']) ? $room['gambar_ruangan'] : 'public/assets/image/contohruangan.png';
$imgUrl  = preg_match('#^https?://#i', $imgPath) ? $imgPath : app_config()['base_url'].'/'.ltrim($imgPath, '/');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?= $isEdit ? 'Ubah Data Peminjaman' : 'Lengkapi Data Peminjaman' ?>
    - <?= htmlspecialchars($room['nama_ruangan']) ?>
  </title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking2.css?v=1.8">
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

  <main>

    <div class="room-header">
      <div class="room-image-container">
        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Ruangan" class="room-image" style="object-fit:cover;">
      </div>

      <div class="room-details">
        <h2><?= htmlspecialchars($room['nama_ruangan']) ?></h2>
        <p><?= htmlspecialchars($room['deskripsi'] ?? 'Tidak ada deskripsi ruangan.') ?></p>

        <p class="capacity">
          <strong>
          Kapasitas: <?= htmlspecialchars($room['kapasitas_min']) ?>
          - 
          <?= htmlspecialchars($room['kapasitas_max']) ?> 
          orang
          </strong>
        </p>

        <h3>Waktu Peminjaman:</h3>
        <p>Tanggal: <strong><?= htmlspecialchars($payload['tanggal']) ? date('d M Y', strtotime($payload['tanggal'])) : '-' ?></strong></p>
        <p>Jam: <strong><?= htmlspecialchars($payload['jam_mulai']) ?> </strong> - <strong> <?= htmlspecialchars($payload['jam_selesai']) ?> </strong></p>
        
        <p style="margin-top:8px;font-weight:600;">
          Maks anggota: <?= $kapasitasMax > 0 ? $maxAnggota : 'tidak dibatasi' ?> (1 slot untuk penanggung jawab).
          <?= $kapasitasMin > 0 ? ' Minimal total peminjam: ' . $kapasitasMin . ' orang.' : '' ?>
        </p>
      </div>
    </div>

    <div class="card">
      <h1><?= $isEdit ? 'Ubah data peminjaman' : 'Lengkapi data peminjaman' ?></h1>

      <!-- flash error -->
      <?php if ($err): ?>
        <div class="alert-error"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

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
          <label>NIM/NIP penanggung jawab</label>
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
              <div style="display:flex; align-items:center; justify-content:space-between;">
                <label>NIM Anggota <?= $idx ?></label>
                <button type="button" class="remove-btn" style="background:#ff5c5c;color:#fff;border:none;border-radius:6px;padding:4px 10px;cursor:pointer;">
                  Hapus
                </button>
              </div>
              <input class="input-line anggota-input" type="text" name="nim_anggota[]" value="<?= htmlspecialchars($val) ?>" required>
            </div>
          <?php 
          $idx++; 
          endforeach; 
          ?>
        </div>

        <button type="button" class="add-btn" id="addAnggota">+ Tambah Anggota</button>

        <div class="actions">
          <a href="?route=<?= $isEdit ? ('Booking/editForm/' . urlencode($payload['booking_id'])) : ('Booking/step1/' . urlencode($payload['room_id'])) ?>" class="btn-back">
            Kembali
          </a>
          <button type="submit" class="btn-save">
            <?= $isEdit ? 'Simpan Perubahan' : 'Simpan' ?>
          </button>
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
            <a href="?route=User/riwayat&refresh=<?= time() ?>" class="btn-modal btn-modal-white">Lihat riwayat peminjaman</a>
        </div>
    </div>
  </div>

  <!-- MODAL LOGOUT -->
  <div id="logoutModal" class="modal-overlay">
    <div class="modal-content">
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

<!-- MODAL WARNING (kapasitas & validasi lainnya) -->
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

  <script>
  /* =========================================================
   * INITIAL SETUP (AMBIL ELEMENT & STATE AWAL)
   * ========================================================= */
  const anggotaList     = document.getElementById('anggotaList');
  const addBtn          = document.getElementById('addAnggota');
  const jumlahHidden    = document.getElementById('jumlahPeminjam');
  const jumlahDisplay   = document.querySelector('input[name="jumlah_peminjam_display"]');
  const bookingForm     = document.getElementById('bookingForm');

  // Jumlah anggota saat ini (diambil dari PHP render awal)
  let anggotaCount = <?= $idx - 1 ?>;

  /* =========================================================
   * KONFIGURASI KAPASITAS (DARI BACKEND (database))
   * ========================================================= */
  const kapasitasMax = <?= $kapasitasMax ?>;
  const kapasitasMin = <?= $kapasitasMin ?>;
  const maxAnggota   = <?= $maxAnggota === PHP_INT_MAX ? 'Infinity' : $maxAnggota ?>;

  const kapasitasMaxMsg = kapasitasMax > 0
    ? `Jumlah anggota tidak boleh melebihi ${maxAnggota} (kapasitas total ${kapasitasMax} orang).`
    : 'Jumlah anggota tidak dibatasi.';

  const kapasitasMinMsg = kapasitasMin > 0
    ? `Jumlah peminjam harus minimal ${kapasitasMin} orang.`
    : '';

  /* =========================================================
   * MODAL WARNING (UNTUK VALIDASI FRONTEND)
   * ========================================================= */
  const warningModal = document.getElementById('warningModal');
  const warningText  = document.getElementById('warningText');

  function showWarning(message) {
    warningText.textContent = message;
    warningModal.classList.add('active');
  }

  function closeWarning() {
    warningModal.classList.remove('active');
  }

  // Tutup modal jika klik area luar
  warningModal.addEventListener('click', (e) => {
    if (e.target === warningModal) closeWarning();
  });

  /* =========================================================
   * TAMBAH FIELD ANGGOTA (DYNAMIC FORM)
   * ========================================================= */
  function addAnggotaField(value = '') {
    // Validasi batas maksimum anggota
    if (anggotaCount >= maxAnggota && maxAnggota !== Infinity) {
      showWarning(kapasitasMaxMsg);
      return;
    }

    anggotaCount++;

    const wrapper = document.createElement('div');
    wrapper.className = 'form-group anggota-item';

    wrapper.innerHTML = `
      <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
        <label style="flex:1;">NIM Anggota ${anggotaCount}</label>
        <button type="button" class="remove-btn"
          style="background:#ff5c5c;color:#fff;border:none;border-radius:6px;padding:4px 10px;cursor:pointer;">
          Hapus
        </button>
      </div>
      <input class="input-line anggota-input" type="text" name="nim_anggota[]" value="${value}" required>
    `;

    anggotaList.appendChild(wrapper);
  }

  /* =========================================================
   * REINDEX LABEL SETELAH HAPUS
   * ========================================================= */
  function reindexAnggota() {
    const items = document.querySelectorAll('.anggota-item');

    // Update jumlah anggota berdasarkan DOM terbaru
    anggotaCount = items.length;

    // Reset numbering label
    items.forEach((item, index) => {
      const label = item.querySelector('label');
      label.textContent = `NIM Anggota ${index + 1}`;
    });
  }

  /* =========================================================
   * EVENT: TAMBAH & HAPUS ANGGOTA
   * ========================================================= */

  // Tambah anggota
  addBtn.addEventListener('click', () => addAnggotaField(''));

  // Hapus anggota (pakai event delegation karena elemen dinamis)
  anggotaList.addEventListener('click', (e) => {
    if (!e.target.classList.contains('remove-btn')) return;

    // Minimal harus ada 1 anggota
    if (anggotaCount <= 1) {
      showWarning("Minimal harus ada 1 anggota.");
      return;
    }

    const item = e.target.closest('.anggota-item');
    item.remove();

    reindexAnggota();
  });

  /* =========================================================
   * SUBMIT FORM (AJAX + VALIDASI)
   * ========================================================= */
  bookingForm.addEventListener('submit', function(event) {
    event.preventDefault(); // Hindari reload

    const anggotaInputs = Array.from(document.querySelectorAll('.anggota-input'));

    // Validasi: tidak boleh kosong
    const anyEmpty = anggotaInputs.some(inp => inp.value.trim() === '');
    if (anyEmpty) {
      showWarning('Isi semua NIM anggota, tidak boleh ada yang kosong.');
      return;
    }

    // Validasi: tidak boleh duplikat
    const nimValues  = anggotaInputs.map(i => i.value.trim());
    const uniqueNims = new Set(nimValues);

    if (uniqueNims.size !== nimValues.length) {
      showWarning("Tidak boleh ada NIM anggota yang sama.");
      return;
    }

    // Hitung total peminjam (1 PJ + anggota)
    const filledMembers = nimValues.filter(v => v !== '');
    const total = 1 + filledMembers.length;

    // Validasi kapasitas
    if (kapasitasMax > 0 && total > kapasitasMax) {
      showWarning(kapasitasMaxMsg);
      return;
    }

    if (kapasitasMin > 0 && total < kapasitasMin) {
      showWarning(kapasitasMinMsg);
      return;
    }

    // Sinkronisasi ke input hidden (source of truth backend)
    jumlahHidden.value  = total;
    jumlahDisplay.value = total;

    const formData  = new FormData(bookingForm);
    const submitBtn = bookingForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerText;

    // UX: loading state
    submitBtn.innerText = 'Menyimpan...';
    submitBtn.disabled  = true;

    fetch(bookingForm.action, {
      method: 'POST',
      body: formData
    })
    .then(async res => {
      const text = await res.text();
      try {
        return JSON.parse(text);
      } catch (e) {
        console.error("Response bukan JSON:", text);
        throw new Error("Invalid JSON response");
      }
    })
    .then(data => {
      if (data.success === true) {
        openModal();
        return;
      }

      showWarning(data.message || "Terjadi kesalahan saat menyimpan booking.");
    })
    .catch(err => {
      console.error("Fetch Error:", err);
      showWarning("Terjadi kesalahan sistem. Silakan coba lagi.");
    })
    .finally(() => {
      submitBtn.innerText = originalText;
      submitBtn.disabled  = false;
    });
  });

  /* =========================================================
   * MODAL SUCCESS
   * ========================================================= */
  const successModal = document.getElementById('successModal');

  function openModal() {
    successModal.classList.add('active');
  }

  function closeModal() {
    successModal.classList.remove('active');
  }

  successModal.addEventListener('click', (e) => {
    if (e.target === successModal) closeModal();
  });

  /* =========================================================
   * MODAL LOGOUT
   * ========================================================= */
  const logoutModal = document.getElementById('logoutModal');

  function showLogoutModal() {
    logoutModal.classList.add('active');
  }

  function closeLogoutModal() {
    logoutModal.classList.remove('active');
  }

  logoutModal.addEventListener('click', (e) => {
    if (e.target === logoutModal) closeLogoutModal();
  });
  </script>
</body>
</html>
