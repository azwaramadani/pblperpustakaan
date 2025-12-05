<?php
$adminName     = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
$todayBookings = $todayBookings ?? [];
$topRooms      = $topRooms ?? [];
$todayDate     = $todayDate ?? date('Y-m-d');
$feedbacks     = $feedbacks ?? [];
$stats         = $stats ?? ['user_today'=>0,'booking_today'=>0,'room_active'=>0,'user_total'=>0];
$filters       = $filters ?? ['sort_create'=>'desc', 'role'=> '', 'unit'=> '','jurusan'=>'', 'program_studi'=>'', 'keyword'=>''];
$fbFilters     = $fbFilters ?? ['fb_sort_date'=>'desc','fb_sort_feedback'=>'all'];
$roleList    = $roleList ?? [];
$unitList      = $unitList ?? [];
$jurusanList   = $jurusanList ?? [];
$prodiList     = $prodiList ?? [];
$success       = $success ?? null;
$error         = $error ?? null;

//pagination
$pagination  = $pagination ?? ['page'=>1, 'total_pages'=>1, 'limit'=>10, 'total'=>count($todayBookings)];

// buat hitung informasi kayak (menampilkan 1-... data dari ... data) 
$perPage     = (int)($pagination['limit'] ?? 10);
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages  = max(1, (int)($pagination['total_pages'] ?? 1));
$totalRows   = (int)($pagination['total'] ?? count($todayBookings));

$startRow = $totalRows ? (($currentPage - 1) * $perPage + 1) : 0;
$endRow   = $totalRows ? min($startRow + $perPage - 1, $totalRows) : 0;

// Susun query string supaya tombol halaman tetap membawa filter yang dipilih
$queryParams = $_GET ?? [];
$queryParams['route'] = 'Admin/dashboard';
unset($queryParams['page']); // page dipasang ulang sesuai tombol yang diklik
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? ($baseQuery . '&') : 'route=Admin/dashboard&';

