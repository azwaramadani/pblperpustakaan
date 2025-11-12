<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Ruangan - Rudy Ruang Study</title>
  <link rel="stylesheet" href="../../../public/assets/css/styleruangan.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <div class="logo">
        <img src="../../../public/assets/image/LogoPNJ.png" alt="Logo PNJ" class="logo-pnj">
        <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy" class="logo-rudy">
    </div>
    <nav>
      <a href="home.php">Beranda</a>
      <a href="ruangan.php" class="active">Ruangan</a>
      <a href="riwayat.php">Riwayat</a>
    </nav>
    <a href="#" class="btn-login">Masuk</a>
  </header>

  <!-- Main Content -->
  <main class="container">
    <h2 class="title">Daftar Ruangan</h2>
    <p class="subtitle">Lihat ketersediaan ruangan untuk belajar individu, diskusi kelompok, atau kegiatan akademik lainnya.</p>

    <!-- Tombol Kategori -->
    <div class="category-buttons">
      <button class="btn-category active">Ruang Study</button>
      <button class="btn-category">Ruang Rapat</button>
    </div>

    <!-- Grid Ruangan -->
    <div class="room-grid">
      <?php
      $rooms = [
        ["nama" => "Pusat Prancis", "kapasitas" => "6 - 12 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"],
        ["nama" => "Ruang Layar", "kapasitas" => "6 - 12 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"],
        ["nama" => "Ruang Sinergi", "kapasitas" => "6 - 12 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"],
        ["nama" => "Zona Interaktif", "kapasitas" => "6 - 12 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"],
        ["nama" => "Sudut Pustaka", "kapasitas" => "6 - 12 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"],
        ["nama" => "Ruang Cendikia", "kapasitas" => "6 - 12 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"],
        ["nama" => "Galeri Literasi", "kapasitas" => "6 - 12 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"],
        ["nama" => "Ruang Asa", "kapasitas" => "2 - 4 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"],
        ["nama" => "Lentera Edukasi", "kapasitas" => "2 - 4 orang", "status" => "Tersedia", "gambar" => "../../../public/assets/image/contohruangan.png"]
      ];

      foreach ($rooms as $room) {
        echo '
        <div class="room-card">
          <img src="'.$room["gambar"].'" alt="'.$room["nama"].'">
          <div class="room-content">
            <h3>'.$room["nama"].'</h3>
            <p><strong>Kapasitas:</strong> '.$room["kapasitas"].'</p>
            <p><strong>Status:</strong> '.$room["status"].'</p>
            <button class="btn-book">Booking sekarang</button>
          </div>
        </div>';
      }
      ?>
    </div>
  </main>

  <footer class="footer">
  <div class="footer-brand">
    <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy">
    <p>Rudy Ruang Study - platform peminjaman ruangan study yang praktis, transparan, dan terintegrasi.</p>
  </div>

  <div class="footer-nav">
    <div class="footer-section">
      <h4>Navigasi</h4>
      <a href="#">Beranda</a>
      <a href="#">Daftar Ruangan</a>
      <a href="#">Panduan</a>
      <a href="#">Masuk</a>
    </div>

    <div class="footer-section">
      <h4>Bantuan</h4>
      <a href="#">FAQ</a>
      <a href="#">Aturan Ruangan</a>
    </div>

    <div class="footer-section">
      <h4>Kontak</h4>
      <p>📧 PerpusPNJ@gmail.com</p>
      <p>📞 082123456780</p>
      <p>📍 Kampus PNJ, Depok</p>
    </div>
  </div>
</footer>


</body>
</html>
