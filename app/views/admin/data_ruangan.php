<?php
$rooms = $rooms ?? [];
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Ruangan - Rudy</title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css">
</head>

<body class="admin-body">
<div class="admin-layout">
  <aside class="sidebar">
    <div class="brand">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Rudy">
      <p>Panel Admin</p>
    </div>
    <nav class="sidebar-nav">
      <a href="?route=Admin/dashboard">Dashboard</a>
      <a href="?route=Admin/dataPeminjaman">Data Peminjaman</a>
      <a href="?route=Admin/dataRuangan" class="active">Data Ruangan</a>
      <a href="?route=Auth/logout">Keluar</a>
    </nav>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Ruangan</h2>
          <p style="margin:4px 0 0;">Daftar ruangan beserta statistik peminjaman & feedback</p>
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

      <button class="btn-add">Tambah Ruangan</button>

      <?php if (empty($rooms)): ?>
        <div class="flash error">Belum ada data ruangan.</div>
      <?php else: ?>
        <?php foreach ($rooms as $r): ?>
          <?php
            $img = $r['gambar_ruangan'] ?: 'public/assets/image/contohruangan.png';
            $imgUrl = preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/');
            $kapasitas = $r['kapasitas_min'].' - '.$r['kapasitas_max'].' orang';
            $totalBooking = (int)($r['total_booking'] ?? 0);
            $totalFeedback= (int)($r['total_feedback'] ?? 0);
            $puasPercent  = (int)($r['puas_percent'] ?? 0);
          ?>
          <div class="room-card">
            <div class="room-info">
              <h3><?= htmlspecialchars($r['nama_ruangan']) ?></h3>
              <p><strong>Deskripsi:</strong> <?= htmlspecialchars($r['deskripsi'] ?? 'Belum ada deskripsi.') ?></p>
              <p><strong>Kapasitas:</strong> <?= htmlspecialchars($kapasitas) ?></p>
              <p><strong>Jumlah peminjaman:</strong> <?= $totalBooking ?> kali</p>
              <p><strong>Tingkat kepuasan:</strong> <?= $puasPercent ?>%</p>
              <p><strong>Jumlah feedback:</strong> <?= $totalFeedback ?></p>
            </div>
            <div class="room-actions">
              <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="<?= htmlspecialchars($r['nama_ruangan']) ?>">
              <button class="btn-rect">Lihat Feedback</button>
              <button class="btn-rect">Ubah</button>
              <button class="btn-rect">Hapus</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </main>
  </div>
</div>
</body>
</html>