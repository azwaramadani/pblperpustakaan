<?php
$adminName   = $admin['username'] ?? ($admin['nama'] ?? 'Admin');
//pagination
$pagination  = $pagination ?? ['page'=>1, 'total_pages'=>1, 'limit'=>10, 'total'=>count($users)];

//buat hitung informasi kayak (menampilkan 1-... data dari ... data) 
$perPage     = (int)($pagination['limit'] ?? 10);
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages  = max(1, (int)($pagination['total_pages'] ?? 1));
$totalRows   = (int)($pagination['total'] ?? count($users));

$startRow = $totalRows ? (($currentPage - 1) * $perPage + 1) : 0;
$endRow   = $totalRows ? min($startRow + $perPage - 1, $totalRows) : 0;

// Susun query string supaya tombol halaman tetap membawa filter yang dipilih
$queryParams                 = $_GET ?? [];
$queryParams['route']        = 'Admin/dataAkun';
unset($queryParams['page']); // page dipasang ulang sesuai tombol yang diklik
$baseQuery                   = http_build_query($queryParams);
$baseQuery                   = $baseQuery ? ($baseQuery . '&') : 'route=Admin/dataAkun&';

// Tentukan range nomor halaman yang ditampilin
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
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleadmin.css?v=1.9">
  <!-- FontAwesome Regular & Solid -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Style Khusus untuk Modal Hapus Baru -->
  <style>
    .modal-delete-custom {
      text-align: center;
      max-width: 340px !important; /* Ukuran lebih kecil sesuai desain */
      border-radius: 24px !important;
      padding: 32px 24px !important;
    }
    .modal-delete-icon {
      font-size: 48px;
      color: #ff4d4f; /* Warna merah icon */
      margin-bottom: 20px;
      display: inline-block;
      padding: 10px;
      border: 2px solid #fff; /* Opsional: memberi kesan rapi */
    }
    .modal-delete-title {
      font-size: 18px;
      font-weight: 700;
      color: #111827;
      margin: 0 0 24px 0;
      line-height: 1.4;
    }
    .btn-delete-confirm {
      width: 100%;
      background: #ff4d4f;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 15px;
      margin-bottom: 12px;
      cursor: pointer;
      transition: background 0.2s;
      box-shadow: 0 4px 10px rgba(255, 77, 79, 0.2);
    }
    .btn-delete-confirm:hover {
      background: #e04345;
    }
    .btn-delete-cancel {
      width: 100%;
      background: #ffffff;
      color: #374151;
      border: 1px solid #d1d5db;
      padding: 12px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn-delete-cancel:hover {
      background: #f9fafb;
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
      <a href="?route=Admin/dashboard">
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
      <a href="?route=Admin/dataAkun" class="active">
        <i class="fa-solid fa-users"></i> Data Akun
      </a>
      <a href="?route=Auth/logout" style="color: var(--danger) !important;">
        <i class="fa-solid fa-right-from-bracket" style="color: var(--danger) !important;"></i> Keluar
      </a>
    </nav>

    <div class="sidebar-footer">
      <img src="public/assets/image/userlogo.png" class="avatar-img" alt="Admin">
      <div class="user-info">
        <span class="name"><?=  htmlspecialchars($admin['username']) ?></span>
        <span style="font-size:11px; color:#6b7280;">Administrator</span>
      </div>
    </div>
  </aside>

  <div class="main-area">
    <header class="top-nav">
      <div class="nav-brand">
        <div>
          <h2 style="margin:0;">Data Akun User</h2>
          <p style="margin:4px 0 0;">Data akun user aplikasi RUDY</p>
        </div>
      </div>
      <div class="header-actions">
        <a href="?route=Admin/exportakun" class="btn-laporan">
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
    
      <!-- TABEL 1: USER VALIDASI -->
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Akun User Validasi</h3>
            <p class="subtitle">Data semua akun user yang harus divalidasi.</p>
          </div>
        </div>

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

          <label>Status Akun</label>
          <select name="status_akun">
            <option value="">Semua</option>
            <?php foreach ($statusakunList as $stl): ?>
              <option value="<?= htmlspecialchars($stl) ?>" <?= ($filters['status_akun']===$stl?'selected':'') ?>><?= htmlspecialchars($stl) ?></option>
            <?php endforeach; ?>
          </select>
          
          <div class="search-bar" style="margin-left:auto;">
            <input
              type="text"
              name="keyword"
              placeholder="Cari nama atau NIM/NIP..."
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
                <th>No</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Unit</th>
                <th>Jurusan</th>
                <th>Program Studi</th>
                <th>NIM/NIP</th>
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
                <tr><td colspan="13" style="text-align:center; padding:20px;">Semua user sudah divalidasi.</td></tr>
              <?php else: ?>
                <?php $rowNumber = $startRow ?: 1; ?>
                <?php foreach ($userregist as $ur): ?>
                  <?php
                    $img = $ur['bukti_aktivasi'] ?: '';
                    $imgUrl = $img ? (preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/')) : '';
                    $statusKey = strtolower($ur['status_akun']);
                  ?>
                  <tr>
                    <td><?= $rowNumber++ ?></td>
                    <td><?= htmlspecialchars($ur['nama'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['role'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['unit'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['jurusan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ur['program_studi'] ?? '-')?></td>
                    <td><?= htmlspecialchars($ur['nim_nip'] ?? '-') ?></td>
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
                      <!-- BUTTON UBAH STATUS -->
                      <button class="aksi-btn js-open-modal"
                              data-id="<?= $ur['user_id'] ?>"
                              data-status="<?= htmlspecialchars($ur['status_akun']) ?>">
                        Ubah Status
                      </button>
                      
                      <!-- BUTTON HAPUS (MODIFIED) -->
                      <button class="aksi-btn danger js-open-delete" 
                              data-id="<?= $ur['user_id'] ?>">
                          Hapus Akun
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
  
      <!-- TABEL 2: SEMUA USER -->
      <section class="panel">
        <div class="section-head">
          <div>
            <h3>Daftar Semua User</h3>
            <p class="subtitle">Data lengkap seluruh akun user terdaftar.</p>
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
              placeholder="Cari nama atau NIM/NIP akun..."
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
                <th>No</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Unit</th>
                <th>Jurusan</th>
                <th>Program Studi</th>
                <th>NIM/NIP</th>
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
                <tr><td colspan="13" style="text-align:center; padding:20px;">Belum ada data user.</td></tr>
              <?php else: ?>
                <?php $rowNumber = $startRow ?: 1; ?>
                <?php foreach ($users as $u): ?>
                  <?php
                    $img = $u['bukti_aktivasi'] ?: '';
                    $imgUrl = $img ? (preg_match('#^https?://#i', $img) ? $img : app_config()['base_url'].'/'.ltrim($img,'/')) : '';
                    $statusKey = strtolower($u['status_akun']);
                  ?>
                  <tr>
                    <td><?= $rowNumber++ ?></td>
                    <td><?= htmlspecialchars($u['nama'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['role'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['unit'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['jurusan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['program_studi'] ?? '-')?></td>
                    <td><?= htmlspecialchars($u['nim_nip'] ?? '-') ?></td>
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
                      <!-- BUTTON UBAH STATUS -->
                      <button class="aksi-btn js-open-modal"
                              data-id="<?= $u['user_id'] ?>"
                              data-status="<?= htmlspecialchars($u['status_akun']) ?>">
                        Ubah Status
                      </button>
                      
                      <!-- BUTTON HAPUS (MODIFIED) -->
                      <button class="aksi-btn danger js-open-delete" 
                              data-id="<?= $u['user_id'] ?>">
                          Hapus Akun
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

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
  </div>
</div>

<!-- Modal Ubah Status (Tetap Sama) -->
<div class="modal-backdrop" id="modalStatus">
  <div class="modal-card">
    <h4 style="margin-top:0;">Ubah Status Akun</h4>
    <form method="POST" action="?route=Admin/updateUserStatus" id="statusForm">
      <input type="hidden" name="user_id" id="userIdInput">
      <div class="radio-row" style="margin:20px 0;">
        <label><input type="radio" name="status_akun" value="Disetujui"> Disetujui</label>
        <label><input type="radio" name="status_akun" value="Ditolak"> Ditolak</label>
      </div>
      <div class="modal-actions" style="text-align:right;">
        <button type="button" class="btn-pill btn-cancel js-close-modal" style="margin-right:10px;">Batal</button>
        <button type="submit" class="btn-pill btn-save">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL HAPUS AKUN (DESAIN BARU) -->
<div class="modal-backdrop" id="modalDelete">
  <div class="modal-card modal-delete-custom">
    
    <!-- Icon Tong Sampah -->
    <div class="modal-delete-icon">
        <i class="fa-regular fa-trash-can"></i>
    </div>

    <!-- Judul -->
    <h4 class="modal-delete-title">
        Apakah anda yakin<br>ingin menghapus?
    </h4>

    <form method="POST" action="?route=Admin/deleteUser" id="deleteForm">
      <input type="hidden" name="user_id" id="deleteUserIdInput">
      
      <!-- Tombol Hapus (Merah) -->
      <button type="submit" class="btn-delete-confirm">
        Hapus
      </button>

      <!-- Tombol Batal (Putih/Border) -->
      <button type="button" class="btn-delete-cancel js-close-delete">
        Batal
      </button>
    </form>
  </div>
</div>

<script>
  // Script Modal Ubah Status
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

  // Script Modal Hapus Akun
  const modalDelete = document.getElementById('modalDelete');
  const deleteIdInput = document.getElementById('deleteUserIdInput');

  document.querySelectorAll('.js-open-delete').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      deleteIdInput.value = id;
      modalDelete.style.display = 'flex';
    });
  });

  document.querySelectorAll('.js-close-delete').forEach(btn => {
    btn.addEventListener('click', () => modalDelete.style.display = 'none');
  });

  modalDelete.addEventListener('click', (e) => {
    if (e.target === modalDelete) modalDelete.style.display = 'none';
  });
</script>
</body>
</html>