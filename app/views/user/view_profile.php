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

  <div class="profile-dropdown">
      <div class="profile-trigger"> 
          <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
          <div class="user-name"><a href="?route=User/viewProfile"><p><?= htmlspecialchars($user['nama']) ?></p></a></div>
      </div>
      <div class="profile-card">
        <p><strong><?= htmlspecialchars($user['nama']) ?></strong></p>
        <p><strong><?= htmlspecialchars($user['role']) ?></strong></p>
        <p><?= htmlspecialchars($user['unit'] ?? '') ?></p>
        <p><?= htmlspecialchars($user['jurusan'] ?? '') ?></p>
        <p><?= htmlspecialchars($user['program_studi'] ?? '') ?></p>
        <p><?= htmlspecialchars($user['nim_nip']) ?></p>
        <p><?= htmlspecialchars($user['no_hp']) ?></p>
        <p><?= htmlspecialchars($user['email']) ?></p>
        <a class="btn-logout" href="#" onclick="showLogoutModal(); return false;">Keluar</a>
      </div>
    </div>
</header>

<main class="page">
  
  <div class="page-header">
    <h1 class="page-title">Profil Saya</h1>
    <p class="page-subtitle">Kelola informasi profil dan akun Anda</p>
  </div>

  <section class="profile-container">
    
    <div class="profile-sidebar">
      <div class="avatar-wrap">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="Foto Profil">
      </div>
      <h2 class="sidebar-name"><?= htmlspecialchars($user['nama'] ?? 'User') ?></h2>
      <span class="role-badge"><?= htmlspecialchars($user['role'] ?? 'Mahasiswa') ?></span>
      
      <a class="btn-logout-main" href="?route=Auth/logout">Keluar</a>
    </div>

    <div class="profile-details">
      <h3 class="details-title">Informasi Pribadi</h3>
      
      <div class="details-grid">
        <div class="info-item">
          <span class="label">UNIT</span>
          <span class="value"><?= htmlspecialchars($user['unit'] ?? '-') ?></span>
        </div>
        <div class="info-item">
          <span class="label">JURUSAN</span>
          <span class="value"><?= htmlspecialchars($user['jurusan'] ?? '-') ?></span>
        </div>
        <div class="info-item">
          <span class="label">PROGRAM STUDI</span>
          <span class="value"><?= htmlspecialchars($user['program_studi'] ?? '-') ?></span>
        </div>
        <div class="info-item">
          <span class="label">NIM / NIP</span>
          <span class="value"><?= htmlspecialchars($user['nim_nip'] ?? '-') ?></span>
        </div>
        <div class="info-item">
          <span class="label">NO. HP</span>
          <span class="value"><?= htmlspecialchars($user['no_hp'] ?? '-') ?></span>
        </div>
        <div class="info-item">
          <span class="label">EMAIL</span>
          <span class="value"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
        </div>
      </div>
    </div>

  </section>
</main>

<footer class="footer">
    <div class="footer-content-wrapper">
        <div class="footer-left">
            <div class="footer-brand">
                <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy Ruang Study" class="footer-logo">
            </div>
            <p class="footer-description">
                Rudi Ruangan Studi adalah platform peminjaman ruangan perpustakaan yang membantu mahasiswa dan staf mengatur penggunaan ruang belajar dengan mudah dan efisien.
            </p>
        </div>

        <div class="footer-nav">
            <div>
                <h4>Navigasi</h4>
                <a href="?route=user/home">Beranda</a>
                <a href="?route=user/ruangan">Ruangan</a>
                <a id="navigasipanduan" href="#">Panduan</a>
            </div>        
            <div>
                <h4>Kontak</h4>
                <a href="mailto:PerpusPNJ@email.com">PerpusPNJ@email.com</a>
                <a href="tel:0822123456780">0822123456780</a>
                <p>Kampus PNJ, Depok</p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>