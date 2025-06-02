<?php
require_once __DIR__ . '/../../../app/init.php';

\App\middleware\AuthMiddleware::requireRole(['admin', 'cliente']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/sidebar.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/tables.css">
    <link rel="stylesheet" href="../../css/forms.css">
    <title>Dashboard Admin</title>
</head>

<body>

<div class="app-wrapper">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <div class="content-wrapper">
        <header class="header">
            <h1>Sistema de Gestión de Órdenes</h1>
            <?php include __DIR__ . '/../components/menu.php'; ?>
        </header>

        <main class="main-content" id="main-content">
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js" ></script>
<script src="../../js/main.js"></script>
<script src="../../js/forms.js"></script>
</body>
</html>
