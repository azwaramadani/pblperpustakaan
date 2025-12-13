<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rudy Ruang Study</title>
  <link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylehome.css?v=1.6">
</head>

<body>

<header class="navbar">
  <div class="logo">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40">
    <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40">
  </div>

  <nav class="nav-menu">
    <a href="?route=User/home" class="active">Beranda</a>
    <a href="?route=User/ruangan">Ruangan</a>
    <a href="?route=User/riwayat">Riwayat</a>
  </nav>

    <div class="profile-dropdown">
      <div class="profile-trigger">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png" alt="User">
        <div class="user-name"><p><?= htmlspecialchars($user['nama']) ?></p></div>
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
        <a class="btn-logout" href="?route=Auth/logout">Keluar</a>
      </div>
    </div>
</header>

<main>
  <section class="hero">
    <div class="hero-text">
      <p class="intro">Selamat Datang di <span>Rudy</span></p>
      <h1>Ruang Study untuk Semua Mahasiswa!</h1>
      <p class="description">
        Atur jadwal belajar dan diskusi dengan mudah melalui Rudy.
      </p>
      <div class="btn-group">
        <a href="?route=User/ruangan" class="btn primary">Lihat daftar Ruangan</a>
        <a href="#" id="lihat-cara-booking" class="btn secondary">Lihat Cara Booking</a>
      </div>
    </div>

    <div class="hero-visual">
      <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png">
    </div>
  </section>

  <section class="fitur">
    <div class="fitur-row">
      <div class="fitur-item">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/cepat.png" alt="Cepat">
        <h3>Cepat & Praktis</h3>
        <p>Pemesanan ruangan hanya beberapa klik.</p>
      </div>

      <div class="fitur-item">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/terintegrasi.png" alt="Terintegrasi">
        <h3>Terintegrasi Perpustakaan</h3>
        <p>Data ruangan langsung dari perpustakaan.</p>
      </div>

      <div class="fitur-item">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/transparan.png" alt="Fleksibel">
        <h3>Fleksibel & Transparan</h3>
        <p>Atur jadwal sesuai kebutuhanmu.</p>
      </div>

      <div class="fitur-item">
        <img src="<?= app_config()['base_url'] ?>/public/assets/image/mudahdigunakan.png" alt="Mudah">
        <h3>Mudah Digunakan</h3>
        <p>Antarmuka ramah mahasiswa.</p>
      </div>
    </div>
  </section>

  <section class="ruangan-section">
    <div class="section-header">
      <h2>Ruangan Populer di Rudy</h2>
      <p>Ruangan Favorit Mahasiswa!</p>
    </div>
    <div class="ruangan-list">
      <?php foreach ($toprooms as $tr): ?>
        <article class="card">
          <img src="<?= app_config()['base_url'] ?>/public/assets/image/contohruangan.png" alt="Ruangan Populer">
          <div class="card-body">
            <h3><?= htmlspecialchars($tr['nama_ruangan']) ?></h3>
            <p>Kapasitas: <?= htmlspecialchars($tr['kapasitas_min']) ?> - <?=  htmlspecialchars($tr['kapasitas_max'])?> orang </p>
            <p>Status : <span class="status"><?=  htmlspecialchars($tr['status']) ?></span></p>
            <a href="?route=Booking/step1/<?= $tr['room_id'] ?>">
              <button type="button" class="btn primary block booking-trigger">Booking sekarang</button>
            </a>  
          </div>
        </article>
      <?php endforeach; ?>  
    </div>
  </section>

  <section id="cara-booking" class="steps">
    <h2>Cara Menggunakan Rudy</h2>
    <ol>
      <li>Login ke akunmu.</li>
      <li>Pilih ruangan.</li>
      <li>Isi formulir peminjaman.</li>
      <li>Dapatkan kode booking.</li>
      <li>Tunjukkan ke admin.</li>
    </ol>
  </section>
</main>

<!-- FOOTER YANG SUDAH DIPERBAIKI -->
<footer class="footer">
    <div class="footer-content-wrapper">
        <!-- Bagian Kiri: Logo & Deskripsi -->
        <div class="footer-left">
            <div class="footer-brand">
                <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" alt="Logo Rudy Ruang Study" class="footer-logo">
            </div>
            <p class="footer-description">
                Rudi Ruangan Studi adalah platform peminjaman ruangan perpustakaan yang membantu mahasiswa dan staf mengatur penggunaan ruang belajar dengan mudah dan efisien.
            </p>
        </div>

        <!-- Bagian Kanan: Navigasi & Kontak -->
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

<script>
  document.querySelector('#lihat-cara-booking').addEventListener('click', function(e){
      e.preventDefault();
      document.querySelector('#cara-booking').scrollIntoView({
          behavior: 'smooth'
      });
  });
</script>
</body>
</html>