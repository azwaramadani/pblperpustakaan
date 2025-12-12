<?php
$adminName     = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
$todayDate     = $todayDate ?? date('Y-m-d');
$stats         = $stats ?? ['user_mustvalidate'=>0, 'user_today'=>0,'booking_today'=>0,'room_active'=>0,'user_total'=>0];

//pagination logic
$pagination  = $pagination ?? ['page'=>1, 'total_pages'=>1, 'limit'=>10, 'total'=>count($todayBookings)];
$perPage     = (int)($pagination['limit'] ?? 10);
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages  = max(1, (int)($pagination['total_pages'] ?? 1));
$totalRows   = (int)($pagination['total'] ?? count($todayBookings));

$startRow = $totalRows ? (($currentPage - 1) * $perPage + 1) : 0;
$endRow   = $totalRows ? min($startRow + $perPage - 1, $totalRows) : 0;

$queryParams = $_GET ?? [];
$queryParams['route'] = 'Admin/dashboard';
unset($queryParams['page']);
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? ($baseQuery . '&') : 'route=Admin/dashboard&';

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
  <!-- Pastikan path CSS ini sesuai dengan struktur folder Anda -->
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css?v=1.2">
  <!-- FontAwesome untuk icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="admin-body">
<div class="admin-layout">
  
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Rudy">
    </div>
    
    <nav class="sidebar-nav">
      <!-- Menambahkan Icon <i> pada setiap menu -->
      <a href="?route=Admin/dashboard" class="active">
        <i class="fa-solid fa-chart-line"></i> Dashboard
      </a>
      <a href="?route=Admin/dataPeminjaman">
        <i class="fa-solid fa-calendar-check"></i> Data Peminjaman
      </a>
      <a href="?route=Admin/dataRuangan">
        <i class="fa-solid fa-door-open"></i> Data Ruangan
      </a>
      <a href="?route=Admin/dataFromAdminCreateBooking">
        <i class="fa-solid fa-user-tag"></i> Data Pinjam Admin
      </a>
      <a href="?route=Admin/dataAkun">
        <i class="fa-solid fa-users"></i> Data Akun
      </a>
      <!-- Menu Keluar -->
      <a href="?route=Auth/logout" style="color: var(--danger) !important;">
        <i class="fa-solid fa-right-from-bracket" style="color: var(--danger) !important;"></i> Keluar
      </a>
    </nav>

    <!-- PROFIL (Otomatis di paling bawah karena CSS margin-top: auto) -->
    <div class="sidebar-footer">
      <img src="public/assets/image/userlogo.png" class="avatar-img" alt="Admin">
      <div class="user-info">
        <span class="name">adminrudy1</span>
        <span style="font-size:11px; color:#6b7280;">Administrator</span>
      </div>
    </div>
  </aside>

  <!-- AREA KONTEN UTAMA -->
  <div class="main-area">
    
    <!-- HEADER (TOP NAV) -->
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2>Dashboard Admin</h2>
          <p>Ringkasan statistik dan peminjaman hari ini.</p>
        </div>
      </div>
    </header>

    <main class="content">
      <!-- Flash Messages -->
      <?php if (!empty($success)): ?>
        <div class="flash success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="flash error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
 
      <!-- Stats Grid -->  
      <div class="stats-grid">
        <a href="?route=Admin/dataAkun" class="stats-cardlink">
          <div class="stat-card">
            <p class="stat-number"><?= (int)$stats['user_mustvalidate'] ?></p>
            <p class="stat-label">Akun perlu validasi</p>
          </div>
        </a>
        <a href="?route=Admin/dataAkun" class="stats-cardlink">
          <div class="stat-card">
            <p class="stat-number"><?= (int)$stats['user_today'] ?></p>
            <p class="stat-label">User baru hari ini</p>
          </div>
        </a>
        <a href="?route=Admin/dataAkun" class="stats-cardlink">
            <div class="stat-card">
              <p class="stat-number"><?= (int)$stats['user_total'] ?></p>
              <p class="stat-label">Total User</p>
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
            <p class="stat-label">Ruangan Aktif</p>
          </div>
        </a>  
      </div>

      <!-- Tabel Data Booking -->
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Data Booking Hari Ini</h3>
            <p class="subtitle"><?= date('d F Y', strtotime($todayDate)) ?></p>
          </div>
        </div>

        <form class="filter-bar" method="GET" action="">
          <input type="hidden" name="route" value="Admin/dashboard">

          <!-- Search Bar -->
          <div class="search-bar" style="flex: 1; min-width: 250px;">
            <input type="text" name="keyword" placeholder="Cari nama/NIM..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
            <button type="submit" style="background:none; border:none; cursor:pointer;"><i class="fa-solid fa-magnifying-glass" style="color:var(--yellow-dark)"></i></button>
          </div>

          <!-- Sort -->
          <select name="sort_create">
            <option value="desc" <?= ($filters['sort_create'] === 'desc') ? 'selected' : '' ?>>Terbaru</option>
            <option value="asc"  <?= ($filters['sort_create'] === 'asc')  ? 'selected' : '' ?>>Terlama</option>
          </select>

          <!-- Filter Role -->
          <select name="role">
            <option value="">- Role -</option>
            <?php foreach ($roleList as $rl): ?>
              <option value="<?= htmlspecialchars($rl) ?>" <?= ($filters['role']===$rl?'selected':'') ?>><?= htmlspecialchars($rl) ?></option>
            <?php endforeach; ?>
          </select>

          <!-- Filter Unit (Dikembalikan) -->
          <select name="unit">
            <option value="">- Unit -</option>
            <?php foreach ($unitList as $unl): ?>
              <option value="<?= htmlspecialchars($unl) ?>" <?= ($filters['unit']===$unl?'selected':'') ?>><?= htmlspecialchars($unl) ?></option>
            <?php endforeach; ?>
          </select>

          <!-- Filter Jurusan (Dikembalikan) -->
          <select name="jurusan">
            <option value="">- Jurusan -</option>
            <?php foreach ($jurusanList as $jrl): ?>
              <option value="<?= htmlspecialchars($jrl) ?>" <?= ($filters['jurusan']===$jrl?'selected':'') ?>><?= htmlspecialchars($jrl) ?></option>
            <?php endforeach; ?>
          </select>

          <!-- Filter Prodi (Dikembalikan) -->
          <select name="program_studi">
            <option value="">- Prodi -</option>
            <?php foreach ($prodiList as $prl): ?>
              <option value="<?= htmlspecialchars($prl) ?>" <?= ($filters['program_studi']===$prl?'selected':'') ?>><?= htmlspecialchars($prl) ?></option>
            <?php endforeach; ?>
          </select>

          <button type="submit" class="btn-filter">Terapkan</button>
          <a class="btn-reset" href="?route=Admin/dashboard">Reset</a>
        </form>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Peminjam</th>
                <th>Detail Akademik</th> <!-- Kolom Baru agar info Unit/Jurusan terlihat -->
                <th>Ruangan</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($todayBookings)): ?>
                <tr><td colspan="8" class="empty-row" style="text-align:center; padding:20px;">Belum ada booking hari ini.</td></tr>
              <?php else: ?>
                <?php $rowNumber = $startRow ?: 1; ?>
                <?php foreach ($todayBookings as $tb): ?>
                  <?php
                    $jamMulai   = $tb['jam_mulai'] ? date('H:i', strtotime($tb['jam_mulai'])) : '-';
                    $jamSelesai = $tb['jam_selesai'] ? date('H:i', strtotime($tb['jam_selesai'])) : '-';
                    $statusKey = strtolower($tb['status_booking']); 
                  ?>
                  <tr>
                    <td><?= $rowNumber++ ?></td>
                    <td><?= htmlspecialchars($tb['kode_booking']?? '-') ?></td>
                    <td>
                        <strong><?= htmlspecialchars($tb['nama_penanggung_jawab'] ?? '-') ?></strong><br>
                        <small style="color:gray;">NIM: <?= htmlspecialchars($tb['nimnip_penanggung_jawab'] ?? '-') ?></small>
                    </td>
                    <!-- Menampilkan info filter di tabel -->
                    <td>
                        <small>Unit: <?= htmlspecialchars($tb['unit'] ?? '-') ?></small><br>
                        <small>Jurusan: <?= htmlspecialchars($tb['jurusan'] ?? '-') ?></small>
                    </td>
                    <td><?= htmlspecialchars($tb['nama_ruangan'] ?? '-') ?></td>
                    <td><?= $jamMulai ?> - <?= $jamSelesai ?></td>
                    <td>
                      <span class="status-chip status-<?= $statusKey ?>">
                        <?= htmlspecialchars($tb['status_booking']) ?>
                      </span>
                    </td>
                    <td>
                      <button class="aksi-btn js-open-status"
                              data-id="<?= $tb['booking_id'] ?>"
                              data-status="<?= htmlspecialchars($tb['status_booking']) ?>">
                        <i class="fa-solid fa-pen-to-square"></i> Ubah
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-bar">
          <div class="pagination-info">
            Data <?= $startRow ? "{$startRow}-{$endRow}" : "0" ?> dari <?= $totalRows ?>
          </div>
          <div class="pagination-nav">
             <?php if(!$disablePrev): ?>
                <a class="page-btn" href="?<?= $baseQuery ?>page=<?= max(1, $currentPage - 1) ?>">‹</a>
             <?php endif; ?>
             <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
               <a class="page-btn <?= ($p === $currentPage) ? 'active' : '' ?>" href="?<?= $baseQuery ?>page=<?= $p ?>"><?= $p ?></a>
             <?php endfor; ?>
             <?php if(!$disableNext): ?>
                <a class="page-btn" href="?<?= $baseQuery ?>page=<?= min($totalPages, $currentPage + 1) ?>">›</a>
             <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- Tabel Ruangan Populer -->
      <section class="panel">
        <div class="section-head">
            <h3>Ruangan Populer</h3>
        </div>
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Ruangan</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th>Total Booking</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($topRooms)): ?>
                <?php foreach ($topRooms as $i => $room): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($room['nama_ruangan']) ?></td>
                    <td><?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?></td>
                    <td><?= htmlspecialchars($room['status']) ?></td>
                    <td><?= (int) ($room['total_booking'] ?? 0) ?></td>
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

<!-- Modal Status -->
<div class="modal-backdrop" id="modalBookingStatus">
  <div class="modal-card">
    <h4 style="margin-top:0;">Ubah Status</h4>
    <form method="POST" action="?route=Admin/updateStatus" id="bookingStatusForm">
      <input type="hidden" name="booking_id" id="bookingIdInput">
      <input type="hidden" name="redirect" value="Admin/dashboard">
      <div style="margin:20px 0; display:flex; gap:15px; justify-content:center;">
        <label><input type="radio" name="status_booking" value="Dibatalkan"> Dibatalkan</label>
        <label><input type="radio" name="status_booking" value="Selesai"> Selesai</label>
      </div>
      <div style="text-align:right;">
        <button type="button" class="aksi-btn js-close-status" style="margin-right:10px;">Batal</button>
        <button type="submit" class="aksi-btn" style="background:#22b6b3; color:white; border-color:#22b6b3;">Simpan</button>
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

    const closeModal = () => modal.style.display = 'none';
    document.querySelectorAll('.js-close-status').forEach(btn => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
  })();
</script>
</body>
</html>