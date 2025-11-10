<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Data Ruangan - Rudy Ruang Study</title>
  <link rel="stylesheet" href="../../../public/assets/css/styleadmin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
  <aside class="sidebar">
    <div class="brand">
      <img src="../../../public/assets/image/LogoPNJ.png" alt="Logo PNJ">
      <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy">
    </div>
    <nav class="sidebar-nav">
      <a href="peminjaman.php">Peminjaman</a>
      <a href="ruang_rapat.php">Ruang Rapat</a>
      <a href="data_ruangan.php" class="active">Data Ruangan</a>
      <a href="data_akun.php">Data Akun</a>
    </nav>
  </aside>

  <main class="content">
    <header class="content-header">
      <div>
        <h1>Data Ruangan</h1>
        <p>Kelola informasi ruangan .</p>
      </div>
      <div class="profile-summary">
        <img src="../../../public/assets/image/Logo 1.png" alt="Admin" class="avatar">
        <div>
          <p>Admin</p>
          <span>Super Admin</span>
        </div>
      </div>
    </header>

    <section class="toolbar">
      <button class="btn primary">Tambah Ruangan</button>
      <div class="search-box">
        <input type="text" placeholder="Cari ruangan">
        <span class="icon">&#128269;</span>
      </div>
    </section>

    <section class="room-list">
      <?php for ($i = 0; $i < 2; $i++): ?>
      <article class="room-card">
        <div class="room-info">
          <h2>Lentera Edukasi</h2>
          <p>Deskripsi : Ruangan khusus bimbingan dan konseling dengan suasana tenang dan privat. Cocok untuk sesi diskusi, pendampingan akademik, atau konsultasi pribadi.</p>
          <p>Kapasitas : 2 - 4 orang</p>
          <p>Jumlah peminjaman : 10 kali</p>
          <p>Tingkat kepuasan : 90%</p>
          <p>Jumlah feedback : 10</p>
          <div class="card-actions">
            <button class="btn outline">Lihat Feedback</button>
            <button class="btn secondary">Ubah</button>
            <button class="btn danger">Hapus</button>
          </div>
        </div>
        <div class="room-image">
          <img src="../../../public/assets/image/contohruangan.png" alt="Foto ruang">
        </div>
      </article>
      <?php endfor; ?>
    </section>
  </main>
</div>
</body>
</html>
