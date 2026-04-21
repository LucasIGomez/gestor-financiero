<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; margin: 0; }
        .auth-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 15px; }
        input { width: 100%; padding: 10px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .alerta { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Ingreso al Sistema</h2>
        <?php if (isset($error)) echo "<p class='alerta'>$error</p>"; ?>
        <form action="index.php?action=login_post" method="POST">
            <div class="form-group"><input type="email" name="email" placeholder="Correo Electrónico" required></div>
            <div class="form-group"><input type="password" name="password" placeholder="Contraseña" required></div>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <p style="text-align: center;"><a href="index.php?action=registro">¿No tienes cuenta? Regístrate</a></p>
    </div>
</body>
</html>