<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora Fiscal - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        nav a { color: white; text-decoration: none; font-weight: bold; margin-right: 20px; }
        .form-container { margin-bottom: 30px; padding: 20px; border: 1px solid #17a2b8; border-radius: 5px; max-width: 600px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #17a2b8; color: white; border: none; border-radius: 3px; cursor: pointer; font-weight: bold;}
        button:hover { background-color: #138496; }
        .resultados { padding: 20px; background-color: #f8f9fa; border-left: 5px solid #17a2b8; margin-top: 20px; }
        .alerta { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <nav style="background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <a href="index.php?action=dashboard" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Dashboard</a>
        <a href="index.php?action=gastos_recurrentes" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Gastos Fijos</a>
        <a href="index.php?action=deudas" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Deudas</a>
        <a href="index.php?action=inversiones" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Inversiones</a>
        <a href="index.php?action=impuestos" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Fiscal</a>
        <a href="index.php?action=logout" style="color: #ff4c4c; float: right; text-decoration: none; font-weight: bold;">Cerrar Sesión</a>
    </nav>

    <h1>Automatización Fiscal para Independientes</h1>

    <div class="form-container">
        <form action="index.php?action=impuestos" method="POST">
            <div class="form-group">
                <label>Ingresos Brutos Mensuales ($):</label>
                <input type="number" step="0.01" name="ingresos_brutos" required min="0.01" value="<?= isset($_POST['ingresos_brutos']) ? $_POST['ingresos_brutos'] : '' ?>">
            </div>
            <div class="form-group">
                <label>Gastos Deducibles del Mes ($):</label>
                <input type="number" step="0.01" name="gastos_deducibles" required min="0" value="<?= isset($_POST['gastos_deducibles']) ? $_POST['gastos_deducibles'] : '0' ?>">
            </div>
            
            <h3>Configuración de Alícuotas</h3>
            <div class="form-group">
                <label>Porcentaje IVA (%):</label>
                <input type="number" step="0.01" name="porcentaje_iva" required min="0" value="<?= isset($_POST['porcentaje_iva']) ? $_POST['porcentaje_iva'] : '21' ?>">
            </div>
            <div class="form-group">
                <label>Porcentaje Ingresos Brutos (%):</label>
                <input type="number" step="0.01" name="porcentaje_iibb" required min="0" value="<?= isset($_POST['porcentaje_iibb']) ? $_POST['porcentaje_iibb'] : '3' ?>">
            </div>
            <div class="form-group">
                <label>Porcentaje Impuesto a las Ganancias (% estimación):</label>
                <input type="number" step="0.01" name="porcentaje_ganancias" required min="0" value="<?= isset($_POST['porcentaje_ganancias']) ? $_POST['porcentaje_ganancias'] : '10' ?>">
            </div>
            
            <button type="submit">Calcular Retenciones</button>
        </form>
    </div>

    <?php if (isset($resultados)): ?>
        <?php if (is_string($resultados)): ?>
            <p class="alerta"><?= $resultados ?></p>
        <?php else: ?>
            <div class="resultados">
                <h2>Resumen de Obligaciones Fiscales</h2>
                <p><strong>IVA a pagar:</strong> $<?= number_format($resultados['monto_iva'], 2) ?></p>
                <p><strong>Ingresos Brutos a pagar:</strong> $<?= number_format($resultados['monto_iibb'], 2) ?></p>
                <p><strong>Impuesto a las Ganancias estimado:</strong> $<?= number_format($resultados['monto_ganancias'], 2) ?></p>
                <hr>
                <h3 style="color: #dc3545;">Total Reserva Fiscal a Apartar: $<?= number_format($resultados['reserva_total'], 2) ?></h3>
                <h3 style="color: #28a745;">Ingreso Neto Real (Bolsillo): $<?= number_format($resultados['ingreso_neto_real'], 2) ?></h3>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>