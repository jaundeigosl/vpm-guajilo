<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../css/auth.css">
</head>
<body class="auth">
<div class="container">
    <h1 class="auth-title">Iniciar Sesión</h1>
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form class="auth-form" action="../../auth/login.php" method="POST">
        <label class="auth-label" for="email">Correo Electrónico</label>
        <input class="auth-input" type="email" id="email" name="email" required>

        <label class="auth-label" for="password">Contraseña</label>
        <input class="auth-input" type="password" id="password" name="password" required>

        <button class="auth-btn" type="submit">Iniciar Sesión</button>
    </form>
    <!--    <p class="auth-text">¿No tienes una cuenta? <a class="auth-link" href="../../auth/register.php">Regístrate aquí</a></p>-->
</div>
</body>
</html>
