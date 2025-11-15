<?php
session_start();
require_once '../../../config/database.php';

// Ambil data user untuk profil kanan atas
$user_id = $_SESSION['user_id'] ?? 1;
$query_user = "SELECT nama, email FROM user WHERE user_id = '$user_id'";
$result_user = mysqli_query($connection, $query_user);
$user_data = mysqli_fetch_assoc($result_user);

// Ambil daftar ruangan
$query_room = "SELECT * FROM room WHERE status = 'Tersedia' ORDER BY nama_ruangan ASC";
$result_room = mysqli_query($connection, $query_room);
$ruangan = [];

while ($row = mysqli_fetch_assoc($result_room)) {
    $ruangan[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ruangan | Rudy Ruang Study</title>
    <link rel="stylesheet" href="../../../public/assets/css/styleruangan.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- ===== Navbar ===== -->
<header class="navbar">
  <div class="logo">
    <img src="../../../public/assets/image/LogoPNJ.png" alt="Logo PNJ" height="40">
    <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy" height="40">
  </div>
  <nav class="nav-menu">
    <a href="home.php">Beranda</a>
    <a href="ruangan.php" class="active">Ruangan</a>
    <a href="riwayat.php">Riwayat</a>
  </nav>
  <div class="profile">
        <a href=profile.php>        
            <img src="../../../public/assets/image/userlogo.png" alt="User">
        </a>
        <div class="user-name">
            <p><?= htmlspecialchars($user_data['nama']) ?></p>
        </div>
    </div>
</header>

<!-- ===== Judul ===== -->
<main>
    <section class="title-section">
        <h2 class="title">Daftar Ruangan</h2>
        <p class="subtitle">Lihat ketersediaan ruangan untuk belajar individu, diskusi kelompok, atau kegiatan akademik lainnya.</p>

        <div class="category-buttons">
            <button class="btn-filter active">Ruang Study</button>
            <button class="btn-filter">Ruang Rapat</button>
        </div>
    </section>

    <!-- ===== Grid Ruangan ===== -->
    <div class="room-container">
        <?php if (empty($ruangan)): ?>
            <p class="no-room">Tidak ada ruangan tersedia saat ini.</p>
        <?php else: ?>
            <?php foreach ($ruangan as $r): ?>
                <div class="room-card">
                    <img src="../../../public/assets/image/<?= htmlspecialchars($r['gambar_ruangan']) ?>" alt="<?= htmlspecialchars($r['nama_ruangan']) ?>" class="room-img">
                    <div class="room-info">
                        <h3><?= htmlspecialchars($r['nama_ruangan']) ?></h3>
                        <p>Kapasitas: <?= htmlspecialchars($r['kapasitas_min']) ?> - <?= htmlspecialchars($r['kapasitas_max']) ?> orang</p>
                        <p>Status: <span class="status tersedia"><?= htmlspecialchars($r['status']) ?></span></p>
                    </div>
                    <a href="booking.php?room_id=<?= urlencode($r['room_id']) ?>" class="btn-book">Booking sekarang</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
<!-- ===== Footer ===== -->
<footer class="footer">
    <div class="footer-brand">
        <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy">
        <p>Rudy Ruang Study adalah platform peminjaman ruangan study yang praktis, transparan, dan terintegrasi.</p>
    </div>

    <div class="footer-nav">
        <div>
            <h4>Navigasi</h4>
            <a href="#">Beranda</a>
            <a href="#">Daftar Ruangan</a>
            <a href="#">Panduan</a>
            <a href="#">Masuk</a>
        </div>
        <div>
            <h4>Bantuan</h4>
            <a href="#">FAQ</a>
            <a href="#">Aturan Ruangan</a>
        </div>
        <div>
            <h4>Kontak</h4>
            <p>PerpusPNJ@gmail.com</p>
            <p>0822-3245-6780</p>
            <p>Kampus PNJ, Depok</p>
        </div>
    </div>
</footer>

</body>
</html>
