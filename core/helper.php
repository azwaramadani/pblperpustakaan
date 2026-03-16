<?php

require_once __DIR__ . '/../config/app.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $body)
{
    $mailConfig = require __DIR__ . '/../config/mail.php';

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host       = $mailConfig['host'];
        $mail->Port       = $mailConfig['port'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailConfig['username'];
        $mail->Password   = $mailConfig['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        return $mail->send();

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        exit;
    }
}

function generateBookingCode(): string
{
    return "BK-" . date('Ymd') . "-" . strtoupper(bin2hex(random_bytes(2)));
}

function uploadFile($file, $targetDir)
{
    if (!isset($file) || $file['error'] !== 0) {
        return false;
    }

    $allowed = ['jpg','jpeg','png','pdf'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return false;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $name = uniqid() . "." . $ext;

    $path = $targetDir . $name;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return $name;
    }

    return false;
}

function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function format_indo_date($dateTime, $showTime = false){
    if (empty($dateTime) || $dateTime == '-' || $dateTime == '0000-00-00 00:00:00'){
        return '-';
    }
    try {
        $date = new DateTime($dateTime);

        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $tgl = $date->format('d');
        $bln = (int)$date->format('m');
        $thn = $date->format('Y');
        
        $hasil = "{$tgl} {$bulan[$bln]} {$thn}";

        if ($showTime = True){
            $waktu = $date->format('H:i');
            $hasil .= " | {$waktu}";
        }

        return $hasil;
    } catch (Exception $e) {
        return '-';
    }
}