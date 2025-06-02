<?php
require_once __DIR__ . '/../../../../app/init.php';

use App\controllers\ClientController;
use App\lib\Mailer;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new ClientController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regionId = $_POST['region_id'] ?? null;
    $bccList = explode(',', $_POST['bcc'] ?? '');
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!$regionId || !$subject || !$message) {
        echo "<p class='error'>Faltan datos para enviar el correo.</p>";
        return;
    }

    $clients = $controller->getClientsByRegion($regionId);

    $toEmails = array_map(fn($c) => $c->email, $clients);

    // Usar tu helper de Mailer o mail()
    foreach ($toEmails as $email) {
        mail($email, $subject, $message, "From: no-reply@tusitio.com\r\nBCC: " . implode(',', $bccList));
    }

    echo "<p class='success'>¡Correos enviados exitosamente! 📨</p>";

    require __DIR__ . '/list-client.php';
}
?>