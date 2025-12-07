<?php
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Ruangan - Rudy</title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css">
</head>

<body class="admin-body">
<div class="admin-layout">
  <aside class="sidebar">
    <div class="brand">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Rudy">
    </div>
    <nav class="sidebar-nav">
      <a href="?route=Admin/dashboard">Dashboard</a>
      <a href="?route=Admin/dataPeminjaman">Data Peminjaman</a>
      <a href="?route=Admin/dataRuangan" class="active">Data Ruangan</a>
      <a href="?route=Admin/dataAkun">Data Akun</a>
      <a href="?route=Auth/logout">Keluar</a>
    </nav>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Tambah Ruangan</h2>
          <p class="subtitle">Masukkan data ruangan baru.</p>
        </div>
      </div>
      <div class="profile-summary top">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="Admin" class="avatar">
        <div>
          <p style="margin:0;"><?= htmlspecialchars($adminName) ?></p>
          <span>ID: <?= htmlspecialchars($admin['admin_id'] ?? '-') ?></span>
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
        <a class="btn-add" href="?route=Admin/dataRuangan">Kembali</a>
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
