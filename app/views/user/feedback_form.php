<?php
$user     = $data['user'];
$booking  = $data['booking'];
$feedback = $data['feedback']; // null jika belum ada
$err      = Session::get('flash_error');
Session::set('flash_error', null);
$success  = Session::get('flash_success');
Session::set('flash_success', null);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Feedback - <?= htmlspecialchars($booking['kode_booking']) ?></title>
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
    <a href="?route=User/ruangan">Ruangan</a>
    <a href="?route=User/riwayat" class="active">Riwayat</a>
  </nav>
  <div class="profile-dropdown">
    <div class="profile-trigger">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
      <div class="user-name"><p><?= htmlspecialchars($user['nama']) ?></p></div>
    </div>
    <div class="profile-card">
      <p><strong><?= htmlspecialchars($user['nama']) ?></strong></p>
      <p><?= htmlspecialchars($user['nim_nip']) ?></p>
      <p><?= htmlspecialchars($user['no_hp']) ?></p>
      <p><?= htmlspecialchars($user['email']) ?></p>
      <a class="btn-logout" href="?route=Auth/logout">Keluar</a>
    </div>
  </div>
</header>

<main>
  <section class="title-section" style="text-align:center; margin:30px 0;">
    <h2 class="title">Beri Feedback</h2>
    <p class="subtitle">Kode Booking: <?= htmlspecialchars($booking['kode_booking']) ?></p>
  </section>

  <div class="feedback-wrap">
    <?php if ($err): ?><div class="alert error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <p><strong>Ruangan:</strong> <?= htmlspecialchars($booking['room_id']) ?> | <strong>Tanggal:</strong> <?= htmlspecialchars($booking['tanggal']) ?> | <strong>Jam:</strong> <?= htmlspecialchars($booking['jam_mulai']) ?> - <?= htmlspecialchars($booking['jam_selesai']) ?></p>

    <?php if ($feedback): ?>
      <?php $statusText = !empty($feedback['puas']) ? 'Puas' : 'Tidak Puas'; ?>
        <p><strong>Feedback Anda (sudah terkirim):</strong></p>
        <p>Status: <?= htmlspecialchars($statusText) ?></p>
        <p><?= nl2br(htmlspecialchars($feedback['komentar'])) ?></p>
    <?php else: ?>
      <form action="?route=Feedback/store" method="POST">
        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>">

        <p><strong>Apakah Anda puas?</strong></p>
        <div style="margin-bottom:14px;">
          <label>
            <input type="radio" name="rating" value="Puas" checked> Puas
          </label>
          <label style="margin-left:12px;">
            <input type="radio" name="rating" value="Tidak Puas"> Tidak Puas
          </label>
        </div>

        <p><strong>Ada saran atau masukan?</strong></p>
        <textarea class="textarea" name="komentar" placeholder="Tulis feedback di sini..." required></textarea>

        <div style="margin-top:16px;">
          <button type="submit" class="btn-submit">Simpan</button>
          <a href="?route=User/riwayat" class="btn" style="margin-left:8px; text-decoration:none; padding:10px 16px; border-radius:10px; background:#e0e0e0; color:#000;">Kembali</a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
