<?php
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Ruangan - Rudy</title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css">
  <style>
    /* Container untuk semua card */
    .rooms-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    /* Card individual untuk setiap ruangan */
    .room-card {
      background: #ffffff;
      border: 1px solid #e0e0e0;
      border-radius: 12px;
      padding: 24px;
      display: flex;
      gap: 24px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
    }

    .room-card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Bagian kiri: Informasi ruangan */
    .room-info {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .room-info h3 {
      font-size: 20px;
      margin: 0 0 8px 0;
      color: #212529;
      font-weight: 600;
    }

    .room-info p {
      margin: 0;
      line-height: 1.6;
      color: #495057;
      font-size: 14px;
    }

    .room-info p strong {
      color: #212529;
      font-weight: 600;
    }

    /* Bagian kanan: Gambar dan tombol aksi */
    .room-actions {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 12px;
      min-width: 280px;
    }

    .room-actions img {
      width: 280px;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
    }

    /* Container tombol */
    .action-buttons {
      display: flex;
      gap: 8px;
      width: 100%;
      justify-content: flex-end;
    }

    /* Styling tombol aksi */
    .btn-action {
      padding: 8px 20px;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .btn-feedback {
      background: #FFC107;
      color: #000;
    }

    .btn-feedback:hover {
      background: #FFB300;
    }

    .btn-edit {
      background: #FFC107;
      color: #000;
    }

    .btn-edit:hover {
      background: #FFB300;
    }

    .btn-delete {
      background: #FFC107;
      color: #000;
    }

    .btn-delete:hover {
      background: #FFB300;
    }

    /* Status chip */
    .status-chip {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 600;
      margin-left: 8px;
    }

    .status-tersedia {
      background: #d4edda;
      color: #155724;
    }

    .status-tidak-tersedia {
      background: #f8d7da;
      color: #721c24;
    }

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
      .room-card {
        flex-direction: column;
      }

      .room-actions {
        min-width: 100%;
        align-items: stretch;
      }

      .room-actions img {
        width: 100%;
      }

      .action-buttons {
        justify-content: stretch;
        flex-wrap: wrap;
      }

      .btn-action {
        flex: 1;
        min-width: 100px;
      }
    }
  </style>
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
      <a href="?route=Admin/dataRuangan" class="active">Data Ruangan</a>
      <a href="?route=Admin/dataAkun">Data Akun</a>
      <a href="?route=Auth/logout">Keluar</a>
    </nav>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Ruangan</h2>
          <p style="margin:4px 0 0;">Kelola data ruangan, feedback, serta aksi ubah/hapus.</p>
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

      <div class="section-head" style="align-items:center; justify-content:space-between; margin-bottom: 20px;">
        <div>
          <h3>Daftar Ruangan</h3>
          <p class="subtitle">Klik tombol aksi untuk lihat feedback, ubah, atau hapus ruangan.</p>
        </div>
        <button class="btn-add" type="button" onclick="window.location='?route=Admin/addRuangan'">Tambah Ruangan</button>
      </div>

      <?php if (empty($rooms)): ?>
        <div class="flash error">Belum ada data ruangan.</div>
      <?php else: ?>
        <!-- Container untuk semua card -->
        <div class="rooms-container">
          <?php foreach ($rooms as $r): ?>
            <?php
              $img        = 'public/assets/image/contohruangan.png';
              $imgUrl     = preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/');
              $kapasitas  = $r['kapasitas_min'].' - '.$r['kapasitas_max'].' orang';
              $totalBook  = (int)($r['total_booking'] ?? 0);
              $totalFb    = (int)($r['total_feedback'] ?? 0);
              $puas       = (int)($r['puas_percent'] ?? 0);
              $status     = $r['status'] ?? 'Tersedia';
              $statusCls  = (strtolower($status) === 'tersedia') ? 'status-tersedia' : 'status-tidak-tersedia';
            ?>
            
            <!-- CARD INDIVIDUAL UNTUK SETIAP RUANGAN -->
            <div class="room-card">
              <!-- BAGIAN KIRI: INFORMASI -->
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
              
              <!-- BAGIAN KANAN: GAMBAR DAN TOMBOL AKSI -->
              <div class="room-actions">
                <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($r['nama_ruangan']) ?>">
                
                <div class="action-buttons">
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
            <!-- END CARD -->
            
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal-backdrop" id="modalDelete" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
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
// Script sederhana untuk buka/tutup modal konfirmasi hapus
const modalDelete = document.getElementById('modalDelete');
const roomIdInput = document.getElementById('deleteRoomId');
const infoText   = document.getElementById('deleteInfo');

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