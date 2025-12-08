<?php
$err     = Session::get('flash_error');
Session::set('flash_error', null);

$initialMembers = [''];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lengkapi Data Peminjaman - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleriwayat.css">
</head>
<body>
  <header class="navbar">
    <div class="logo">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40">
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

  <main style="max-width:900px;margin:40px auto;">
    <div style="display:flex;gap:24px;align-items:center;">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="Ruangan" style="width:250px;border-radius:10px;">
      <div>
        <h2><?= htmlspecialchars($room['nama_ruangan']) ?></h2>
        <p><?= htmlspecialchars($room['deskripsi'] ?? 'Ruangan study') ?></p>
        <p>Kapasitas: <?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</p>
        <h3>Waktu Peminjaman:</h3>
        <p><strong><?= htmlspecialchars($payload['tanggal']) ?></strong> (<?= htmlspecialchars($payload['jam_mulai']) ?> - <?= htmlspecialchars($payload['jam_selesai']) ?>)</p>
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

      <form action="?route=Booking/store" method="POST" id="bookingForm">
        <!-- Hidden bawaan step1 -->
        <input type="hidden" name="room_id" value="<?= htmlspecialchars($payload['room_id']) ?>">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($payload['tanggal']) ?>">
        <input type="hidden" name="jam_mulai" value="<?= htmlspecialchars($payload['jam_mulai']) ?>">
        <input type="hidden" name="jam_selesai" value="<?= htmlspecialchars($payload['jam_selesai']) ?>">
        <!-- Hidden info jumlah (backend tetap hitung ulang) -->
        <input type="hidden" name="jumlah_peminjam" id="jumlahPeminjam" value="">

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
          <label>Jumlah Peminjam</label>
          <input class="input-line" type="number" name="jumlah_peminjam" min="2" value="2" required>
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
          <a href="?route=Booking/step1/<?= urlencode($payload['room_id']) ?>" class="btn btn-back">Kembali</a>
          <button type="submit" class="btn btn-save">Simpan</button>
        </div>
      </form>
    </div>
  </main>

<script>
    // JS untuk menambah field NIM anggota baru
    const anggotaList = document.getElementById('anggotaList');
    const addBtn = document.getElementById('addAnggota');
    const jumlahInput = document.getElementById('jumlahPeminjam');
    let anggotaCount = anggotaList.querySelectorAll('.anggota-item').length;

    function addAnggotaField(value = '') {
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
      input.required = false; // field tambahan opsional, minimal 1 sudah required

      wrapper.appendChild(label);
      wrapper.appendChild(input);
      anggotaList.appendChild(wrapper);
    }

    addBtn.addEventListener('click', () => addAnggotaField(''));

    // Saat submit, hitung ulang jumlah peminjam: 1 penanggung + anggota yang terisi
    document.getElementById('bookingForm').addEventListener('submit', () => {
      const filledMembers = Array.from(document.querySelectorAll('.anggota-input'))
        .map(i => i.value.trim())
        .filter(v => v !== '');
      jumlahInput.value = 1 + filledMembers.length;
    });
  </script>
</body>
</html>
