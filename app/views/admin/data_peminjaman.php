<?php
$bookings = $todayBookings ?? [];
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
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
      <p>Panel Admin</p>
    </div>
    <nav class="sidebar-nav">
      <a href="?route=Admin/dashboard">Dashboard</a>
      <a href="?route=Admin/dataPeminjaman" class="active">Data Peminjaman</a>
      <a href="?route=Admin/dataRuangan">Data Ruangan</a>
      <a href="?route=Auth/logout">Keluar</a>
    </nav>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Peminjaman (Hari Ini)</h2>
          <p style="margin:4px 0 0;">Peminjaman tanggal <?= date('d M Y') ?></p>
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

      <?php if (empty($bookings)): ?>
        <div class="panel">
          <p class="subtitle">Belum ada peminjaman untuk hari ini.</p>
        </div>
      <?php else: ?>
        <?php foreach ($bookings as $b): ?>
          <?php
            $tanggal = $b['tanggal'] ? date('d M Y', strtotime($b['tanggal'])) : '-';
            $jamMulai = $b['jam_mulai'] ? date('H:i', strtotime($b['jam_mulai'])) : '-';
            $jamSelesai = $b['jam_selesai'] ? date('H:i', strtotime($b['jam_selesai'])) : '-';
            $statusKey = strtolower($b['status_booking']);
            $img = $b['gambar_ruangan'] ?: 'public/assets/image/contohruangan.png';
            $imgUrl = preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/');
          ?>
          <div class="booking-card">
            <div class="booking-info">
              <h3><?= htmlspecialchars($b['nama_ruangan']) ?></h3>
              <p><strong>Kode Booking:</strong> <?= htmlspecialchars($b['kode_booking']) ?></p>
              <p><strong>Waktu Peminjaman:</strong> <?= $tanggal ?></p>
              <p><strong>Jam Peminjaman:</strong> <?= $jamMulai ?> - <?= $jamSelesai ?></p>
              <p><strong>Nama Penanggung Jawab:</strong> <?= htmlspecialchars($b['nama_penanggung_jawab']) ?></p>
              <p><strong>NIM Penanggung Jawab:</strong> <?= htmlspecialchars($b['nimnip_penanggung_jawab']) ?></p>
              <p><strong>Email Penanggung Jawab:</strong> <?= htmlspecialchars($b['email_penanggung_jawab']) ?></p>
              <p><strong>NIM Peminjam Ruangan:</strong> <?= htmlspecialchars($b['nimnip_peminjam']) ?></p>
              <p><strong>Status:</strong>
                <span class="status-chip status-<?= $statusKey ?>"><?= htmlspecialchars($b['status_booking']) ?></span>
              </p>
            </div>
            <div class="booking-actions">
              <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="<?= htmlspecialchars($b['nama_ruangan']) ?>">
              <button class="btn-round btn-ubah js-open-modal"
                      data-id="<?= $b['booking_id'] ?>"
                      data-status="<?= htmlspecialchars($b['status_booking']) ?>">
                Ubah Status
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </main>
  </div>
</div>

<!-- Modal -->
<div class="modal-backdrop" id="modalStatus">
  <div class="modal-card">
    <h4>Ubah Status Peminjaman</h4>
    <form method="POST" action="?route=Admin/updateStatus" id="statusForm">
      <input type="hidden" name="booking_id" id="bookingIdInput">
      <div class="radio-row">
        <?php
          $options = ['Disetujui','Ditolak','Dibatalkan','Selesai'];
          foreach ($options as $opt):
        ?>
          <label>
            <input type="radio" name="status_booking" value="<?= $opt ?>"> <?= $opt ?>
          </label>
        <?php endforeach; ?>
      </div>
      <div class="modal-actions">
        <button type="submit" class="btn-pill btn-save">Simpan</button>
        <button type="button" class="btn-pill btn-cancel js-close-modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
  const modal = document.getElementById('modalStatus');
  const idInput = document.getElementById('bookingIdInput');
  const radioBtns = document.querySelectorAll('input[name="status_booking"]');

  document.querySelectorAll('.js-open-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const status = btn.dataset.status || '';
      idInput.value = id;
      radioBtns.forEach(r => { r.checked = (r.value === status); });
      modal.style.display = 'flex';
    });
  });

  document.querySelectorAll('.js-close-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      modal.style.display = 'none';
    });
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
  });
</script>
</body>
</html>