<?php
session_start();

// 1. Generate Kode Acak (5 Karakter)
$random_alpha = md5(rand());
$captcha_code = substr($random_alpha, 0, 3);

// Simpan kode di sesi untuk validasi nanti
$_SESSION['captcha_code'] = $captcha_code;

// 2. Buat Gambar (Lebar 120px, Tinggi 40px)
$image = imagecreatetruecolor(140, 48);

// 3. Tentukan Warna
$background_color = imagecolorallocate($image, 249, 249, 249); // Warna #f9f9f9 (sesuai input)
$text_color = imagecolorallocate($image, 11, 142, 138);       // Warna Teal #0b8e8a
$line_color = imagecolorallocate($image, 255, 193, 7);        // Warna Kuning #FFC107 (Noise)
$pixel_color = imagecolorallocate($image, 187, 187, 187);     // Warna Abu (Noise)

imagefilledrectangle($image, 0, 0, 140, 48, $background_color);

// 4. Tambahkan "Noise" (Garis & Titik) agar susah dibaca bot
for($i=0; $i<5; $i++) {
    imageline($image, 0, rand() % 50, 200, rand() % 50, $line_color);
}
for($i=0; $i<500; $i++) {
    imagesetpixel($image, rand() % 200, rand() % 50, $pixel_color);
}

// 5. Tulis Kode di Gambar
// Menggunakan font bawaan PHP (angka 5 adalah ukuran font terbesar bawaan)
imagestring($image, 5, 45, 15, $captcha_code, $text_color);

// 6. Output Gambar
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>