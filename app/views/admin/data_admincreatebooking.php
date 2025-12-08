<?php
$adminName   = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
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
      <a href="?route=Admin/dataPeminjaman">Data Peminjaman</a>
      <a href="?route=Admin/dataRuangan">Data Ruangan</a>
      <a href="?route=Admin/dataFromAdminCreateBooking" class="active">Data Pinjam Admin</a>
      <a href="?route=Admin/dataAkun">Data Akun</a>
      <a href="?route=Auth/logout">Keluar</a>
    </nav>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Peminjaman oleh Admin</h2>
          <p>Semua data peminjaman yang dibuat oleh admin.</p>
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

    
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Data Peminjaman Admin</h3>
            <p class="subtitle">Semua peminjaman ruangan yang dibuat oleh admin</p>
          </div>
        </div>

        <!-- Filter/sort dan search-->
        <form class="filter-bar" method="GET" action="">
          <input type="hidden" name="route" value="Admin/datapeminjaman">

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
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="7"></circle>
                <line x1="16.65" y1="16.65" x2="21" y2="21"></line>
              </svg>
            </button>
          </div>

          <button type="submit" class="btn-filter">Terapkan</button>
          <a class="btn-reset" href="?route=Admin/datapeminjaman">Reset</a>
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
                <tr><td colspan="10" class="empty-row">Belum ada data booking.</td></tr>
              <?php else: ?>
                <?php $rowNumber = 1; ?>
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
                        Ubah Status
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>

<!-- Modal ubah status booking -->
<div class="modal-backdrop" id="modalBookingStatus">
  <div class="modal-card">
    <h4>Ubah Status Peminjaman</h4>
    <form method="POST" action="?route=Admin/updateStatus" id="bookingStatusForm">
      <input type="hidden" name="booking_id" id="bookingIdInput">
      <input type="hidden" name="redirect" value="Admin/dataFromAdminCreateBooking">
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