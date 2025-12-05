<?php
$adminName   = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
$filters     = $filters ?? ['sort_date'=>'desc', 'from_date'=>'','to_date'=>'', 'role'=> '', 'unit'=> '', 'jurusan'=>'', 'program_studi'=>'', 'keyword'=>''];
$users       = $users ?? [];
$userregist  = $userregist ?? [];
$roleList    = $roleList ?? [];
$unitList    = $unitList ?? [];
$jurusanList = $jurusanList ?? [];
$prodiList   = $prodiList ?? [];

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
$queryParams['route']        = 'Admin/dataAkun';
unset($queryParams['page']); // page dipasang ulang sesuai tombol yang diklik
$baseQuery                   = http_build_query($queryParams);
$baseQuery                   = $baseQuery ? ($baseQuery . '&') : 'route=Admin/dataAkun&';

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
            <h3>Data Akun User Validasi</h3>
            <p class="subtitle">Data akun user yang harus divalidasi.</p>
          </div>
        </div>

        <!-- Filter/sort dan search-->
        <form class="filter-bar" method="GET" action="">
          <input type="hidden" name="route" value="Admin/dataAkun">

          <label>Urut tanggal</label>
          <select name="sort_date">
            <option value="desc" <?= ($filters['sort_date'] === 'desc') ? 'selected' : '' ?>>Terbaru &uarr;</option>
            <option value="asc"  <?= ($filters['sort_date'] === 'asc')  ? 'selected' : '' ?>>Terlama &darr;</option>
          </select>

          <label>Dari</label>
          <input type="date" name="from_date" value="<?= htmlspecialchars($filters['from_date']) ?>">

          <label>Sampai</label>
          <input type="date" name="to_date" value="<?= htmlspecialchars($filters['to_date']) ?>">

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
          <br>
          
          <div class="search-bar">
            <input
              type="text"
              name="keyword"
              placeholder="Cari nama user..."
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
          <a class="btn-reset" href="?route=Admin/dataakun">Reset</a>
        </form>

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
              <?php if (empty($userregist)): ?>
                <tr><td colspan="10" style="text-align:center;">user sudah divalidasi semua.</td></tr>
              <?php else: ?>
                <?php foreach ($userregist as $ur): ?>
                  <?php
                    $img = $ur['bukti_aktivasi'] ?: '';
                    $imgUrl = $img ? (preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/')) : '';
                    $statusKey = strtolower($ur['status_akun']);
                  ?>
                  <tr>
                    <td><?= htmlspecialchars($ur['role'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['jurusan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['program_studi'] ?? '-')?></td>
                    <td><?= htmlspecialchars($ur['nim_nip'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['nama'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['no_hp'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['email'] ?? '-') ?></td>
                    <td>
                      <?php if ($imgUrl): ?>
                        <a href="<?= $imgUrl ?>" target="_blank" rel="noopener">
                          <img src="<?= $imgUrl ?>" alt="Bukti" class="img-thumb">
                        </a>
                      <?php else: ?>
                      -
                      <?php endif; ?>
                    </td>
                    <td><?= $ur['created_at'] ? date('d M Y H:i', strtotime($ur['created_at'])) : '-' ?></td>
                    <td>
                      <span class="status-chip status-<?= $statusKey ?>"><?= htmlspecialchars($ur['status_akun']) ?></span>
                    </td>
                    <td>
                      <button class="aksi-btn js-open-modal"
                              data-id="<?= $ur['user_id'] ?>"
                              data-status="<?= htmlspecialchars($ur['status_akun']) ?>">
                        Ubah Status
                      </button>
                      <form method="POST" action="?route=Admin/deleteUser" style="display:inline;" onsubmit="return confirm('Hapus akun ini?');">
                        <input type="hidden" name="user_id" value="<?= $ur['user_id'] ?>">
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
  
    <section class="panel">
        <div class="section-head">
          <div>
            <h3>Data Semua Akun User</h3>
            <p class="subtitle">Data Semua Akun User.</p>
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
                  <td><?= htmlspecialchars($u['program_studi'] ?? '-')?></td>
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