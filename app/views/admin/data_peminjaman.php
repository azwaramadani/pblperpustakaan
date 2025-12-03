<?php
$adminName   = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
$filters     = $filters ?? ['sort_date'=>'desc','from_date'=>'','to_date'=>'', 'jurusan'=>'', 'program_studi'=>''];
$bookings    = $bookings ?? [];
$jurusanList = $jurusanList ?? [];
$prodiList   = $prodiList ?? [];
$pagination  = $pagination ?? ['page'=>1, 'total_pages'=>1, 'limit'=>10, 'total'=>count($bookings)];

// Hitung informasi pagination untuk ditampilkan di UI
$perPage     = (int)($pagination['limit'] ?? 15);
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages  = max(1, (int)($pagination['total_pages'] ?? 1));
$totalRows   = (int)($pagination['total'] ?? count($bookings));

$startRow = $totalRows ? (($currentPage - 1) * $perPage + 1) : 0;
$endRow   = $totalRows ? min($startRow + $perPage - 1, $totalRows) : 0;

// Susun query string supaya tombol halaman tetap membawa filter yang dipilih
$queryParams = $_GET ?? [];
$queryParams['route'] = 'Admin/dataPeminjaman';
unset($queryParams['page']); // page dipasang ulang sesuai tombol yang diklik
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? ($baseQuery . '&') : 'route=Admin/dataPeminjaman&';

// Tentukan range nomor halaman yang ditampilkan (max 5 nomor)
$maxLinks   = 5;
$startPage  = max(1, $currentPage - 2);
$endPage    = min($totalPages, $currentPage + 2);
if (($endPage - $startPage + 1) < $maxLinks) {
    $needed    = $maxLinks - ($endPage - $startPage + 1);
    $startPage = max(1, $startPage - $needed);
    $endPage   = min($totalPages, $startPage + $maxLinks - 1);
}
$noData        = $totalRows === 0;
$disablePrev   = $noData || $currentPage <= 1;
$disableNext   = $noData || $currentPage >= $totalPages;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Peminjaman - Rudy</title>
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
      <a href="?route=Admin/dataPeminjaman" class="active">Data Peminjaman</a>
      <a href="?route=Admin/dataRuangan">Data Ruangan</a>
      <a href="?route=Admin/dataAkun">Data Akun</a>
      <a href="?route=Auth/logout">Keluar</a>
    </nav>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Peminjaman</h2>
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

    <!-- Panel Data Booking -->
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Data Booking</h3>
            <p class="subtitle">Semua peminjaman ruangan oleh user</p>
          </div>
        </div>

        <!-- Filter/sort by date + jurusan + prodi -->
        <form class="filter-bar" method="GET" action="">
          <input type="hidden" name="route" value="Admin/datapeminjaman">

          <label>Urut tanggal</label>
          <select name="sort_date">
            <option value="desc" <?= ($filters['sort_date'] === 'desc') ? 'selected' : '' ?>>Terbaru &uarr;</option>
            <option value="asc"  <?= ($filters['sort_date'] === 'asc')  ? 'selected' : '' ?>>Terlama &darr;</option>
          </select>

          <label>Dari</label>
          <input type="date" name="from_date" value="<?= htmlspecialchars($filters['from_date']) ?>">

          <label>Sampai</label>
          <input type="date" name="to_date" value="<?= htmlspecialchars($filters['to_date']) ?>">

          <label>Jurusan</label>
          <select name="jurusan">
            <option value="">Semua</option>
            <?php foreach ($jurusanList as $jrl): ?>
              <option value="<?= htmlspecialchars($jrl) ?>" <?= ($filters['jurusan']===$jrl?'selected':'') ?>><?= htmlspecialchars($jrl) ?></option>
            <?php endforeach; ?>
          </select>

          <label>Program Studi</label>
          <select name="program_studi">
            <option value="">Semua</option>
            <?php foreach ($prodiList as $prl): ?>
              <option value="<?= htmlspecialchars($prl) ?>" <?= ($filters['program_studi']===$prl?'selected':'') ?>><?= htmlspecialchars($prl) ?></option>
            <?php endforeach; ?>
          </select>

          <button type="submit" class="btn-filter">Terapkan</button>
          <a class="btn-reset" href="?route=Admin/datapeminjaman">Reset</a>
        </form>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Kode Booking</th>
                <th>Role</th>
                <th>Jurusan</th>
                <th>Program Studi</th>
                <th>Nama User</th>
                <th>NIM/NIP</th>
                <th>Ruangan</th>
                <th>Tanggal & Jam</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($bookings)): ?>
                <tr><td colspan="8" class="empty-row">Belum ada data booking.</td></tr>
              <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                  <?php
                    $tanggal    = $b['tanggal'] ? date('d M Y', strtotime($b['tanggal'])) : '-';
                    $jamMulai   = $b['jam_mulai'] ? date('H:i', strtotime($b['jam_mulai'])) : '-';
                    $jamSelesai = $b['jam_selesai'] ? date('H:i', strtotime($b['jam_selesai'])) : '-';
                    $statusKey  = strtolower($b['status_booking']);
                  ?>
                  <tr>
                    <td><?= htmlspecialchars($b['kode_booking']) ?></td>
                    <td><?= htmlspecialchars($b['role']) ?></td>
                    <td><?= htmlspecialchars($b['jurusan']) ?></td>
                    <td><?= htmlspecialchars($b['program_studi']) ?></td>
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
        <!-- Kontrol pagination -->
        <div class="pagination-bar">
          <div class="pagination-info">
            Menampilkan <?= $startRow ? "{$startRow} - {$endRow}" : "0" ?> dari <?= $totalRows ?> data (<?= $perPage ?> per halaman)
          </div>
          <div class="pagination-nav">
            <a class="page-btn secondary <?= $disablePrev ? 'disabled' : '' ?>" href="?<?= $baseQuery ?>page=1">« Pertama</a>
            <a class="page-btn secondary <?= $disablePrev ? 'disabled' : '' ?>" href="?<?= $baseQuery ?>page=<?= max(1, $currentPage - 1) ?>">‹ Sebelumnya</a>

            <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
              <a class="page-btn <?= ($p === $currentPage) ? 'active' : 'secondary' ?>" href="?<?= $baseQuery ?>page=<?= $p ?>"><?= $p ?></a>
            <?php endfor; ?>

            <a class="page-btn secondary <?= $disableNext ? 'disabled' : '' ?>" href="?<?= $baseQuery ?>page=<?= min($totalPages, $currentPage + 1) ?>">Berikutnya ›</a>
            <a class="page-btn secondary <?= $disableNext ? 'disabled' : '' ?>" href="?<?= $baseQuery ?>page=<?= $totalPages ?>">Terakhir »</a>
          </div>
        </div>
      </section>
</body>
</html>