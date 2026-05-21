<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

// LOAD ENV FILE
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

function sendMail($to, $subject, $body)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // OTP COOLDOWN
    if (isset($_SESSION["last_otp_sent"])) {

        $secondsPassed = time() - $_SESSION["last_otp_sent"];

        if ($secondsPassed < 30) {

            $remaining = 30 - $secondsPassed;

            return "Please wait {$remaining} seconds before requesting another code.";
        }
    }

    $_SESSION["last_otp_sent"] = time();

    $mail = new PHPMailer(true);

    try {

        // SMTP CONFIG
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'];
        $mail->Password   = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['MAIL_PORT'];

        // SENDER
        $mail->setFrom(
            $_ENV['MAIL_FROM'],
            $_ENV['MAIL_FROM_NAME']
        );

        // RECEIVER
        $mail->addAddress($to);

        // EMAIL CONTENT
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        // SEND
        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log("Mailer Error: " . $mail->ErrorInfo);

        return "Mailer Error: " . $mail->ErrorInfo;
    }
}