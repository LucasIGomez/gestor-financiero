<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; margin: 0; }
        .auth-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 15px; }
        input { width: 100%; padding: 10px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .alerta { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Crear Cuenta</h2>
        <?php if (isset($error)) echo "<p class='alerta'>$error</p>"; ?>
        <form action="index.php?action=registro_post" method="POST">
            <div class="form-group"><input type="text" name="nombre" placeholder="Nombre completo" required></div>
            <div class="form-group"><input type="email" name="email" placeholder="Correo Electrónico" required></div>
            <div class="form-group"><input type="password" name="password" placeholder="Contraseña segura" required minlength="6"></div>
            <button type="submit">Registrarse</button>
        </form>
        <p style="text-align: center;"><a href="index.php?action=login">Volver al Login</a></p>
    </div>
</body>
</html>