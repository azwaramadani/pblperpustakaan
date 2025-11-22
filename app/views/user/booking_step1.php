<?php
$user = $data['user'] ?? [
  'nama'   => Session::get('nama') ?? '',
  'nim_nip'=> Session::get('nim_nip') ?? '',
  'no_hp'  => Session::get('no_hp') ?? '',
  'email'  => Session::get('email') ?? '',
];
$room = $data['room'] ?? [];
$puasPercent = $data['puas_percent'] ?? 0;
$badgeText   = $puasPercent > 0 ? $puasPercent . '% Orang Puas' : 'Belum ada feedback';
$err  = Session::get('flash_error');
Session::set('flash_error', null);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pilih Tanggal & Jam - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleriwayat.css">
</head>
<body>
  <header class="navbar">
    <div class="logo">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40">
    </div>
    <nav class="nav-menu">
      <a href="?route=User/home">Beranda</a>
      <a href="?route=User/ruangan" class="active">Ruangan</a>
      <a href="?route=User/riwayat">Riwayat</a>
    </nav>
    <div class="profile-dropdown">
      <div class="profile-trigger">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
        <div class="user-name"><p><?= htmlspecialchars($user['nama']) ?></p></div>
      </div>
      <div class="profile-card">
        <p><strong><?= htmlspecialchars($user['nama']) ?></strong></p>        
        <p><?= htmlspecialchars($data['user']['role']) ?></p>
        <p><?= htmlspecialchars($user['nim_nip']) ?></p>
        <p><?= htmlspecialchars($user['no_hp']) ?></p>
        <p><?= htmlspecialchars($user['email']) ?></p>
        <a class="btn-logout" href="?route=Auth/logout">Keluar</a>
      </div>
    </div>
  </header>

  <main style="max-width:900px;margin:40px auto;">
    <div style="display:flex;gap:24px;align-items:center;">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="Ruangan" style="width:250px;border-radius:10px;">
      <div>
        <h2><?= htmlspecialchars($room['nama_ruangan']) ?></h2>
        <p><?= htmlspecialchars($room['deskripsi'] ?? 'Ruangan study') ?></p>
        <p>Kapasitas: <?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</p>
      </div>
      <div style="margin:12px 0;">
        <span class="puas-badge"><?= htmlspecialchars($badgeText) ?></span>
      </div>
    </div>

    <?php if ($err): ?>
      <div style="color:red;margin-top:16px;"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <div style="margin-top:24px;padding:24px;border:1px solid #ddd;border-radius:12px;background:#fff;">
      <h3>Pilih tanggal dan jam peminjaman</h3>
      <p>Batas Maksimal Waktu Peminjaman adalah 15:00 WIB.</p>
      <form action="?route=Booking/step2" method="POST" style="display:grid;gap:16px;max-width:420px;">
        <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
        <label>Tanggal
          <input type="date" name="tanggal" required>
        </label>
        <div style="display:flex;gap:12px;align-items:center;">
          <label style="flex:1;">Jam Mulai
            <input type="time" name="jam_mulai" required>
          </label>
          <span>Sampai</span>
          <label style="flex:1;">Jam Selesai
            <input type="time" name="jam_selesai" required>
          </label>
        </div>
        <div style="display:flex;gap:12px;">
          <a href="?route=User/ruangan" class="btn" style="flex:1;text-align:center;background:#0d9488;color:#fff;padding:10px 0;border-radius:6px;text-decoration:none;">Kembali</a>
          <button type="submit" style="flex:1;background:#f6c200;border:0;padding:10px 0;border-radius:6px;cursor:pointer;">Lanjut</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
