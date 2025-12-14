<?php
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Ruangan - Rudy</title>
  <!-- Update versi CSS agar cache ter-refresh -->
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css?v=1.5">
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
      <a href="?route=Admin/dashboard">
        <i class="fa-solid fa-chart-line"></i> Dashboard
      </a>
      <a href="?route=Admin/dataPeminjaman">
        <i class="fa-solid fa-calendar-check"></i> Data Peminjaman
      </a>
      <!-- CLASS ACTIVE DISINI -->
      <a href="?route=Admin/dataRuangan" class="active">
        <i class="fa-solid fa-door-open"></i> Data Ruangan
      </a>
      <a href="?route=Admin/dataFromAdminCreateBooking">
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

  <!-- KONTEN UTAMA -->
  <div class="main-area">
    
    <!-- HEADER (TOP NAV) - SUDAH DIPERBAIKI -->
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Ruangan</h2>
          <p style="margin:4px 0 0;">Kelola data ruangan.</p>
        </div>
      </div>
      
      <!-- TOMBOL BUAT LAPORAN (MUNCUL SEKARANG) -->
      <div class="header-actions">
        <a href="?route=Admin/exportRuangan" class="btn-laporan">
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

      <div class="section-head" style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 20px;">
        <div>
          <p class="subtitle">Klik tombol aksi untuk booking, lihat feedback, ubah, atau hapus ruangan.</p>
          <a href="?route=Admin/exportRuangan" style="text-decoration: none">
          <button class="btn-add" type="button">Buat Laporan</button>
          </a>
        </div>
        <button class="btn-add" type="button" onclick="window.location='?route=Admin/addRuangan'">Tambah Ruangan</button>
      </div>

      <?php if (empty($rooms)): ?>
        <div class="flash error">Belum ada data ruangan.</div>
      <?php else: ?>
        <!-- Container Card Ruangan -->
        <div class="rooms-container">
          <?php foreach ($rooms as $r): ?>
            <?php
              $img        = 'public/assets/image/contohruangan.png'; // Default image
              // Cek jika ada gambar spesifik dan path-nya valid
              if (!empty($r['gambar']) && file_exists($r['gambar'])) {
                  $img = $r['gambar'];
              } elseif (!empty($r['image_path'])) {
                  $img = $r['image_path'];
              }

              $imgUrl     = preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/');
              $kapasitas  = $r['kapasitas_min'].' - '.$r['kapasitas_max'].' orang';
              $totalBook  = (int)($r['total_booking'] ?? 0);
              $totalFb    = (int)($r['total_feedback'] ?? 0);
              $puas       = (int)($r['puas_percent'] ?? 0);
              $status     = $r['status'] ?? 'Tersedia';
              $statusCls  = (strtolower($status) === 'tersedia') ? 'status-tersedia' : 'status-tidak-tersedia';
            ?>
            
            <div class="room-card">
              <!-- Info Kiri -->
              <div class="room-info">
                <div>
                  <h3>
                    <?= htmlspecialchars($r['nama_ruangan']) ?>
                    <span class="status-chip <?= $statusCls ?>"><?= htmlspecialchars($status) ?></span>
                  </h3>
                </div>
                
                <p><strong>Deskripsi :</strong> <?= htmlspecialchars($r['deskripsi'] ?? 'Belum ada deskripsi.') ?></p>
                <p><strong>Kapasitas :</strong> <?= htmlspecialchars($kapasitas) ?></p>
                <p><strong>Jumlah peminjaman :</strong> <?= $totalBook ?> kali</p>
                <p><strong>Tingkat kepuasan :</strong> <?= $puas ?>%</p>
                <p><strong>Jumlah feedback :</strong> <?= $totalFb ?></p>
              </div>
              
              <!-- Gambar & Aksi Kanan -->
              <div class="room-actions">
                <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($r['nama_ruangan']) ?>">
                
                <div class="action-buttons">
                  <a class="btn-action btn-edit" href="?route=Booking/adminStep1/<?= $r['room_id'] ?>">Booking</a>
                  <a class="btn-action btn-feedback" href="?route=Admin/feedbackRuangan/<?= $r['room_id'] ?>">Lihat Feedback</a>
                  <a class="btn-action btn-edit" href="?route=Admin/editRuangan/<?= $r['room_id'] ?>">Ubah</a>
                  <button type="button"
                          class="btn-action btn-delete js-open-delete"
                          data-id="<?= $r['room_id'] ?>"
                          data-name="<?= htmlspecialchars($r['nama_ruangan'], ENT_QUOTES) ?>">
                    Hapus
                  </button>
                </div>
              </div>
            </div>
            
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal-backdrop" id="modalDelete" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
  <div class="modal-card" style="background: white; padding: 24px; border-radius: 12px; max-width: 400px; width: 90%;">
    <h4 style="margin-top: 0;">Hapus Ruangan?</h4>
    <p id="deleteInfo">Apakah Anda yakin ingin menghapus ruangan ini?</p>
    <form method="POST" action="?route=Admin/deleteRuangan">
      <input type="hidden" name="room_id" id="deleteRoomId">
      <div class="modal-actions" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
        <button type="button" class="btn-pill btn-cancel js-close-modal" style="padding: 8px 20px; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer;">Batal</button>
        <button type="submit" class="btn-pill btn-save" style="padding: 8px 20px; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer;">Ya, hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
const modalDelete = document.getElementById('modalDelete');
const roomIdInput = document.getElementById('deleteRoomId');
const infoText    = document.getElementById('deleteInfo');

document.querySelectorAll('.js-open-delete').forEach(btn => {
  btn.addEventListener('click', () => {
    roomIdInput.value = btn.dataset.id;
    infoText.textContent = `Hapus ruangan "${btn.dataset.name}"? Data yang dihapus tidak bisa dikembalikan.`;
    modalDelete.style.display = 'flex';
  });
});

document.querySelectorAll('.js-close-modal').forEach(btn => {
  btn.addEventListener('click', () => modalDelete.style.display = 'none');
});

modalDelete.addEventListener('click', (e) => {
  if (e.target === modalDelete) modalDelete.style.display = 'none';
});
</script>
</body>
</html>