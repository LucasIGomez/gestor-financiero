<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — ClariFi</title>
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

                    <h2>Iniciá Sesión</h2>

                    <?php if (isset($error)): ?>
                        <div class="alerta"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if (isset($_GET['registrado'])): ?>
                        <div class="exito">¡Cuenta creada con éxito! Ahora podés iniciar sesión.</div>
                    <?php endif; ?>

                    <form action="index.php?action=login" method="POST">
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" name="email" placeholder="tu@email.com" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </form>

                    <p class="auth-link">¿No tenés cuenta? <a href="index.php?action=registro">Registrate</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>