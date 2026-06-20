<?php
require_once 'includes/db.php';
require_once 'includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

$name    = trim(strip_tags($_POST['cfName2']   ?? ''));
$email   = filter_input(INPUT_POST, 'cfEmail2', FILTER_VALIDATE_EMAIL) ?: '';
$phone   = trim(strip_tags($_POST['cfPhone']   ?? ''));
$company = trim(strip_tags($_POST['cfCompany'] ?? ''));
$message = trim(strip_tags($_POST['message']   ?? ''));

if (!$name || !$email || !$message) {
    header('Location: contact.php?sent=error');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO contact_submissions (full_name, email, phone, company, message) VALUES (?,?,?,?,?)");
    $stmt->execute([$name, $email, $phone, $company, $message]);

    // Notify admin via SMTP
    $notifyTo = getSetting($pdo, 'smtp_notify_email') ?: getSetting($pdo, 'smtp_from_email') ?: 'info@acturemedia.com';
    $adminBody = "
        <h3>New Contact Submission</h3>
        <table style='border-collapse:collapse;font-family:sans-serif;font-size:14px'>
            <tr><td style='padding:6px 12px;color:#888'>Name</td><td style='padding:6px 12px'><b>" . h($name) . "</b></td></tr>
            <tr><td style='padding:6px 12px;color:#888'>Email</td><td style='padding:6px 12px'><a href='mailto:" . h($email) . "'>" . h($email) . "</a></td></tr>
            <tr><td style='padding:6px 12px;color:#888'>Phone</td><td style='padding:6px 12px'>" . h($phone ?: '—') . "</td></tr>
            <tr><td style='padding:6px 12px;color:#888'>Company</td><td style='padding:6px 12px'>" . h($company ?: '—') . "</td></tr>
            <tr><td style='padding:6px 12px;color:#888;vertical-align:top'>Message</td><td style='padding:6px 12px'>" . nl2br(h($message)) . "</td></tr>
        </table>
        <p style='margin-top:16px'><a href='/admin/contacts.php'>View in Admin →</a></p>
    ";
    sendMail($pdo, $notifyTo, 'Acture Admin', "New Contact: $name", $adminBody, $email);

    // Auto-reply to sender
    $siteName = getSetting($pdo, 'site_name', 'Acture Media');
    $replyBody = "
        <p>Hi " . h($name) . ",</p>
        <p>Thank you for reaching out to $siteName. We've received your message and will get back to you within 1–2 business days.</p>
        <p style='color:#888;font-size:13px'>This is an automated confirmation. Please don't reply to this email.</p>
        <p>— The $siteName Team</p>
    ";
    sendMail($pdo, $email, $name, "We received your message — $siteName", $replyBody);

    header('Location: contact.php?sent=1');
} catch (PDOException $e) {
    header('Location: contact.php?sent=error');
}
exit;
