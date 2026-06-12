<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — ClariFi</title>
    <link rel="stylesheet" href="app/views/assets/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-sidebar-image">
                <div class="auth-sidebar-content">
                    <h2>Claridad en tus finanzas</h2>
                    <p>Tomá decisiones inteligentes basadas en datos reales. Automatizá cobros, proyectá tus ahorros y optimizá tus deudas en una sola plataforma robusta.</p>
                </div>
            </div>
            <div class="auth-form-side">
                <div class="auth-card">
                    <div class="brand">
                        <h1>ClariFi</h1>
                        <p>Claridad Financiera</p>
                    </div>

                    <h2>Crear Cuenta</h2>

                    <?php if (isset($error)): ?>
                        <div class="alerta"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="index.php?action=registro" method="POST">
                        <div class="form-group">
                            <label>Nombre completo</label>
                            <input type="text" name="nombre" placeholder="Tu nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" name="email" placeholder="tu@email.com" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" name="password" placeholder="Mínimo 6 caracteres" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-success">Crear Cuenta</button>
                    </form>

                    <p class="auth-link">¿Ya tenés cuenta? <a href="index.php?action=login">Iniciar Sesión</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>