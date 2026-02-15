<?php
/**
 * Contact Form Handler
 * Uses environment variables from .env file
 */

// Load environment variables
require_once __DIR__ . '/config/env_loader.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Rohdaten auslesen
$rawInput = file_get_contents('php://input');

// JSON dekodieren
$data = json_decode($rawInput, true);

// Werte extrahieren mit Fallback
$name = isset($data['name']) ? $data['name'] : null;
$email = isset($data['email'])  ? $data['email'] : null;
$message = isset($data['message'])  ? $data['message'] : null;

if (!$name || !$email || !$message) {
    $response = [
        'status' => 'error',
        'message' => 'wrong usage',
    ];
    echo json_encode($response);
    return;
}

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings from environment variables
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'mail.your-server.de';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USERNAME') ?: 'contact@samuel-mencke.com';
    $mail->Password   = getenv('SMTP_PASSWORD') ?: '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = getenv('SMTP_PORT') ?: 587;

    //Recipients from environment variables
    $mail->setFrom(
        getenv('MAIL_FROM_ADDRESS') ?: 'contact@samuel-mencke.com',
        getenv('MAIL_FROM_NAME') ?: 'Kontaktanfrage - Samuel Mencke'
    );
    $mail->addAddress(
        getenv('MAIL_TO_ADDRESS') ?: 'mail@samuel-mencke.com',
        getenv('MAIL_TO_NAME') ?: 'Samuel Mencke'
    );
    $mail->addReplyTo($email, $name);

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'Portfolio - contact';
    $mail->Body    = '<h1>Nachricht</h1><p>'.$message.'</p>';
    $mail->AltBody = 'Nachricht: '.$message;

    $mail->send();
    $response = [
        'status' => 'success',
        'message' => 'Message has been sent',
    ];
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}",
    ];
}

echo json_encode($response);
return;
