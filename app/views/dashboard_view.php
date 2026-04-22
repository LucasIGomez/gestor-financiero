<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        /* Barra de Navegación */
        nav { background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        nav a { color: white; text-decoration: none; font-weight: bold; margin-right: 20px; }
        /* Tarjetas de Resumen */
        .resumen-container { display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
        .tarjeta { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); flex: 1; min-width: 150px; text-align: center; }
        .tarjeta h3 { margin-top: 0; font-size: 1.1em; color: #555; }
        .tarjeta p { font-size: 1.5em; font-weight: bold; margin: 0; }
        .ingreso { color: #28a745; }
        .gasto { color: #dc3545; }
        .neutro { color: #007bff; }
        /* Formularios y Tablas */
        .form-container, .tabla-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;}
        button:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>

    <nav>
        <a href="index.php?action=dashboard">Dashboard</a>
        <a href="index.php?action=gastos_recurrentes">Gastos Fijos</a>
        <a href="index.php?action=deudas">Deudas</a>
        <a href="index.php?action=inversiones">Inversiones</a>
        <a href="index.php?action=impuestos">Fiscal</a>
        <a href="index.php?action=logout" style="color: #ff4c4c; float: right;">Cerrar Sesión</a>
    </nav>

    <h2>Dashboard Financiero</h2>

    <?php if (!empty($datos['alertas'])): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <h4 style="margin-top: 0; color: #721c24;">⚠️ Alertas de Comportamiento Financiero</h4>
            <ul style="margin-bottom: 0;">
                <?php foreach ($datos['alertas'] as $alerta): ?>
                    <li><strong><?= $alerta ?></strong></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="resumen-container">
        <div class="tarjeta">
            <h3>Total Ingresos</h3>
            <p class="ingreso">$<?= number_format($datos['ingresos'], 2) ?></p>
        </div>
        <div class="tarjeta">
            <h3>Total Gastos</h3>
            <p class="gasto">$<?= number_format($datos['gastos'], 2) ?></p>
        </div>
        <div class="tarjeta">
            <h3>Flujo de Caja (Liquidez)</h3>
            <p class="<?= $datos['liquidez'] >= 0 ? 'ingreso' : 'gasto' ?>">$<?= number_format($datos['liquidez'], 2) ?></p>
        </div>
        <div class="tarjeta">
            <h3>Pasivos (Deuda Total)</h3>
            <p class="gasto">$<?= number_format($datos['total_deudas'], 2) ?></p>
        </div>
        <div class="tarjeta" style="border: 2px solid <?= $datos['patrimonio_neto'] >= 0 ? '#28a745' : '#dc3545' ?>;">
            <h3>Patrimonio Neto Real</h3>
            <p class="<?= $datos['patrimonio_neto'] >= 0 ? 'ingreso' : 'gasto' ?>">$<?= number_format($datos['patrimonio_neto'], 2) ?></p>
        </div>
    </div>

    <div style="width: 100%; max-width: 600px; margin: 30px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h3 style="text-align: center; color: #333;">Distribución de Gastos</h3>
        <canvas id="graficoGastos"></canvas>
    </div>

    <div class="form-container">
        <h3>Registrar Nueva Transacción</h3>
        <form action="index.php?action=registrar" method="POST">
            <div class="form-group">
                <label>Categoría:</label>
                <select name="id_categoria" required>
                    <optgroup label="Ingresos">
                        <?php foreach ($datos['categorias'] as $categoria): ?>
                            <?php if ($categoria['tipo_flujo'] === 'ingreso'): ?>
                                <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Gastos">
                        <?php foreach ($datos['categorias'] as $categoria): ?>
                            <?php if ($categoria['tipo_flujo'] === 'gasto'): ?>
                                <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>
            <div class="form-group">
                <label>Monto ($):</label>
                <input type="number" step="0.01" name="monto" required min="0.01">
            </div>
            <div class="form-group">
                <label>Descripción:</label>
                <input type="text" name="descripcion" maxlength="255" placeholder="Ej. Compra de supermercado">
            </div>
            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="fecha_transaccion" required value="<?= date('Y-m-d') ?>">
            </div>
            <button type="submit">Guardar Transacción</button>
        </form>
    </div>

    <div class="tabla-container">
        <h3>Historial de Movimientos</h3>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos['transacciones'])): ?>
                    <?php foreach ($datos['transacciones'] as $transaccion): ?>
                        <tr>
                            <td><?= $transaccion['fecha_transaccion'] ?></td>
                            <td><?= htmlspecialchars($transaccion['nombre_categoria']) ?></td>
                            <td><?= htmlspecialchars($transaccion['descripcion']) ?></td>
                            <td class="<?= $transaccion['tipo_flujo'] === 'ingreso' ? 'ingreso' : 'gasto' ?>">
                                <?= ucfirst($transaccion['tipo_flujo']) ?>
                            </td>
                            <td>$<?= number_format($transaccion['monto'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay transacciones registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        const ctx = document.getElementById('graficoGastos').getContext('2d');

        // El motor PHP inyecta el JSON formateado directamente en las variables de JavaScript
        const etiquetas = <?= $datos['grafico_etiquetas'] ?>;
        const valores = <?= $datos['grafico_valores'] ?>;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: etiquetas,
                datasets: [{
                    data: valores,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                        '#9966FF', '#FF9F40', '#C9CBCF', '#E7E9ED'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    </script>
</body>
</html>