<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Inversiones - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        nav a { color: white; text-decoration: none; font-weight: bold; margin-right: 20px; }
        .form-container { margin-bottom: 30px; padding: 20px; border: 1px solid #28a745; border-radius: 5px; max-width: 600px; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; font-weight: bold;}
        button:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: right; }
        th { background-color: #f4f4f4; text-align: center; }
    </style>
</head>
<body>
    <nav style="background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <a href="index.php?action=dashboard" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Dashboard</a>
        <a href="index.php?action=gastos_recurrentes" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Gastos Fijos</a>
        <a href="index.php?action=deudas" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Deudas</a>
        <a href="index.php?action=inversiones" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Inversiones</a>
        <a href="index.php?action=metas" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Metas de Ahorro</a>
        <a href="index.php?action=impuestos" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Fiscal</a>
        <a href="index.php?action=logout" style="color: #ff4c4c; float: right; text-decoration: none; font-weight: bold;">Cerrar Sesión</a>
    </nav>

    <h1>Proyección de Interés Compuesto</h1>

    <div class="form-container">
        <form action="index.php?action=inversiones" method="POST">
            <div class="form-group">
                <label>Capital Inicial ($):</label>
                <input type="number" step="0.01" name="inversion_inicial" required min="0" value="<?= isset($_POST['inversion_inicial']) ? $_POST['inversion_inicial'] : '10000' ?>">
            </div>
            <div class="form-group">
                <label>Aporte Mensual Adicional ($):</label>
                <input type="number" step="0.01" name="adicion_mensual" required min="0" value="<?= isset($_POST['adicion_mensual']) ? $_POST['adicion_mensual'] : '5000' ?>">
            </div>
            <div class="form-group">
                <label>Tasa de Interés Anual Estimada (%):</label>
                <input type="number" step="0.01" name="tasa_anual" required min="0" value="<?= isset($_POST['tasa_anual']) ? $_POST['tasa_anual'] : '10' ?>">
            </div>
            <div class="form-group">
                <label>Años de Proyección:</label>
                <input type="number" name="anos" required min="1" max="50" value="<?= isset($_POST['anos']) ? $_POST['anos'] : '10' ?>">
            </div>
            <button type="submit">Calcular Proyección</button>
        </form>
    </div>

    <?php if (isset($resultados) && !empty($resultados)): ?>
        <h2>Resultados Año a Año</h2>
        <table>
            <thead>
                <tr>
                    <th>Año</th>
                    <th>Total Aportado (Capital Propio)</th>
                    <th>Interés Compuesto Ganado</th>
                    <th>Saldo Final (Patrimonio Proyectado)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $fila): ?>
                    <tr>
                        <td style="text-align: center; font-weight: bold;"><?= $fila['ano'] ?></td>
                        <td>$<?= number_format($fila['total_aportado'], 2) ?></td>
                        <td style="color: green;">$<?= number_format($fila['interes_ganado'], 2) ?></td>
                        <td style="font-weight: bold; font-size: 1.1em;">$<?= number_format($fila['saldo_final'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>