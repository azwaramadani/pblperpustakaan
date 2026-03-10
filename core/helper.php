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