// buat nentuin range nomor halaman yang ditampilin (max 5 nomor)
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
          <p style="margin:4px 0 0;">Ringkasan peminjaman ruangan dan feedback.</p>
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
 
      <!-- stats card -->  
      <div class="stats-grid">
        <a href="?route=Admin/dataAkun" class="stats-cardlink">
            <div class="stat-card">
              <p class="stat-number"><?= (int)$stats['user_total'] ?></p>
              <p class="stat-label">Total user</p>
            </div>
        </a>
        <a href="?route=Admin/dataAkun" class="stats-cardlink">
          <div class="stat-card">
            <p class="stat-number"><?= (int)$stats['user_today'] ?></p>
            <p class="stat-label">User register hari ini</p>
          </div>
        </a>
        
        <a href="?route=Admin/dashboard" class="stats-cardlink">
          <div class="stat-card">
            <p class="stat-number"><?= (int)$stats['booking_today'] ?></p>
            <p class="stat-label">Booking hari ini</p>
          </div>
        </a>

        <a href="?route=Admin/dataruangan" class="stats-cardlink">
          <div class="stat-card">
            <p class="stat-number"><?= (int)$stats['room_active'] ?></p>
            <p class="stat-label">Ruangan aktif</p>
          </div>
        </a>  
      </div>

      <!-- Data booking khusus hari ini -->
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Data Booking Hari Ini</h3>
            <p class="subtitle">Booking hari ini, <?= date('d M Y', strtotime($todayDate)) ?></p>
          </div>
        </div>

        <!-- Filter/sort by date + jurusan + prodi -->
        <form class="filter-bar" method="GET" action="">
          <input type="hidden" name="route" value="Admin/dashboard">

          <label>Urut dibuat</label>
          <select name="sort_create">
            <option value="desc" <?= ($filters['sort_create'] === 'desc') ? 'selected' : '' ?>>Terbaru &uarr;</option>
            <option value="asc"  <?= ($filters['sort_create'] === 'asc')  ? 'selected' : '' ?>>Terlama &darr;</option>
          </select>

          <label>Role</label>
          <select name="role">
            <option value="">Semua</option>
            <?php foreach ($roleList as $rl): ?>
              <option value="<?= htmlspecialchars($rl) ?>" <?= ($filters['role']===$rl?'selected':'') ?>><?= htmlspecialchars($rl) ?></option>
            <?php endforeach; ?>
          </select>

          <label>Unit</label>
          <select name="unit">
            <option value="">Semua</option>
            <?php foreach ($unitList as $unl): ?>
              <option value="<?= htmlspecialchars($unl) ?>" <?= ($filters['unit']===$unl?'selected':'') ?>><?= htmlspecialchars($unl) ?></option>
            <?php endforeach; ?>
          </select>    

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
          
          <div class="search-bar">
            <input
              type="text"
              name="keyword"
              placeholder="Cari nama penanggung jawab..."
              value="<?= htmlspecialchars($filters['keyword']) ?>">
            <button type="submit" aria-label="Cari">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="7"></circle>
                <line x1="16.65" y1="16.65" x2="21" y2="21"></line>
              </svg>
            </button>
          </div>

          <button type="submit" class="btn-filter">Terapkan</button>
          <a class="btn-reset" href="?route=Admin/dashboard">Reset</a>
        </form>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Role</th>
                <th>Jurusan</th>
                <th>Program Studi</th>
                <th>Nama Penanggung Jawab</th>
                <th>NIM/NIP Penanggung Jawab</th>
                <th>Total Peminjam</th>
                <th>Tanggal & Waktu Peminjaman</th>
                <th>Dibuat</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($todayBookings)): ?>
                <tr><td colspan="11" class="empty-row">Belum ada booking hari ini.</td></tr>
              <?php else: ?>
                <?php $rowNumber = $startRow ?: 1; ?>
                <?php foreach ($todayBookings as $tb): ?>
                  <?php
                    $tanggal    = $tb['tanggal'] ? date('d M Y', strtotime($tb['tanggal'])) : '-';
                    $jamMulai   = $tb['jam_mulai'] ? date('H:i', strtotime($tb['jam_mulai'])) : '-';
                    $jamSelesai = $tb['jam_selesai'] ? date('H:i', strtotime($tb['jam_selesai'])) : '-';
                    $statusKey = strtolower($tb['status_booking']); 
                  ?>
                  <tr>
                    <td><?= $rowNumber++ ?></td>
                    <td><?= htmlspecialchars($tb['kode_booking']) ?></td>
                    <td><?= htmlspecialchars($tb['role']) ?></td>
                    <td><?= htmlspecialchars($tb['jurusan']) ?></td>
                    <td><?= htmlspecialchars($tb['program_studi'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($tb['nama_penanggung_jawab']) ?></td>
                    <td><?= htmlspecialchars($tb['nimnip_penanggung_jawab']) ?></td>
                    <td><?= (int)$tb['total_peminjam'] ?></td>
                    <td><?= $tanggal ?> | <?= $jamMulai ?> - <?= $jamSelesai ?></td>
                    <td><?= $tb['created_at'] ? date('d M Y H:i', strtotime($tb['created_at'])) : '-' ?></td>
                    <td>
                      <span class="status-chip status-<?= $statusKey ?>">
                        <?= htmlspecialchars($tb['status_booking']) ?>
                      </span>
                    </td>
                    <td>
                      <button class="aksi-btn js-open-status"
                              data-id="<?= $tb['booking_id'] ?>"
                              data-status="<?= htmlspecialchars($tb['status_booking']) ?>">
                        Ubah Status
                      </button>
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
            Menampilkan <?= $startRow ? "{$startRow} - {$endRow}" : "0" ?> dari <?= $totalRows ?> Data.
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

      <!-- Table ruangan -->
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
                    <td><?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</td>
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

<!-- Modal ubah status booking -->
<div class="modal-backdrop" id="modalBookingStatus">
  <div class="modal-card">
    <h4>Ubah Status Peminjaman</h4>
    <form method="POST" action="?route=Admin/updateStatus" id="bookingStatusForm">
      <input type="hidden" name="booking_id" id="bookingIdInput">
      <input type="hidden" name="redirect" value="Admin/dashboard">
      <div class="radio-row">
        <label><input type="radio" name="status_booking" value="Dibatalkan"> Dibatalkan</label>
        <label><input type="radio" name="status_booking" value="Selesai"> Selesai</label>
      </div>
      <div class="modal-actions">
        <button type="submit" class="btn-pill btn-save">Simpan</button>
        <button type="button" class="btn-pill btn-cancel js-close-status">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function() {
    const modal = document.getElementById('modalBookingStatus');
    const idInput = document.getElementById('bookingIdInput');
    const radios = modal.querySelectorAll('input[name="status_booking"]');

    document.querySelectorAll('.js-open-status').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const status = btn.dataset.status || '';
        idInput.value = id;
        radios.forEach(r => { r.checked = (r.value === status); });
        modal.style.display = 'flex';
      });
    });

    document.querySelectorAll('.js-close-status').forEach(btn => {
      btn.addEventListener('click', () => modal.style.display = 'none');
    });

    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.style.display = 'none';
    });
  })();
</script>
</body>
</html>
