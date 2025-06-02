<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

function sendEmail($to, $subject, $body, $cc = null): bool
{
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = getenv('MAIL_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('MAIL_USERNAME');
        $mail->Password   = getenv('MAIL_PASSWORD');
        $mail->SMTPSecure = getenv('MAIL_ENCRYPTION') ?: 'tls';
        $mail->Port       = getenv('MAIL_PORT');

        // Datos del remitente
        $mail->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));

        // Destinatario
        $mail->addAddress($to);

        // CCO
        if ($cc) {
            $mail->addBCC($cc);
        }

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Error al enviar correo: ' . $mail->ErrorInfo);
        return false;
    }
}