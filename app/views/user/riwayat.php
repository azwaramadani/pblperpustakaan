<?php
session_start();
$_SESSION['user_id'] = 1;
require_once '../../../config/database.php'; 
// Ambil data user yang sedang login
$user_id = $_SESSION['user_id'];
$query_user = "SELECT nama, email FROM user WHERE user_id = '$user_id'";
$result_user = mysqli_query($connection, $query_user);
$user_data = mysqli_fetch_assoc($result_user);

// Misal ambil berdasarkan user login (contoh: $_SESSION['user_id'])
$user_id = 1; // nanti ganti ini sesuai session login

$sql = "
    SELECT 
        rm.nama_ruangan,
        b.kode_booking,
        DATE_FORMAT(b.tanggal, '%W, %e %M %Y') AS tanggal,
        CONCAT(DATE_FORMAT(b.jam_mulai, '%H:%i'), ' - ', DATE_FORMAT(b.jam_selesai, '%H:%i')) AS jam,
        b.nama_penanggung_jawab AS penanggung,
        b.nimnip_penanggung_jawab AS nim,
        b.email_penanggung_jawab AS email,
        b.nimnip_peminjam AS nim_ruangan,
        b.status_booking AS status,
        rm.gambar_ruangan AS gambar
    FROM booking b
    JOIN room rm ON b.room_id = rm.room_id
    JOIN user r ON b.user_id = r.user_id
    WHERE b.user_id = '$user_id'
    ORDER BY b.tanggal DESC
";

$result = mysqli_query($connection, $sql);
$riwayat = [];

while ($row = mysqli_fetch_assoc($result)) {
    $riwayat[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman Ruangan</title>
    <link rel="stylesheet" href="../../../public/assets/css/styleriwayat.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
    <a href="ruangan.php">Ruangan</a>
    <a href="riwayat.php" class="active">Riwayat</a>
  </nav>
  <div class="profile">
        <img src="../../../public/assets/image/userlogo.png" alt="User">
        <div class="user-name">
            <p><?= htmlspecialchars($user_data['nama']) ?></p>
        </div>
    </div>
</header>

<!-- ===== Judul ===== -->
<h2 class="title">Riwayat Peminjaman Saya</h2>

<!-- ===== Konten Riwayat ===== -->
<div class="container">
    <?php foreach ($riwayat as $r): ?>
        <div class="card">
            <div class="info">
                <h3><?= htmlspecialchars($r['nama_ruangan']) ?></h3>
                <p><strong>Kode Booking:</strong> <?= htmlspecialchars($r['kode_booking']) ?></p>
                <p><strong>Tanggal:</strong> <?= htmlspecialchars($r['tanggal']) ?></p>
                <p><strong>Jam:</strong> <?= htmlspecialchars($r['jam']) ?></p>
                <p><strong>Penanggung Jawab:</strong> <?= htmlspecialchars($r['penanggung']) ?></p>
                <p><strong>NIM:</strong> <?= htmlspecialchars($r['nim']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($r['email']) ?></p>
                <p><strong>Anggota Ruangan:</strong> <?= htmlspecialchars($r['nim_ruangan']) ?></p>
                <p><strong>Status:</strong> 
                    <span class="status <?= strtolower($r['status']) ?>">
                        <?= htmlspecialchars($r['status']) ?>
                    </span>
                </p>
            </div>

            <div class="gambar">
                <img src="<?= htmlspecialchars($r['gambar']) ?>" alt="Ruangan">
                <div class="btn-group">
                    <?php if ($r['status'] == 'Menunggu'): ?>
                        <a href="#" class="btn ubah">Ubah</a>
                        <a href="#" class="btn batal">Batalkan</a>
                    <?php elseif ($r['status'] == 'Selesai' && !$r['sudah_feedback']): ?>
                        <a href="feedback.php?kode=<?= urlencode($r['kode_booking']) ?>" class="btn feedback">Beri Feedback</a>
                    <?php elseif ($r['status'] == 'Selesai' && $r['sudah_feedback']): ?>
                        <a href="feedback.php?kode=<?= urlencode($r['kode_booking']) ?>&lihat=true" class="btn feedback">Lihat Feedback Saya</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ===== Footer ===== -->
<footer class="footer">
    <div class="footer-brand">
        <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy">
        <p>Rudy Ruang Study - platform peminjaman ruangan study yang praktis, transparan, dan terintegrasi.</p>
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
