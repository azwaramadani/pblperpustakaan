<?php
$adminName   = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
$feedbacks   = $feedbacks ?? [];
$summary     = $feedbackSummary ?? ['total'=>0,'puas'=>0,'tidak_puas'=>0];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback Ruangan - Rudy</title>
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
          <h2 style="margin:0;">Feedback Ruangan</h2>
          <p class="subtitle">Semua feedback untuk <?= htmlspecialchars($room['nama_ruangan'] ?? '') ?></p>
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
      <div class="section-head" style="align-items:center; justify-content:space-between;">
        <div>
          <h3><?= htmlspecialchars($room['nama_ruangan'] ?? '-') ?></h3>
          <p class="subtitle"><?= htmlspecialchars($room['deskripsi'] ?? 'Belum ada deskripsi.') ?></p>
        </div>
        <a class="btn-add" href="?route=Admin/dataRuangan">Kembali ke Data Ruangan</a>
      </div>

      <div class="panel" style="margin-bottom:16px;">
        <div style="display:flex; gap:16px; flex-wrap:wrap;">
          <div>
            <p style="margin:0; font-size:12px; color:#777;">Kapasitas</p>
            <strong><?= htmlspecialchars(($room['kapasitas_min'] ?? 0).' - '.($room['kapasitas_max'] ?? 0).' orang') ?></strong>
          </div>
          <div>
            <p style="margin:0; font-size:12px; color:#777;">Total Booking</p>
            <strong><?= (int)($room['total_booking'] ?? 0) ?> kali</strong>
          </div>
          <div>
            <p style="margin:0; font-size:12px; color:#777;">Feedback Masuk</p>
            <strong><?= (int)$summary['total'] ?> ulasan</strong>
          </div>
          <div>
            <p style="margin:0; font-size:12px; color:#777;">Tingkat Kepuasan</p>
            <strong><?= (int)($room['puas_percent'] ?? 0) ?>%</strong>
          </div>
        </div>
      </div>

      <div class="panel" style="margin-bottom:16px; display:flex; gap:12px; flex-wrap:wrap;">
        <span class="status-chip status-disetujui">Puas: <?= (int)$summary['puas'] ?></span>
        <span class="status-chip status-ditolak">Tidak Puas: <?= (int)$summary['tidak_puas'] ?></span>
        <span class="status-chip status-menunggu">Total: <?= (int)$summary['total'] ?></span>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama User</th>
              <th>Rating</th>
              <th>Komentar</th>
              <th>Tanggal Feedback</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($feedbacks)): ?>
            <tr><td colspan="5" style="text-align:center;">Belum ada feedback untuk ruangan ini.</td></tr>
          <?php else: ?>
            <?php $no=1; foreach ($feedbacks as $fb): ?>
              <?php
                $isPuas = !empty($fb['puas']);
                $badge  = $isPuas ? 'status-disetujui' : 'status-ditolak';
                $label  = $isPuas ? 'Puas' : 'Tidak Puas';
              ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($fb['nama_user'] ?? 'User') ?></td>
                <td><span class="status-chip <?= $badge ?>"><?= $label ?></span></td>
                <td><?= htmlspecialchars($fb['komentar'] ?? '-') ?></td>
                <td><?= $fb['tanggal_feedback'] ? date('d M Y H:i', strtotime($fb['tanggal_feedback'])) : '-' ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>
</body>
</html>
