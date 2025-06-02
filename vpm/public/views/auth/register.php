<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
    <link rel="stylesheet" href="../../css/auth.css">
</head>
<body class="auth">
<div class="container">
    <h1 class="auth-title">Registro</h1>
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif (isset($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <form class="auth-form" action="../auth/register.php" method="POST">
        <label class="auth-label" for="name">Nombre</label>
        <input class="auth-input" type="text" id="name" name="name" required>

        <label class="auth-label" for="lastname">Apellido</label>
        <input class="auth-input" type="text" id="lastname" name="lastname" required>

        <label class="auth-label" for="email">Correo Electrónico</label>
        <input class="auth-input" type="email" id="email" name="email" required>

        <label class="auth-label" for="password">Contraseña</label>
        <input class="auth-input" type="password" id="password" name="password" required>

        <button class="auth-btn" type="submit">Registrarse</button>
    </form>
    <p class="auth-text">¿Ya tienes una cuenta? <a class="auth-link" href="../auth/login.php">Inicia sesión aquí</a></p>
</div>
</body>
</html>
