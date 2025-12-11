<?php
$adminName   = $admin['username'] ?? ($admin['nama'] ?? 'Admin');

//pagination
$pagination  = $pagination ?? ['page'=>1, 'total_pages'=>1, 'limit'=>10, 'total'=>count($bookings)];

//buat hitung informasi kayak (menampilkan 1-... data dari ... data) 
$perPage     = (int)($pagination['limit'] ?? 10);
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages  = max(1, (int)($pagination['total_pages'] ?? 1));
$totalRows   = (int)($pagination['total'] ?? count($bookings));

$startRow = $totalRows ? (($currentPage - 1) * $perPage + 1) : 0;
$endRow   = $totalRows ? min($startRow + $perPage - 1, $totalRows) : 0;

// Susun query string supaya tombol halaman tetap membawa filter yang dipilih
$queryParams                 = $_GET ?? [];
$queryParams['route']        = 'Admin/dataFromAdminCreateBooking';
unset($queryParams['page']); // page dipasang ulang sesuai tombol yang diklik
$baseQuery                   = http_build_query($queryParams);
$baseQuery                   = $baseQuery ? ($baseQuery . '&') : 'route=Admin/dataFromAdminCreateBooking&';

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
  <!-- Update versi CSS agar browser memuat tampilan terbaru -->
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css?v=1.6">
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
      <a href="?route=Admin/dataRuangan">
        <i class="fa-solid fa-door-open"></i> Data Ruangan
      </a>
      <!-- Menu Aktif -->
      <a href="?route=Admin/dataFromAdminCreateBooking" class="active">
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
    <!-- HEADER (TOP NAV) -->
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Peminjaman oleh Admin</h2>
          <p style="margin:4px 0 0;">Semua data peminjaman yang dibuat oleh admin.</p>
        </div>
      </div>
      
      <!-- PERBAIKAN: Profil dihapus, diganti tombol Buat Laporan -->
      <div class="header-actions">
        <a href="?route=Admin/buatLaporan" class="btn-laporan">
            <i class="fa-solid fa-plus"></i> Buat Laporan
        </a>
      </div>
    </header>

    <main class="content">
      <?php if (!empty($success)): ?>
        <div class="flash success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="flash error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

    
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Data Peminjaman Admin</h3>
            <p class="subtitle">Semua peminjaman ruangan yang dibuat oleh admin</p>
          </div>
        </div>

        <!-- Filter/sort dan search-->
        <form class="filter-bar" method="GET" action="">
          <input type="hidden" name="route" value="Admin/dataFromAdminCreateBooking">

          <label>Urut tanggal</label>
          <select name="sort_date">
            <option value="desc" <?= ($filters['sort_date'] === 'desc') ? 'selected' : '' ?>>Terbaru</option>
            <option value="asc"  <?= ($filters['sort_date'] === 'asc')  ? 'selected' : '' ?>>Terlama</option>
          </select>

          <label>Dari</label>
          <input type="date" name="from_date" value="<?= htmlspecialchars($filters['from_date']) ?>">

          <label>Sampai</label>
          <input type="date" name="to_date" value="<?= htmlspecialchars($filters['to_date']) ?>">

          <div class="search-bar">
            <input
              type="text"
              name="keyword"
              placeholder="Cari nama penanggung jawab..."
              value="<?= htmlspecialchars($filters['keyword']) ?>">
            <button type="submit" aria-label="Cari">
              <i class="fa-solid fa-magnifying-glass" style="color:var(--yellow-dark)"></i>
            </button>
          </div>

          <button type="submit" class="btn-filter">Terapkan</button>
          <a class="btn-reset" href="?route=Admin/dataFromAdminCreateBooking">Reset</a>
        </form>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Nama Penanggung Jawab</th>
                <th>NIM/NIP Penanggung Jawab</th>
                <th>Email Penanggung Jawab</th>
                <th>Total Peminjam</th>
                <th>Ruangan</th>
                <th>Waktu Peminjaman</th>
                <th>Waktu Dibuat</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($bookings)): ?>
                <tr><td colspan="11" class="empty-row" style="text-align:center; padding:20px;">Belum ada data booking.</td></tr>
              <?php else: ?>
                <?php $rowNumber = $startRow ?: 1; ?>
                <?php foreach ($bookings as $b): ?>
                  <?php
                    $tanggal    = $b['tanggal'] ? date('d M Y', strtotime($b['tanggal'])) : '-';
                    $jamMulai   = $b['jam_mulai'] ? date('H:i', strtotime($b['jam_mulai'])) : '-';
                    $jamSelesai = $b['jam_selesai'] ? date('H:i', strtotime($b['jam_selesai'])) : '-';
                    $statusKey  = strtolower($b['status_booking']);
                  ?>
                  <tr>
                    <td><?= $rowNumber++ ?></td>
                    <td><?= htmlspecialchars($b['kode_booking']) ?></td>
                    <td><?= htmlspecialchars($b['nama_penanggung_jawab']?? '-') ?></td>
                    <td><?= htmlspecialchars($b['nimnip_penanggung_jawab']?? '-') ?></td>
                    <td><?= htmlspecialchars($b['email_penanggung_jawab']?? '-') ?></td>
                    <td><?= (int)$b['total_peminjam'] ?? '-'?></td>
                    <td><?= htmlspecialchars($b['nama_ruangan']?? '-') ?></td>
                    <td><?= $tanggal ?> | <?= $jamMulai ?> - <?= $jamSelesai ?></td>
                    <td><?= htmlspecialchars($b['created_at']) ?></td>
                    <td>
                      <span class="status-chip status-<?= $statusKey ?>">
                        <?= htmlspecialchars($b['status_booking']) ?>
                      </span>
                    </td>
                    <td>
                      <button class="aksi-btn js-open-status"
                              data-id="<?= $b['booking_id'] ?>"
                              data-status="<?= htmlspecialchars($b['status_booking']) ?>">
                        <i class="fa-solid fa-pen-to-square"></i> Ubah
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
    </main>

<!-- Modal ubah status booking -->
<div class="modal-backdrop" id="modalBookingStatus">
  <div class="modal-card">
    <h4 style="margin-top:0;">Ubah Status Peminjaman</h4>
    <form method="POST" action="?route=Admin/updateStatus" id="bookingStatusForm">
      <input type="hidden" name="booking_id" id="bookingIdInput">
      <input type="hidden" name="redirect" value="Admin/dataFromAdminCreateBooking">
      
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
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });
  })();
</script>

</body>
</html>