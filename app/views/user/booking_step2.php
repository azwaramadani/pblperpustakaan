<?php
$user    = $data['user'] ?? [
  'nama'   => Session::get('nama') ?? '',
  'nim_nip'=> Session::get('nim_nip') ?? '',
  'no_hp'  => Session::get('no_hp') ?? '',
  'email'  => Session::get('email') ?? '',
];
$room    = $data['room'];
$payload = $data['payload'];
$err     = Session::get('flash_error');
Session::set('flash_error', null);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lengkapi Data Peminjaman - <?= htmlspecialchars($room['nama_ruangan']) ?></title>
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
    <div class="profile">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
      <div class="user-name"><p><?= htmlspecialchars($user['nama']) ?></p></div>
    </div>
  </header>

  <main style="max-width:900px;margin:40px auto;">
    <div style="display:flex;gap:24px;align-items:center;">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="Ruangan" style="width:250px;border-radius:10px;">
      <div>
        <h2><?= htmlspecialchars($room['nama_ruangan']) ?></h2>
        <p><?= htmlspecialchars($room['deskripsi'] ?? 'Ruangan study') ?></p>
        <p>Kapasitas: <?= htmlspecialchars($room['kapasitas_min']) ?> - <?= htmlspecialchars($room['kapasitas_max']) ?> orang</p>
        <p><strong><?= htmlspecialchars($payload['tanggal']) ?></strong> (<?= htmlspecialchars($payload['jam_mulai']) ?> - <?= htmlspecialchars($payload['jam_selesai']) ?>)</p>
      </div>
    </div>

    <?php if ($err): ?>
      <div style="color:red;margin-top:16px;"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <div style="margin-top:24px;padding:24px;border:1px solid #ddd;border-radius:12px;background:#fff;">
      <h3>Lengkapi Data Peminjaman</h3>
      <form action="?route=Booking/store" method="POST" style="display:grid;gap:16px;max-width:480px;">
        <!-- Hidden bawaan step1 -->
        <input type="hidden" name="room_id" value="<?= htmlspecialchars($payload['room_id']) ?>">
        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($payload['tanggal']) ?>">
        <input type="hidden" name="jam_mulai" value="<?= htmlspecialchars($payload['jam_mulai']) ?>">
        <input type="hidden" name="jam_selesai" value="<?= htmlspecialchars($payload['jam_selesai']) ?>">

        <label>Jumlah Mahasiswa
          <input type="number" name="jumlah_peminjam" min="1" value="1" required>
        </label>

        <label>Nama penanggung jawab
          <input type="text" name="nama_penanggung_jawab" value="<?= htmlspecialchars($user['nama']) ?>" required>
        </label>

        <label>NIM/NIP penanggung jawab
          <input type="text" name="nimnip_penanggung_jawab" value="<?= htmlspecialchars($user['nim_nip']) ?>" required>
        </label>

        <label>Email penanggung jawab
          <input type="email" name="email_penanggung_jawab" required>
        </label>

        <label>NIM/NIP peminjam ruangan (anggota)
          <input type="text" name="nimnip_peminjam" required>
        </label>

        <div style="display:flex;gap:12px;">
          <a href="?route=Booking/step1/<?= urlencode($payload['room_id']) ?>" class="btn" style="flex:1;text-align:center;background:#0d9488;color:#fff;padding:10px 0;border-radius:6px;text-decoration:none;">Kembali</a>
          <button type="submit" style="flex:1;background:#f6c200;border:0;padding:10px 0;border-radius:6px;cursor:pointer;">Simpan</button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
