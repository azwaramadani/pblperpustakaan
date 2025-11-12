<?php
// Data dummy (nanti bisa diganti query MySQL)
$riwayat = [
    [
        'nama_ruangan' => 'Lentera Edukasi',
        'kode_booking' => 'MLBK4G',
        'tanggal' => 'Selasa, 11 November 2025',
        'jam' => '09.00 - 11.00',
        'penanggung' => 'Muhammad Hanif Zidan',
        'nim' => '2407411050',
        'email' => 'muhammad.hanif.zidan.tik24@pnj.ac.id',
        'nim_ruangan' => '2407411051, 2407411052, 2407411053',
        'status' => 'Menunggu',
        'sudah_feedback' => false,
        'gambar' => 'img/ruangan.jpg'
    ],
    [
        'nama_ruangan' => 'Lentera Edukasi',
        'kode_booking' => 'YNTKTS',
        'tanggal' => 'Senin, 3 November 2025',
        'jam' => '09.00 - 11.00',
        'penanggung' => 'Muhammad Hanif Zidan',
        'nim' => '2407411050',
        'email' => 'muhammad.hanif.zidan.tik24@pnj.ac.id',
        'nim_ruangan' => '2407411051, 2407411052, 2407411053',
        'status' => 'Selesai',
        'sudah_feedback' => false,
        'gambar' => 'img/ruangan.jpg'
    ],
    [
        'nama_ruangan' => 'Lentera Edukasi',
        'kode_booking' => 'TDR3000',
        'tanggal' => 'Senin, 27 Oktober 2025',
        'jam' => '09.00 - 11.00',
        'penanggung' => 'Muhammad Hanif Zidan',
        'nim' => '2407411050',
        'email' => 'muhammad.hanif.zidan.tik24@pnj.ac.id',
        'nim_ruangan' => '2407411051, 2407411052, 2407411053',
        'status' => 'Selesai',
        'sudah_feedback' => true,
        'gambar' => 'img/ruangan.jpg'
    ]
];
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

<header class="navbar">
    <div class="left-section">
        <img src="../../../public/assets/image/LogoPNJ.png" alt="Logo PNJ" class="logo-pnj">
        <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy" class="logo-rudy">
    </div>

    <nav class="nav-links">
        <div class="left-section">
        <a href="home.php">Beranda</a>
        <a href="ruangan.php">Ruangan</a>
        <a href="riwayat.php" class="active">Riwayat</a>
</div>
    </nav>
        <div class="profile"> 
        <div class="user-name"> 
            <p>Guest</p> 
        </div> 
    </div>
    </div>
</header>
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
            <img src="../../../public/assets/image/contohruangan.png" alt="Lentera Edukasi">
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

<footer class="footer">
    <div class="footer-brand">
        <img src="../../../public/assets/image/LogoRudy.png" alt="Logo Rudy">
        <p>Rudy Ruang Study - platform peminjaman ruangan study yang praktis, transparan, dan terintegrasi.</p>
    </div>

    <div class="footer-nav">
        <div>
            <h4>Navigasi</h4>
            <a href="home.php">Beranda</a>
            <a href="ruangan.php">Daftar Ruangan</a>
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
