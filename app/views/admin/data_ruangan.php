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

      <div class="section-head" style="align-items:center; justify-content:space-between;">
        <div>
          <h3>Daftar Ruangan</h3>
          <p class="subtitle">Klik tombol aksi untuk lihat feedback, ubah, atau hapus ruangan.</p>
        </div>
        <button class="btn-add" type="button" onclick="window.location='?route=Admin/addRuangan'">Tambah Ruangan</button>
      </div>

      <?php if (empty($rooms)): ?>
        <div class="flash error">Belum ada data ruangan.</div>
      <?php else: ?>
        <?php foreach ($rooms as $r): ?>
          <?php
            $img        = 'public/assets/image/contohruangan.png';
            $imgUrl     = preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/');
            $kapasitas  = $r['kapasitas_min'].' - '.$r['kapasitas_max'].' orang';
            $totalBook  = (int)($r['total_booking'] ?? 0);
            $totalFb    = (int)($r['total_feedback'] ?? 0);
            $puas       = (int)($r['puas_percent'] ?? 0);
            $status     = $r['status'] ?? 'Tersedia';
            $statusCls  = (strtolower($status) === 'tersedia') ? 'status-disetujui' : 'status-ditolak';
          ?>
          <div class="adminroom-card">
            <div class="adminroom-info">
              <div style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                <h3 style="margin:0;"><?= htmlspecialchars($r['nama_ruangan']) ?></h3>
                <span class="adminstatus-chip <?= $statusCls ?>"><?= htmlspecialchars($status) ?></span>
              </div>
              <p><strong>Deskripsi:</strong> <?= htmlspecialchars($r['deskripsi'] ?? 'Belum ada deskripsi.') ?></p>
              <p><strong>Kapasitas:</strong> <?= htmlspecialchars($kapasitas) ?></p>
              <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">
                <div class="panel" style="padding:10px 14px; min-width:140px;">
                  <p style="margin:0; font-size:12px; color:#777;">Total Peminjaman</p>
                  <strong><?= $totalBook ?> kali</strong>
                </div>
                <div class="panel" style="padding:10px 14px; min-width:140px;">
                  <p style="margin:0; font-size:12px; color:#777;">Jumlah Feedback</p>
                  <strong><?= $totalFb ?> ulasan</strong>
                </div>
                <div class="panel" style="padding:10px 14px; min-width:140px;">
                  <p style="margin:0; font-size:12px; color:#777;">Tingkat Kepuasan</p>
                  <strong><?= $puas ?>%</strong>
                </div>
              </div>
            </div>
            <div class="adminroom-actions">
              <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($r['nama_ruangan']) ?>">
              <a class="btn-rect" href="?route=Admin/feedbackRuangan/<?= $r['room_id'] ?>">Lihat Feedback</a>
              <a class="btn-rect" href="?route=Admin/editRuangan/<?= $r['room_id'] ?>">Ubah</a>
              <button type="button"
                      class="btn-rect js-open-delete"
                      data-id="<?= $r['room_id'] ?>"
                      data-name="<?= htmlspecialchars($r['nama_ruangan'], ENT_QUOTES) ?>">
                Hapus
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </main>
  </div>
</div>

<!-- Modal konfirmasi hapus -->
<div class="modal-backdrop" id="modalDelete">
  <div class="modal-card">
    <h4>Hapus Ruangan?</h4>
    <p id="deleteInfo">Apakah Anda yakin ingin menghapus ruangan ini?</p>
    <form method="POST" action="?route=Admin/deleteRuangan">
      <input type="hidden" name="room_id" id="deleteRoomId">
      <div class="modal-actions">
        <button type="submit" class="btn-pill btn-save">Ya, hapus</button>
        <button type="button" class="btn-pill btn-cancel js-close-modal">Batal</button>
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
