<?php
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
$img       = $room['gambar_ruangan'] ?? '';
$imgUrl    = $img ? (preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/')) : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Ruangan - Rudy</title>
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
          <h2 style="margin:0;">Edit Ruangan</h2>
          <p class="subtitle">Ubah data ruangan yang dipilih.</p>
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
        <h3 style="margin:0;">Form Edit</h3>
        <a class="btn-add" href="?route=Admin/dataRuangan">Kembali</a>
      </div>

      <form method="POST" action="?route=Admin/updateRuangan" enctype="multipart/form-data" style="display:grid; gap:12px; max-width:720px;">
        <input type="hidden" name="room_id" value="<?= (int)$room['room_id'] ?>">

        <label>Nama Ruangan</label>
        <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($room['nama_ruangan'] ?? '') ?>" required>

        <label>Kapasitas Minimum</label>
        <input type="number" name="kapasitas_min" min="1" value="<?= (int)($room['kapasitas_min'] ?? 0) ?>" required>

        <label>Kapasitas Maksimum</label>
        <input type="number" name="kapasitas_max" min="1" value="<?= (int)($room['kapasitas_max'] ?? 0) ?>" required>

        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="4" placeholder="Tulis deskripsi ruangan..."><?= htmlspecialchars($room['deskripsi'] ?? '') ?></textarea>

        <label>Status</label>
        <select name="status" required>
          <option value="Tersedia" <?= ((strtolower($room['status'] ?? '') === 'tersedia') ? 'selected' : '') ?>>Tersedia</option>
          <option value="Tidak Tersedia" <?= ((strtolower($room['status'] ?? '') === 'tidak tersedia') ? 'selected' : '') ?>>Tidak Tersedia</option>
        </select>

        <label>Gambar saat ini</label>
        <?php if ($imgUrl): ?>
          <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="Gambar ruangan" style="width:240px; border-radius:12px;">
        <?php else: ?>
          <p style="margin:0;">Belum ada gambar.</p>
        <?php endif; ?>

        <label>Ganti gambar (opsional)</label>
        <input type="file" name="gambar_ruangan" accept="image/*">
        <input type="text" name="gambar_ruangan_manual" value="<?= htmlspecialchars($room['gambar_ruangan'] ?? '') ?>" placeholder="Atau isi URL/path gambar jika sudah ada">

        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:8px;">
          <button type="submit" class="btn-pill btn-save">Simpan Perubahan</button>
          <a class="btn-pill btn-cancel" href="?route=Admin/dataRuangan">Batal</a>
        </div>
      </form>
    </main>
  </div>
</div>
</body>
</html>
