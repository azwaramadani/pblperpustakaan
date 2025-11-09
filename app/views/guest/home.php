<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rudy Ruang Study</title>
  <link rel="stylesheet" href="../../../public/assets/css/stylehome.css">
</head>

<body>
<header class="navbar">
  <div class="logo">
    <img src="../../../public/assets/image/LogoPNJ.png" alt="Logo PNJ" height="40">
    <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy" height="40">
  </div>
  <nav class="nav-menu">
    <a href="app/views/guest/home.php" class="active">Beranda</a>
    <a href="app/views/guest/ruangan.php">Ruangan</a>
    <a href="app/views/guest/riwayat.php">Riwayat</a>
  </nav>
  <div class="profile">
    <div class="user-name">
      <p>Hanif</p>
      <span>Mahasiswa</span>
    </div>
    <img src="../../../public/assets/image/Logo 1.png" alt="user" class="user-icon">
  </div>
</header>

<main>
  <section class="hero">
    <div class="hero-text">
      <p class="intro">Selamat Datang di <span>Rudy</span></p>
      <h1>Ruangan Study untuk Semua Mahasiswa!</h1>
      <p class="description">
        Atur jadwal belajar dan diskusi dengan mudah melalui Rudy. Temukan ruangan study nyaman,
        praktis, dan siap digunakan kapan pun.
      </p>
      <div class="btn-group">
        <a href="app/views/guest/ruangan.php" class="btn primary">Lihat daftar Ruangan</a>
        <a href="#" class="btn secondary">Lihat Cara Booking</a>
      </div>
    </div>
    <div class="hero-visual">
      <img src="../../../public/assets/image/LogoRudy.png" alt="Ruangan belajar">
    </div>
  </section>

  <section class="fitur">
    <div class="fitur-row">
      <div class="fitur-item">
        <img src="../../../public/assets/image/cepat.png" alt="Cepat">
        <h3>Cepat & Praktis</h3>
        <p>Pemesanan ruangan hanya beberapa klik.</p>
      </div>
      <div class="fitur-item">
        <img src="../../../public/assets/image/terintegrasi.png" alt="Terintegrasi">
        <h3>Terintegrasi Perpustakaan</h3>
        <p>Data ruangan langsung dari perpustakaan.</p>
      </div>
      <div class="fitur-item">
        <img src="../../../public/assets/image/transparan.png" alt="Fleksibel">
        <h3>Fleksibel & Transparan</h3>
        <p>Atur jadwal sesuai kebutuhanmu.</p>
      </div>
      <div class="fitur-item">
        <img src="../../../public/assets/image/mudahdigunakan.png" alt="Mudah">
        <h3>Mudah Digunakan</h3>
        <p>Antarmuka ramah mahasiswa.</p>
      </div>
    </div>
  </section>

  <section class="ruangan-section">
    <div class="section-header">
      <p>Temukan ruangan study favorit mahasiswa.</p>
      <h2>Ruangan Populer di Rudy</h2>
    </div>

    <div class="ruangan-list">
      <article class="card">
        <img src="../../../public/assets/image/ruang1.jpg" alt="Lentera Edukasi">
        <div class="card-body">
          <h3>Lentera Edukasi</h3>
          <p>Kapasitas : 6 - 12 orang</p>
          <p>Status : <span class="status">Tersedia</span></p>
          <button class="btn primary block">Booking sekarang</button>
        </div>
      </article>

      <article class="card">
        <img src="../../../public/assets/image/ruang2.jpg" alt="Galeri Literasi">
        <div class="card-body">
          <h3>Galeri Literasi</h3>
          <p>Kapasitas : 6 - 12 orang</p>
          <p>Status : <span class="status">Tersedia</span></p>
          <button class="btn primary block">Booking sekarang</button>
        </div>
      </article>

      <article class="card">
        <img src="../../../public/assets/image/ruang3.jpg" alt="Sudut Pustaka">
        <div class="card-body">
          <h3>Sudut Pustaka</h3>
          <p>Kapasitas : 6 - 12 orang</p>
          <p>Status : <span class="status">Tersedia</span></p>
          <button class="btn primary block">Booking sekarang</button>
        </div>
      </article>
    </div>
  </section>

  <section class="steps">
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
      <a href="#">Panduan</a>
      <a href="#">Masuk</a>
    </div>
    <div>
      <h4>Bantuan</h4>
      <a href="#">FAQ</a>
      <a href="#">Panduan</a>
      <a href="#">Alur</a>
      <a href="#">Akun</a>
    </div>
    <div>
      <h4>Kontak</h4>
      <a href="mailto:perpustakaan@pbl.com">perpustakaan@pbl.com</a>
      <a href="tel:021987654">021-987654</a>
      <p>Kampus PNJ, Depok</p>
    </div>
  </div>
</footer>

</body>
</html>
