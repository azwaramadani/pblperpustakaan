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
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css?v=1.7">
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

    <div class="sidebar-footer">
      <img src="public/assets/image/userlogo.png" class="avatar-img" alt="Admin">
      <div class="user-info">
        <span class="name"><?= htmlspecialchars($admin['username'] ?? 'AdminRudy') ?></span>
        <span style="font-size:11px; color:#6b7280;">Administrator</span>
      </div>
    </div>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Ruangan</h2>
          <p style="margin:4px 0 0;">Kelola data ruangan.</p>
        </div>
      </div>
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
        </div>
        <button class="btn-add" type="button" onclick="window.location='?route=Admin/addRuangan'">Tambah Ruangan</button>
      </div>

      <?php if (empty($rooms)): ?>
        <div class="flash error">Belum ada data ruangan.</div>
      <?php else: ?>
        <div class="rooms-container">
          <?php foreach ($rooms as $r): ?>
            <?php
              // Ambil gambar ruangan: gunakan kolom gambar_ruangan jika ada, selain itu fallback ke contohruangan.png
              $imgPath = !empty($r['gambar_ruangan']) ? $r['gambar_ruangan'] : 'public/assets/image/contohruangan.png';
              $imgUrl  = preg_match('#^https?://#i', $imgPath) ? $imgPath : app_config()['base_url'].'/'.ltrim($imgPath,'/');

              $kapasitas  = $r['kapasitas_min'].' - '.$r['kapasitas_max'].' orang';
              $totalBook  = (int)($r['total_booking'] ?? 0);
              $totalFb    = (int)($r['total_feedback'] ?? 0);
              $puas       = (int)($r['puas_percent'] ?? 0);
              $status     = $r['status'] ?? 'Tersedia';
              $statusCls  = (strtolower($status) === 'tersedia') ? 'status-tersedia' : 'status-tidak-tersedia';
            ?>
            
            <div class="room-card">
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
              
              <div class="room-actions">
                <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($r['nama_ruangan']) ?>" style="object-fit:cover;">
                
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

<!-- Modal Hapus Ruangan -->
<div id="modalDelete" class="modal-overlay">
  <div class="modal-content">
    <div class="icon-box-red">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
    </div>
    
    <h2 class="modal-title">Hapus Ruangan?</h2>
    
    <p id="deleteInfo" style="color: #666; margin-bottom: 20px;">Apakah Anda yakin ingin menghapus ruangan ini?</p>
    
    <form method="POST" action="?route=Admin/deleteRuangan">
      <input type="hidden" name="room_id" id="deleteRoomId">
      <div class="modal-actions">
        <button type="button" class="btn-modal-white js-close-modal">Batal</button>
        <button type="submit" class="btn-modal-red">Ya, hapus</button>
      </div>
    </form>
  </div>
</div>
<script>
    // --- SCRIPT MODAL HAPUS ---
    const modalDelete = document.getElementById('modalDelete');
    const roomIdInput = document.getElementById('deleteRoomId');
    const infoText    = document.getElementById('deleteInfo');

    document.querySelectorAll('.js-open-delete').forEach(btn => {
      btn.addEventListener('click', () => {
        roomIdInput.value = btn.dataset.id;
        if(infoText) {
            infoText.textContent = `Hapus ruangan "${btn.dataset.name}"? Data yang dihapus tidak bisa dikembalikan.`;
        }
        modalDelete.classList.add('active');
      });
    });

    document.querySelectorAll('.js-close-modal').forEach(btn => {
      btn.addEventListener('click', () => {
        modalDelete.classList.remove('active');
      });
    });

    modalDelete.addEventListener('click', (e) => {
      if (e.target === modalDelete) {
          modalDelete.classList.remove('active');
      }
    });
</script>
</body>
</html>
