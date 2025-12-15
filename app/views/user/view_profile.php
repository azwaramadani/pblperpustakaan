<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil | Rudy Ruang Study</title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/styleprofile.css?v=<?= time() ?>">
</head>
<body>
<header class="navbar">
  <div class="logo">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40" alt="Logo PNJ">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40" alt="Logo Rudy">
  </div>

  <nav class="nav-menu">
    <a href="?route=User/home">Beranda</a>
    <a href="?route=User/ruangan">Ruangan</a>
    <a href="?route=User/riwayat">Riwayat</a>
  </nav>

  <div class="profile-dropdown" id="profile-dropdown">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
      <div class="user-name"><a href="?route=User/viewProfile"><p><?= htmlspecialchars($user['nama']) ?></p></a></div>
    <div class="profile-card">
      <p><strong><?= htmlspecialchars($user['nama'] ?? '') ?></strong></p>
      <p><strong><?= htmlspecialchars($user['role'] ?? '') ?></strong></p>
      <p><?= htmlspecialchars($user['unit'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['jurusan'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['program_studi'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['nim_nip'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['no_hp'] ?? '') ?></p>
      <p><?= htmlspecialchars($user['email'] ?? '') ?></p>
      <a class="btn-logout" href="?route=Auth/logout">Keluar</a>
    </div>
  </div>
</header>

<main class="page">
  <section class="profile-hero">
    <h1 class="title">Profil</h1>
    <div class="avatar-wrap">
      <!-- Foto default sesuai permintaan -->
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="Foto Profil">
    </div>
    <p class="user-fullname"><?= htmlspecialchars($user['nama'] ?? '-') ?></p>
  </section>

  <section class="profile-card-big">
    <div class="info-row">
      <span class="label">Nama</span>
      <span class="value"><?= htmlspecialchars($user['nama'] ?? '-') ?></span>
    </div>
    <div class="info-row">
      <span class="label">Role</span>
      <span class="value"><?= htmlspecialchars($user['role'] ?? '-') ?></span>
    </div>
    <div class="info-row">
      <span class="label">Unit</span>
      <span class="value"><?= htmlspecialchars($user['unit'] ?? '-') ?></span>
    </div>
    <div class="info-row">
      <span class="label">Jurusan</span>
      <span class="value"><?= htmlspecialchars($user['jurusan'] ?? '-') ?></span>
    </div>
    <div class="info-row">
      <span class="label">Program Studi</span>
      <span class="value"><?= htmlspecialchars($user['program_studi'] ?? '-') ?></span>
    </div>
    <div class="info-row">
      <span class="label">NIM / NIP</span>
      <span class="value"><?= htmlspecialchars($user['nim_nip'] ?? '-') ?></span>
    </div>
    <div class="info-row">
      <span class="label">No. HP</span>
      <span class="value"><?= htmlspecialchars($user['no_hp'] ?? '-') ?></span>
    </div>
    <div class="info-row">
      <span class="label">Email</span>
      <span class="value"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
    </div>

    <a class="btn-exit" href="?route=Auth/logout">Keluar</a>
  </section>
</main>

<footer class="footer">
  <div class="footer-brand">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy Ruang Study" class="footer-logo">
    <p class="footer-description">
      Rudi Ruangan Studi adalah platform peminjaman ruangan perpustakaan yang membantu mahasiswa dan staf mengatur penggunaan ruang belajar dengan mudah dan efisien.
    </p>
  </div>

  <div class="footer-nav">
    <div>
      <h4>Navigasi</h4>
      <a href="?route=User/home">Beranda</a>
      <a href="?route=User/ruangan">Daftar Ruangan</a>
      <a href="?route=User/riwayat">Panduan</a>
      <a href="?route=User/home">Masuk</a>
    </div>

    <div>
      <h4>Bantuan</h4>
      <a href="#">FAQ</a>
      <a href="#">Aturan ruangan</a>
    </div>

    <div>
      <h4>Kontak</h4>
      <a href="mailto:PerpusPNJ@email.com">PerpusPNJ@email.com</a>
      <a href="tel:0822123456780">0822123456780</a>
      <p>Kampus PNJ, Depok</p>
    </div>
  </div>
</footer>

</body>
</html>
