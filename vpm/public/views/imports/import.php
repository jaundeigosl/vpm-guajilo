<?php
require_once __DIR__ . '/../../../app/init.php';

use App\controllers\ImportController;
use App\middleware\AuthMiddleware;
use PhpOffice\PhpSpreadsheet\IOFactory;

AuthMiddleware::requireRole(['admin']);

$controller = new ImportController();
$error = '';
$success = false;
$fileAlreadyExist = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excelFile'])) {
    $filePath = $_FILES['excelFile']['tmp_name'];
    $uploadDir = __DIR__ . '/uploads/';
    $uploadedFile = $uploadDir . basename($_FILES['excelFile']['name']);

    // Verificar que la carpeta existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $files = scandir($uploadDir);
    $files = array_diff($files, array('.', '..'));

    $fileAlreadyExist = in_array(basename($uploadedFile),$files) ? true : false;

    // Copiar el archivo
    if (!copy($filePath, $uploadedFile)) {
        die("Error: No se pudo copiar el archivo.");
    }

    // Verificar que el archivo tenga contenido
    if (!file_exists($uploadedFile) || filesize($uploadedFile) === 0) {
        die("Error: El archivo está vacío.");
    }

    // Verificar que es un archivo .xlsx válido
    $fileType = mime_content_type($uploadedFile);
    if ($fileType !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        die("Error: El archivo no es un archivo Excel (.xlsx) válido. Tipo detectado: $fileType");
    }

    // Intentar leer el archivo

    $controller->importExcel($uploadedFile, $fileAlreadyExist);
    
}
?>

<h2>Importación de Archivos Excel</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success">Archivo importado con éxito.</div>
<?php endif; ?>


<form id="importForm" method="POST" action="/views/imports/import.php" enctype="multipart/form-data"
      class="form ajax-form">
    <div class="card">
        <h3>Selecciona el archivo a importar</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="excelFile">Archivo Excel:</label>
                <input type="file" name="excelFile" id="excelFile" accept=".xlsx, .xls" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit">Importar</button>
            <a href="/views/import/list-imports.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>

<div id="loadingMessage" style="display: none; margin-top: 20px;">
    <div class="alert alert-info">⏳ Cargando archivo... Por favor espera.</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("importForm");
        const loadingMessage = document.getElementById("loadingMessage");

        form.addEventListener("submit", async function (e) {
            e.preventDefault();
            loadingMessage.style.display = "block";

            const formData = new FormData(form);
            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData
                });

                const text = await response.text();
                loadingMessage.style.display = "none";
                document.body.innerHTML = text;
            } catch (error) {
                loadingMessage.style.display = "none";
                alert("Error al cargar el archivo: " + error.message);
            }
        });
    });
</script>