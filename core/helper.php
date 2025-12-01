<?php
# ===============================================================
# CORE: HELPER
# ===============================================================
# Berisi fungsi umum yang digunakan oleh banyak bagian aplikasi.
# ===============================================================
require_once __DIR__ . '/../config/app.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ...lanjutkan isi helper lainnya (sendMail, generateBookingCode, dst)

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

# method/function Generate kode booking 
function generateBookingCode(): string
{
    $date = date('Ymd');
    $rand = random_int(1000, 9999);
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
