<?php
require_once __DIR__ . '/../../../../app/init.php';
require_once __DIR__ . '/../../../../app/helpers/form_helpers.php';
require_once __DIR__ . '/../../../../app/helpers/email_helper.php';

use App\controllers\ClientController;
use App\middleware\AuthMiddleware;

header('Content-Type: text/html; charset=utf-8');
AuthMiddleware::requireRole(['admin']);

$controller = new ClientController();
$regions = $controller->getRegions();
?>
<h2>Clientes <span class="separator">|</span> Enviar Email</h2>

<div id="email-result"></div>

<form id="email-form" class="form ajax-form">
    <div class="card">
        <h3>Información</h3>

        <div class="form-row">
            <label for="region_id">Región</label>
            <select name="region_id" id="region_id" required>
                <option value="">Elegir clientes</option>
                <?php foreach ($regions as $region): ?>
                    <option value="<?= $region['id'] ?>"><?= htmlspecialchars($region['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Para -->
        <div class="form-row">
            <label for="to_client_id">Para</label>
            <select name="to_client_id" id="to_client_id" required>
                <option value="">Seleccione un cliente</option>
            </select>
        </div>

        <!-- CCO -->
        <div class="form-row">
            <label for="cc_client_id">C.C.O.</label>
            <select name="cc_client_id" id="cc_client_id">
                <option value="">Seleccione un alias</option>
            </select>
        </div>

        <div class="form-row">
            <label for="subject">Asunto</label>
            <input type="text" name="subject" id="subject" required placeholder="Escribe el asunto aquí">
        </div>

        <div class="form-row">
            <label for="message">Mensaje</label>
            <textarea name="message" id="message" rows="6" required placeholder="Escribe tu mensaje aquí"></textarea>
        </div>

        <div class="form-actions">
            <button type="submit">Enviar</button>
            <a href="/views/admin/client/list-client.php" class="dynamic-link cancel-btn">Cancelar</a>
        </div>
    </div>
</form>

<script>
    document.getElementById('region_id').addEventListener('change', async function () {
        const regionId = this.value;
        if (!regionId) return;

        const response = await fetch(`/public/api/client/by-region.php?region_id=${regionId}`);
        const clients = await response.json();

        const toSelect = document.getElementById('to_client_id');
        const ccSelect = document.getElementById('cc_client_id');

        toSelect.innerHTML = '<option value="">Seleccione un cliente</option>';
        ccSelect.innerHTML = '<option value="">Seleccione un alias</option>';

        clients.forEach(client => {
            const optionTo = document.createElement('option');
            optionTo.value = client.id;
            optionTo.textContent = `${client.name} (${client.email})`;
            toSelect.appendChild(optionTo);

            const optionCc = document.createElement('option');
            optionCc.value = client.id;
            optionCc.textContent = `${client.alias} (${client.email})`;
            ccSelect.appendChild(optionCc);
        });
    });

    document.getElementById('email-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        document.getElementById('email-result').innerText = result.message;
        if (result.success) {
            this.reset();
        }
    });
</script>