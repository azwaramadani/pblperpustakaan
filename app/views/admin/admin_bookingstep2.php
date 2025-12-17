<?php
$adminName   = $admin['username'] ?? ($admin['nama'] ?? 'Admin');

$err     = Session::get('flash_error');
Session::set('flash_error', null);

$initialMembers = [''];

// Batas kapasitas ruangan dari backend
$kapasitasMax = (int)($room['kapasitas_max'] ?? 0);
$kapasitasMin = (int)($room['kapasitas_min'] ?? 0);
// Maksimal anggota = kapasitasMax - 1 (karena 1 slot untuk penanggung jawab). Jika 0/negatif, anggap tak terbatas.
$maxAnggota = $kapasitasMax > 0 ? max(0, $kapasitasMax - 1) : PHP_INT_MAX;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lengkapi Data Peminjaman - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking2.css">
  <style>
    /* Modal warning khusus kapasitas/validasi */
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

  <header class="navbar">
    <div class="logo">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40">
    </div>

    <div class="profile-dropdown">
      <div class="profile-trigger">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
        <div class="user-name">
          <a href="?route=Admin/dataRuangan" style="text-decoration: none; color: black;"><p><?= htmlspecialchars($adminName) ?></p></a>
        </div>
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
        <p style="margin-top:8px;font-weight:600;">
          Maks anggota: <?= $kapasitasMax > 0 ? $maxAnggota : 'tidak dibatasi' ?> (1 slot untuk penanggung jawab).
          <?= $kapasitasMin > 0 ? ' Minimal total peminjam: ' . $kapasitasMin . ' orang.' : '' ?>
        </p>
      </div>
    </div>

    <?php if ($err): ?>
      <div style="color:red;margin-top:16px;"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <div class="card">
      <h1>Lengkapi Data Peminjaman</h1>

      <?php if ($err): ?>
        <div class="alert error"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <form action="?route=Booking/adminStore" method="POST" id="bookingForm">
        <!-- Hidden bawaan step1 -->
        <input type="hidden" name="room_id" value="<?= htmlspecialchars($payload['room_id']) ?>">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($payload['tanggal']) ?>">
        <input type="hidden" name="jam_mulai" value="<?= htmlspecialchars($payload['jam_mulai']) ?>">
        <input type="hidden" name="jam_selesai" value="<?= htmlspecialchars($payload['jam_selesai']) ?>">
        <!-- Hidden info jumlah (backend tetap hitung ulang) -->
        <input type="hidden" name="jumlah_peminjam" id="jumlahPeminjam" value="">

        <div class="form-group">
          <label>Nama penanggung jawab</label>
          <input class="input-line" type="text" name="nama_penanggung_jawab" value="" required>
        </div>

        <div class="form-group">
          <label>NIM/NIP penanggung jawab</label>
          <input class="input-line" type="text" name="nimnip_penanggung_jawab" value="" required>
        </div>

        <div class="form-group">
          <label>Email penanggung jawab</label>
          <input class="input-line" type="email" name="email_penanggung_jawab" value="" required>
        </div>

        <div class="form-group">
          <label>Jumlah Peminjam</label>
          <input class="input-line" type="number" name="jumlah_peminjam_display" min="2" value="2" required>
        </div>

        <div class="anggota-wrap" id="anggotaList">
          <?php $idx = 1; foreach ($initialMembers as $val): ?>
            <div class="form-group anggota-item">
              <label>NIM Anggota <?= $idx ?></label>
              <input class="input-line anggota-input" type="text" name="nim_anggota[]" value="<?= htmlspecialchars($val) ?>" required>
            </div>
          <?php $idx++; endforeach; ?>
        </div>

        <button type="button" class="add-btn" id="addAnggota">+ Tambah Anggota</button>

        <div class="actions">
          <a href="?route=Booking/adminStep1/<?= urlencode($payload['room_id']) ?>" class="btn btn-back">Kembali</a>
          <button type="submit" class="btn btn-save">Simpan</button>
        </div>
      </form>
    </div>
  </main>

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

    // JS untuk menambah field NIM anggota baru
    const anggotaList = document.getElementById('anggotaList');
    const addBtn = document.getElementById('addAnggota');
    const jumlahInput = document.getElementById('jumlahPeminjam');
    const jumlahDisplay = document.querySelector('input[name="jumlah_peminjam_display"]');
    let anggotaCount = anggotaList.querySelectorAll('.anggota-item').length;

    // batas kapasitas
    const kapasitasMax = <?= $kapasitasMax ?>;
    const kapasitasMin = <?= $kapasitasMin ?>;
    const maxAnggota = <?= $maxAnggota === PHP_INT_MAX ? 'Infinity' : $maxAnggota ?>;
    const kapasitasMaxMsg = kapasitasMax > 0
      ? `Jumlah anggota tidak boleh melebihi ${maxAnggota} (kapasitas total ${kapasitasMax} orang).`
      : 'Jumlah anggota tidak dibatasi.';
    const kapasitasMinMsg = kapasitasMin > 0
      ? `Jumlah peminjam harus minimal ${kapasitasMin} orang.`
      : '';

    function addAnggotaField(value = '') {
      if (maxAnggota !== Infinity && anggotaCount >= maxAnggota) {
        showWarning(kapasitasMaxMsg);
        return;
      }
      anggotaCount += 1;
      const wrapper = document.createElement('div');
      wrapper.className = 'form-group anggota-item';

      const label = document.createElement('label');
      label.textContent = 'NIM Anggota ' + anggotaCount;

      const input = document.createElement('input');
      input.type = 'text';
      input.name = 'nim_anggota[]';
      input.value = value;
      input.className = 'input-line anggota-input';
      input.required = true; // wajib diisi untuk semua field

      wrapper.appendChild(label);
      wrapper.appendChild(input);
      anggotaList.appendChild(wrapper);
    }

    addBtn.addEventListener('click', () => addAnggotaField(''));

    // Saat submit, hitung ulang jumlah peminjam: 1 penanggung + anggota yang terisi, dan pastikan semua terisi
    document.getElementById('bookingForm').addEventListener('submit', (e) => {
      const anggotaInputs = Array.from(document.querySelectorAll('.anggota-input'));
      const anyEmpty = anggotaInputs.some(inp => inp.value.trim() === '');
      if (anyEmpty) {
        showWarning('Isi semua NIM anggota, tidak boleh ada yang kosong.');
        e.preventDefault();
        return;
      }

      const filledMembers = anggotaInputs.map(i => i.value.trim()).filter(v => v !== '');
      const total = 1 + filledMembers.length;

      // Validasi kapasitas sebelum submit
      if (kapasitasMax > 0 && total > kapasitasMax) {
        showWarning(kapasitasMaxMsg);
        e.preventDefault();
        return;
      }
      if (kapasitasMin > 0 && total < kapasitasMin) {
        showWarning(kapasitasMinMsg);
        e.preventDefault();
        return;
      }

      jumlahInput.value = total;
      jumlahDisplay.value = total;
    });
  </script>
</body>
</html>
