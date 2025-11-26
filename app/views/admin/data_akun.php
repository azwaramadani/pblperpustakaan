<?php
$users = $users ?? [];
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Akun User - Rudy</title>
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
      <a href="?route=Admin/dataAkun" class="active">Data Akun</a>
      <a href="?route=Auth/logout">Keluar</a>
    </nav>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Akun User</h2>
          <p class="subtitle">Data akun user aplikasi RUDY</p>
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
            <h3>Data Akun User</h3>
            <p class="subtitle">Urut dari pembuatan akun terbaru.</p>
          </div>
        </div>  
        <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Role</th>
              <th>Jurusan</th>
              <th>Program Studi</th>
              <th>NIM/NIP</th>
              <th>Nama</th>
              <th>No HP</th>
              <th>Email</th>
              <th>Bukti Aktivasi</th>
              <th>Waktu Dibuat</th>
              <th>Status Akun</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr><td colspan="10" style="text-align:center;">Belum ada data user.</td></tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <?php
                  $img = $u['bukti_aktivasi'] ?: '';
                  $imgUrl = $img ? (preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/')) : '';
                  $statusKey = strtolower($u['status_akun']);
                ?>
                <tr>
                  <td><?= htmlspecialchars($u['role'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($u['jurusan'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($u['program_studi']) ?? '-'?></td>
                  <td><?= htmlspecialchars($u['nim_nip'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($u['nama'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($u['no_hp'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                  <td>
                    <?php if ($imgUrl): ?>
                      <a href="<?= $imgUrl ?>" target="_blank" rel="noopener">
                        <img src="<?= $imgUrl ?>" alt="Bukti" class="img-thumb">
                      </a>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td><?= $u['created_at'] ? date('d M Y H:i', strtotime($u['created_at'])) : '-' ?></td>
                  <td>
                    <span class="status-chip status-<?= $statusKey ?>"><?= htmlspecialchars($u['status_akun']) ?></span>
                  </td>
                  <td>
                    <button class="aksi-btn js-open-modal"
                            data-id="<?= $u['user_id'] ?>"
                            data-status="<?= htmlspecialchars($u['status_akun']) ?>">
                      Ubah Status
                    </button>
                    <form method="POST" action="?route=Admin/deleteUser" style="display:inline;" onsubmit="return confirm('Hapus akun ini?');">
                      <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                      <button type="submit" class="aksi-btn danger">Hapus Akun</button>
                    </form>
                  </td>
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

<div class="modal-backdrop" id="modalStatus">
  <div class="modal-card">
    <h4>Ubah Status Akun</h4>
    <form method="POST" action="?route=Admin/updateUserStatus" id="statusForm">
      <input type="hidden" name="user_id" id="userIdInput">
      <div class="radio-row">
        <label><input type="radio" name="status_akun" value="Disetujui"> Disetujui</label>
        <label><input type="radio" name="status_akun" value="Ditolak"> Ditolak</label>
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
  const idInput = document.getElementById('userIdInput');
  const radios = document.querySelectorAll('input[name="status_akun"]');

  document.querySelectorAll('.js-open-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const status = btn.dataset.status || '';
      idInput.value = id;
      radios.forEach(r => { r.checked = (r.value === status); });
      modal.style.display = 'flex';
    });
  });

  document.querySelectorAll('.js-close-modal').forEach(btn => {
    btn.addEventListener('click', () => modal.style.display = 'none');
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
  });
</script>
</body>
</html>