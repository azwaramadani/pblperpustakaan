<?php
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Ruangan - Rudy</title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css?v=1.5">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="admin-body">
<div class="admin-layout">
  <aside class="sidebar">
    <div class="brand">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Rudy">
    </div>
    
    <nav class="sidebar-nav">
      <a href="?route=Admin/dashboard">
        <i class="fa-solid fa-chart-line"></i> Dashboard
      </a>
      <a href="?route=Admin/dataPeminjaman">
        <i class="fa-solid fa-calendar-check"></i> Data Peminjaman
      </a>
      <!-- CLASS ACTIVE DISINI -->
      <a href="?route=Admin/dataRuangan" class="active">
        <i class="fa-solid fa-door-open"></i> Data Ruangan
      </a>
      <a href="?route=Admin/dataFromAdminCreateBooking">
        <i class="fa-solid fa-user-tag"></i> Data Pinjam Admin
      </a>
      <a href="?route=Admin/dataAkun">
        <i class="fa-solid fa-users"></i> Data Akun
      </a>
      <a href="?route=Auth/logout" style="color: var(--danger) !important;">
        <i class="fa-solid fa-right-from-bracket" style="color: var(--danger) !important;"></i> Keluar
      </a>
    </nav>

    <!-- PROFIL DI SIDEBAR (Footer) -->
    <div class="sidebar-footer">
      <img src="public/assets/image/userlogo.png" class="avatar-img" alt="Admin">
      <div class="user-info">
        <span class="name">adminrudy1</span>
        <span style="font-size:11px; color:#6b7280;">Administrator</span>
      </div>
    </div>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Tambah Ruangan</h2>
          <p class="margin:4px 0 0;">Masukkan data ruangan baru.</p>
        </div>
      </div>
    </header>

    <main class="content">
      <?php if (!empty($success)): ?>
        <div class="flash success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="flash error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="section-head" style="align-items:center; justify-content:space-between;">
        <h3 style="margin:0;">Form Tambah</h3>
        <a class="btn-add"href="?route=Admin/dataRuangan">Kembali</a>
      </div>

      <form method="POST" action="?route=Admin/storeRuangan" enctype="multipart/form-data" style="display:grid; gap:12px; max-width:720px;">
        <label>Nama Ruangan</label>
        <input type="text" name="nama_ruangan" required>

        <label>Kapasitas Minimum</label>
        <input type="number" name="kapasitas_min" min="1" required>

        <label>Kapasitas Maksimum</label>
        <input type="number" name="kapasitas_max" min="1" required>

        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="4" placeholder="Tulis deskripsi ruangan..."></textarea>

        <label>Status</label>
        <select name="status" required>
          <option value="Tersedia">Tersedia</option>
          <option value="Tidak Tersedia">Tidak Tersedia</option>
        </select>

        <label>Gambar (opsional)</label>
        <input type="file" name="gambar_ruangan" accept="image/*">
        <input type="text" name="gambar_ruangan_manual" placeholder="Atau isi URL/path gambar jika sudah ada">

        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:8px;">
          <button type="submit" class="btn-pill btn-save">Simpan</button>
          <a class="btn-pill btn-cancel" href="?route=Admin/dataRuangan">Batal</a>
        </div>
      </form>
    </main>
  </div>
</div>
</body>
</html>
