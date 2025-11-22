<?php
# ===============================================================
# CORE: HELPER
# ===============================================================
# Berisi fungsi umum yang digunakan oleh banyak bagian aplikasi.
# ===============================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

# Akses konfigurasi global
function app_config(): array
{
    static $cfg = null;

    if ($cfg === null) {
        $cfg = require __DIR__ . '/../config/app.php';
        date_default_timezone_set($cfg['timezone'] ?? 'Asia/Jakarta');
    }

    return $cfg;
}

# buat mailpit
function sendMail($to, $subject, $body) {
    $mailConfig = require __DIR__ . '/../config/mail.php';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $mailConfig['host'];
        $mail->Port = $mailConfig['port'];
        $mail->SMTPAuth = false;

        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        return $mail->send();

    } catch (Exception $e) {
        return false;
    }
}


# Generate kode booking unik
function generateBookingCode(): string
{
    $date = date('Ymd');
    $rand = rand(1000, 9999);
    return "BK-{$date}-{$rand}";
}

# Upload file (contoh: bukti aktivasi, surat peminjaman)
function uploadFile($file, $targetDir)
{
    if (!isset($file) || $file['error'] !== 0) {
        return false;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = time() . "-" . rand(1000, 9999) . "." . $ext;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $path = $targetDir . $name;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return $name;
    }

    return false;
}

# Kirim JSON (untuk AJAX)
function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
