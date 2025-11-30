<?php
$topRooms    = $topRooms ?? [];
$bookings    = $bookings ?? [];
$feedbacks   = $feedbacks ?? [];
$users       = $users ?? [];
$adminName   = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
$stats       = $stats ?? ['user_today'=>0,'booking_today'=>0,'room_active'=>0,'user_total'=>0];
$filters     = $filters ?? ['sort_date'=>'desc','from_date'=>'','to_date'=>'', 'jurusan'=>'', 'program_studi'=>''];
$fbFilters   = $fbFilters ?? ['fb_sort_date'=>'desc','fb_sort_feedback'=>'all'];
$jurusanList = $jurusanList ?? [];
$prodiList = $prodiList ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - Rudy</title>
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
      <a href="?route=Admin/dashboard" class="active">Dashboard</a>
      <a href="?route=Admin/dataPeminjaman">Data Peminjaman</a>
      <a href="?route=Admin/dataRuangan">Data Ruangan</a>
      <a href="?route=Admin/dataAkun">Data Akun</a>
      <a href="?route=Auth/logout">Keluar</a>
    </nav>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Rudy">
        <div>
          <h2 style="margin:0;">Dashboard Admin</h2>
          <p style="margin:4px 0 0;">Ringkasan peminjaman ruangan dan feedback</p>
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
      <!-- Kartu ringkasan -->
      <div class="stats-grid">
        <div class="stat-card">
          <p class="stat-number"><?= (int)$stats['user_today'] ?></p>
          <p class="stat-label">User register hari ini</p>
        </div>
        <div class="stat-card">
          <p class="stat-number"><?= (int)$stats['booking_today'] ?></p>
          <p class="stat-label">Booking hari ini</p>
        </div>
        <div class="stat-card">
          <p class="stat-number"><?= (int)$stats['room_active'] ?></p>
          <p class="stat-label">Ruangan aktif hari ini</p>
        </div>
        <div class="stat-card">
          <p class="stat-number"><?= (int)$stats['user_total'] ?></p>
          <p class="stat-label">Total user</p>
        </div>
      </div>

      <!-- Panel Ruangan -->
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Ruangan Paling Banyak Dibooking</h3>
            <p class="subtitle">Urut dari jumlah booking terbanyak</p>
          </div>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Ruangan</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th>Total Booking</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($topRooms)): ?>
                <tr><td colspan="5" class="empty-row">Belum ada data booking.</td></tr>
              <?php else: ?>
                <?php foreach ($topRooms as $i => $room): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($room['nama_ruangan']) ?></td>
                    <td><?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> org</td>
                    <td><?= htmlspecialchars($room['status']) ?></td>
                    <td><?= (int) ($room['total_booking'] ?? 0) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      
                  






















      <!-- Panel Feedback Ruangan -->
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Feedback Ruangan</h3>
            <p class="subtitle">Data join ruangan dengan feedback yang diberikan user</p>
          </div>
        </div>

        <!-- Filter feedback -->
        <form class="filter-bar" method="GET" action="">
          <input type="hidden" name="route" value="Admin/dashboard">
          <label>Urut tanggal</label>
          <select name="fb_sort_date">
            <option value="desc" <?= ($fbFilters['fb_sort_date'] === 'desc') ? 'selected' : '' ?>>Terbaru &uarr;</option>
            <option value="asc"  <?= ($fbFilters['fb_sort_date'] === 'asc')  ? 'selected' : '' ?>>Terlama &darr;</option>
          </select>

          <label>Feedback</label>
          <select name="fb_feedback">
            <option value="all"   <?= ($fbFilters['fb_sort_feedback'] === 'all')   ? 'selected' : '' ?>>Semua</option>
            <option value="puas"  <?= ($fbFilters['fb_sort_feedback'] === 'puas')  ? 'selected' : '' ?>>Puas</option>
            <option value="tidak" <?= ($fbFilters['fb_sort_feedback'] === 'tidak') ? 'selected' : '' ?>>Tidak Puas</option>
          </select>

          <button type="submit" class="btn-filter">Terapkan</button>
          <a class="btn-reset" href="?route=Admin/dashboard">Reset</a>
        </form>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Kode Booking</th>
                <th>Ruangan</th>
                <th>Nama User</th>
                <th>Feedback</th>
                <th>Komentar</th>
                <th>Tanggal Feedback</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($feedbacks)): ?>
                <tr><td colspan="6" class="empty-row">Belum ada feedback.</td></tr>
              <?php else: ?>
                <?php foreach ($feedbacks as $f): ?>
                  <tr>
                    <td><?= htmlspecialchars($f['kode_booking'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($f['nama_ruangan']) ?></td>
                    <td><?= htmlspecialchars($f['nama_user']) ?> (<?= htmlspecialchars($f['nim_nip']) ?>)</td>
                    <td><?= !empty($f['puas']) ? 'Puas' : 'Tidak Puas' ?></td>
                    <td><?= nl2br(htmlspecialchars($f['komentar'] ?? '-')) ?></td>
                    <td><?= $f['tanggal_feedback'] ? date('d M Y H:i', strtotime($f['tanggal_feedback'])) : '-' ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</div>
</body>
</html>
