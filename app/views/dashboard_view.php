<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .resumen { display: flex; gap: 20px; margin-bottom: 30px; }
        .tarjeta { border: 1px solid #ccc; padding: 20px; border-radius: 5px; min-width: 200px; }
        .ingreso { color: green; font-weight: bold; }
        .gasto { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .form-container { margin-bottom: 30px; padding: 20px; border: 1px solid #007bff; border-radius: 5px; }
        .form-group { margin-bottom: 10px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <h1>Dashboard Financiero</h1>

    <div class="resumen">
        <div class="tarjeta">
            <h3>Ingresos Históricos</h3>
            <p class="ingreso">$<?= number_format($datos['ingresos'], 2) ?></p>
        </div>
        <div class="tarjeta">
            <h3>Gastos Históricos</h3>
            <p class="gasto">$<?= number_format($datos['gastos'], 2) ?></p>
        </div>
        <div class="tarjeta" style="background-color: #e9ecef;">
            <h3>Liquidez Disponible</h3>
            <p><strong>$<?= number_format($datos['liquidez'], 2) ?></strong></p>
        </div>
        <div class="tarjeta" style="border-color: #dc3545;">
            <h3 style="color: #dc3545;">Pasivos (Deuda Total)</h3>
            <p class="gasto">-$<?= number_format($datos['total_deudas'], 2) ?></p>
        </div>
        <div class="tarjeta" style="background-color: <?= $datos['patrimonio_neto'] >= 0 ? '#d4edda' : '#f8d7da' ?>; border-color: <?= $datos['patrimonio_neto'] >= 0 ? '#c3e6cb' : '#f5c6cb' ?>;">
            <h3>Patrimonio Neto Real</h3>
            <p style="color: <?= $datos['patrimonio_neto'] >= 0 ? 'green' : 'red' ?>;">
                <strong>$<?= number_format($datos['patrimonio_neto'], 2) ?></strong>
            </p>
        </div>
    </div>

    <div style="width: 100%; max-width: 600px; margin: 30px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h3 style="text-align: center; color: #333;">Distribución de Gastos</h3>
        <canvas id="graficoGastos"></canvas>
    </div>

    <div class="form-container">
        <h2>Registrar Nueva Transacción</h2>
        <form action="index.php?action=registrar" method="POST">
            <div class="form-group">
                <label>Categoría:</label>
                <select name="id_categoria" required>
                    <option value="">Seleccione una categoría...</option>
                    <?php foreach ($datos['categorias'] as $cat): ?>
                        <option value="<?= $cat['id_categoria'] ?>">
                            <?= htmlspecialchars($cat['nombre_categoria']) ?> (<?= strtoupper($cat['tipo_flujo']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Monto ($):</label>
                <input type="number" step="0.01" name="monto" required min="0.01">
            </div>

            <div class="form-group">
                <label>Descripción (Opcional):</label>
                <input type="text" name="descripcion" maxlength="255">
            </div>

            <div class="form-group">
                <label>Fecha:</label>
                <input type="date" name="fecha_transaccion" required value="<?= date('Y-m-d') ?>">
            </div>

            <button type="submit">Guardar Transacción</button>
        </form>
    </div>

    <h2>Historial de Movimientos</h2>
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
            <?php foreach ($datos['transacciones'] as $t): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($t['fecha_transaccion'])) ?></td>
                    <td><?= htmlspecialchars($t['nombre_categoria']) ?></td>
                    <td><?= htmlspecialchars($t['descripcion']) ?></td>
                    <td><?= strtoupper($t['tipo_flujo']) ?></td>
                    <td class="<?= $t['tipo_flujo'] === 'ingreso' ? 'ingreso' : 'gasto' ?>">
                        $<?= number_format($t['monto'], 2) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script>
        const ctx = document.getElementById('graficoGastos').getContext('2d');

        // El motor PHP inyecta el JSON formateado directamente en las variables de JavaScript
        const etiquetas = <?= $datos['grafico_etiquetas'] ?>;
        const valores = <?= $datos['grafico_valores'] ?>;

        new Chart(ctx, {
            type: 'doughnut', // Define un gráfico de anillo moderno
            data: {
                labels: etiquetas,
                datasets: [{
                    data: valores,
                    backgroundColor: [
                        '#FF6384', // Rojo/Rosa
                        '#36A2EB', // Azul
                        '#FFCE56', // Amarillo
                        '#4BC0C0', // Turquesa
                        '#9966FF', // Violeta
                        '#FF9F40', // Naranja
                        '#C9CBCF'  // Gris
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right', // Coloca las etiquetas a la derecha del gráfico
                    }
                }
            }
        });
    </script>

</body>
</html>