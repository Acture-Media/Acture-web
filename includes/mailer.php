<?php
require_once dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/PHPMailer/src/SMTP.php';
require_once dirname(__DIR__) . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function getSetting(PDO $pdo, string $key, string $default = ''): string {
    static $cache = [];
    if (!isset($cache[$key])) {
        $stmt = $pdo->prepare("SELECT value FROM site_settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $cache[$key] = $stmt->fetchColumn() ?: $default;
    }
    return $cache[$key];
}

function sendMail(PDO $pdo, string $toEmail, string $toName, string $subject, string $body, string $replyTo = ''): bool {
    $host       = getSetting($pdo, 'smtp_host');
    $port       = (int)getSetting($pdo, 'smtp_port', '587');
    $user       = getSetting($pdo, 'smtp_username');
    $pass       = getSetting($pdo, 'smtp_password');
    $enc        = getSetting($pdo, 'smtp_encryption', 'tls');
    $fromEmail  = getSetting($pdo, 'smtp_from_email', 'noreply@acturemedia.com');
    $fromName   = getSetting($pdo, 'smtp_from_name', 'Acture Media');

    if (!$host || !$user) {
        // Fallback to PHP mail() if SMTP not configured
        $headers = "From: $fromName <$fromEmail>\r\nContent-Type: text/html; charset=UTF-8";
        return @mail($toEmail, $subject, $body, $headers);
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $user;
        $mail->Password   = $pass;
        $mail->SMTPSecure = $enc === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $port;
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail, $toName);
        if ($replyTo) $mail->addReplyTo($replyTo);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('PHPMailer error: ' . $mail->ErrorInfo);
        return false;
    }
}
