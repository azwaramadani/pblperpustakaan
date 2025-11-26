<?php
$topRooms  = $topRooms ?? [];
$bookings  = $bookings ?? [];
$feedbacks = $feedbacks ?? [];
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
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
                    <td><?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</td>
                    <td><?= (int) ($room['total_booking'] ?? 0) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Data Booking</h3>
            <p class="subtitle">Semua peminjaman ruangan oleh user</p>
          </div>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Kode Booking</th>
                <th>Nama User</th>
                <th>NIM/NIP</th>
                <th>Ruangan</th>
                <th>Tanggal & Jam</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($bookings)): ?>
                <tr><td colspan="6" class="empty-row">Belum ada data booking.</td></tr>
              <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                  <?php
                    $tanggal = $b['tanggal'] ? date('d M Y', strtotime($b['tanggal'])) : '-';
                    $jamMulai = $b['jam_mulai'] ? date('H:i', strtotime($b['jam_mulai'])) : '-';
                    $jamSelesai = $b['jam_selesai'] ? date('H:i', strtotime($b['jam_selesai'])) : '-';
                    $statusKey = strtolower($b['status_booking']);
                  ?>
                  <tr>
                    <td><?= htmlspecialchars($b['kode_booking']) ?></td>
                    <td><?= htmlspecialchars($b['nama_user']) ?></td>
                    <td><?= htmlspecialchars($b['nim_nip']) ?></td>
                    <td><?= htmlspecialchars($b['nama_ruangan']) ?></td>
                    <td><?= $tanggal ?> | <?= $jamMulai ?> - <?= $jamSelesai ?></td>
                    <td>
                      <span class="status-chip status-<?= $statusKey ?>">
                        <?= htmlspecialchars($b['status_booking']) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Feedback Ruangan</h3>
            <p class="subtitle">Data join ruangan dengan feedback yang diberikan user</p>
          </div>
        </div>
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
