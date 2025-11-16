<?php
require_once '../config/app.php';
require_once '../core/Router.php';

$router = new Router();
$router->run();


?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rudy Ruang Study</title>
  <link rel="stylesheet" href="assets/css/stylehome.css">
</head>

<body>
<header class="navbar">
  <div class="logo">
    <img src="assets/image/LogoPNJ.png" alt="Logo PNJ" height="40">
    <img src="assets/image/LogoRudy.png" alt="Logo Rudy" height="40">
  </div>
  <nav class="nav-menuguest">
    <a href="home.php" class="active">Beranda</a>
    <a href="ruangan.php">Ruangan</a>
    <a href="riwayat.php">Riwayat</a>
  </nav>
  <div class="guest-login-button">
    <div class="btn-group">
      <a href="../app/views/auth/login_user.php" class="btn primary">Login</a>
    </div>
  </div>
</header>

<main>
  <section class="hero">
    <div class="hero-text">
      <p class="intro">Selamat Datang di <span>Rudy</span></p>
      <h1>Ruang Study untuk Semua Mahasiswa!</h1>
      <p class="description">
        Atur jadwal belajar dan diskusi dengan mudah melalui Rudy. Temukan ruangan study nyaman,
        praktis, dan siap digunakan kapan pun.
      </p>
      <div class="btn-group">
        <a href="../app/views/guest/ruangan.php" class="btn primary">Lihat daftar Ruangan</a>
        <a href="#" id="lihat-cara-booking"class="btn secondary">Lihat Cara Booking</a>
      </div>
    </div>
    <div class="hero-visual">
      <img src="assets/image/LogoRudy.png" alt="Logo Rudy">
    </div>
  </section>


  <section class="fitur">
    <div class="fitur-row">
      <div class="fitur-item">
        <img src="assets/image/cepat.png" alt="Cepat">
        <h3>Cepat & Praktis</h3>
        <p>Pemesanan ruangan hanya beberapa klik.</p>
      </div>
      <div class="fitur-item">
        <img src="assets/image/terintegrasi.png" alt="Terintegrasi">
        <h3>Terintegrasi Perpustakaan</h3>
        <p>Data ruangan langsung dari perpustakaan.</p>
      </div>
      <div class="fitur-item">
        <img src="assets/image/transparan.png" alt="Fleksibel">
        <h3>Fleksibel & Transparan</h3>
        <p>Atur jadwal sesuai kebutuhanmu.</p>
      </div>
      <div class="fitur-item">
        <img src="assets/image/mudahdigunakan.png" alt="Mudah">
        <h3>Mudah Digunakan</h3>
        <p>Antarmuka ramah mahasiswa.</p>
      </div>
    </div>
  </section>

  <section class="ruangan-section">
    <div class="section-header">
      <h2>Ruangan Populer di Rudy</h2>
      <p>Temukan ruang study favorit mahasiswa.</p>
    </div>

    <div class="ruangan-list">
      <article class="card">
        <img src="assets/image/contohruangan.png" alt="Lentera Edukasi">
        <div class="card-body">
          <h3>Lentera Edukasi</h3>
          <p>Kapasitas : 6 - 12 orang</p>
          <p>Status : <span class="status">Tersedia</span></p>
          <button type="button" class="btn primary block booking-trigger">Booking sekarang</button>
        </div>
      </article>

      <article class="card">
        <img src="assets/image/contohruangan.png" alt="Galeri Literasi">
        <div class="card-body">
          <h3>Galeri Literasi</h3>
          <p>Kapasitas : 6 - 12 orang</p>
          <p>Status : <span class="status">Tersedia</span></p>
          <button type="button" class="btn primary block booking-trigger">Booking sekarang</button>
        </div>
      </article>

      <article class="card">
        <img src="assets/image/contohruangan.png" alt="Sudut Pustaka">
        <div class="card-body">
          <h3>Sudut Pustaka</h3>
          <p>Kapasitas : 6 - 12 orang</p>
          <p>Status : <span class="status">Tersedia</span></p>
          <button type="button" class="btn primary block booking-trigger">Booking sekarang</button>
        </div>
      </article>
    </div>
  </section>

<div class="modal" id="login-modal" aria-hidden="true">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="login-modal-title">
    <button class="modal-close" type="button" aria-label="Tutup pop-up">&times;</button>
    <h3 id="login-modal-title">Login untuk membooking ruangan non-rapat ini</h3>
    <p>Masuk terlebih dahulu agar kamu bisa melanjutkan proses booking ruangan favoritmu.</p>
    <a href="../app/views/auth/login_user.php" class="btn primary modal-login">Masuk sekarang</a>
  </div>
</div>

  <section id="cara-booking" class="steps">
    <h2>Cara Menggunakan Rudy</h2>
    <ol>
      <li>Login ke akunmu.</li>
      <li>Pilih ruangan yang ingin kamu pinjam.</li>
      <li>Isi formulir peminjaman.</li>
      <li>Dapatkan kode booking.</li>
      <li>Tunjukkan kode booking ke admin perpustakaan.</li>
      <li>Ruangan siap dipakai.</li>
    </ol>
  </section>
</main>

<footer class="footer">
  <div class="footer-brand">
    <img src="../../../public/assets/image/LogoRudy.png" alt="Rudy">
    <p>Rudy Ruang Study - platform peminjaman ruangan study yang praktis, transparan, dan terintegrasi.</p>
  </div>
  <div class="footer-nav">
    <div>
      <h4>Navigasi</h4>
      <a href="#">Beranda</a>
      <a href="#">Ruangan</a>
      <a id="navigasipanduan" href="#">Panduan</a>
      <a href="#">Masuk</a>
    </div>
    <div>
      <h4>Bantuan</h4>  
      <a href="#">FAQ</a>
      <a id="bantuanpanduan" href="#">Panduan</a>
      <a href="#">Alur</a>
      <a href="#">Akun</a>
    </div>
    <div>
      <h4>Kontak</h4>
      <a href="mailto:PerpusPNJ@email.com">PerpusPNJ@email.com</a>
      <a href="tel:0822123456780">0822123456780</a>
      <p>Kampus PNJ, Depok</p>
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

  (function () {
    const modal = document.getElementById('login-modal');
    if (!modal) return;
    const openModal = () => {
      modal.classList.add('show');
      modal.setAttribute('aria-hidden', 'false');
    };
    const closeModal = () => {
      modal.classList.remove('show');
      modal.setAttribute('aria-hidden', 'true');
    };
    document.querySelectorAll('.booking-trigger').forEach(btn => {
      btn.addEventListener('click', event => {
        event.preventDefault();
        openModal();
      });
    });
    modal.addEventListener('click', event => {
      if (
        event.target.classList.contains('modal') ||
        event.target.classList.contains('modal-backdrop') ||
        event.target.classList.contains('modal-close')
      ) {
        closeModal();
      }
    });

    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && modal.classList.contains('show')) {
        closeModal();
      }
    });
  })();
  document.querySelector('#bantuanpanduan').addEventListener('click', function(e){
        e.preventDefault();
        document.querySelector('#cara-booking').scrollIntoView({
            behavior: 'smooth'
        });
        });
</script>

</body>
</html>
