<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Deudas - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .form-container, .tabla-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;}
        button:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #dc3545; color: white; }
        .ahorro { color: #28a745; font-weight: bold; }
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

    <h2>Asesor de Prioridades de Deuda (Método Avalancha)</h2>

    <div class="form-container">
        <h3>Registrar Nueva Deuda / Tarjeta</h3>
        <form action="index.php?action=registrar_deuda" method="POST">
            
            <div class="grid-2">
                <div class="form-group">
                    <label>Tipo de Deuda:</label>
                    <select name="tipo_deuda" id="tipo_deuda" required onchange="toggleCamposDinámicos()">
                        <option value="prestamo">Préstamo / Crédito Amortizable</option>
                        <option value="tarjeta_credito">Tarjeta de Crédito (Revolvente)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nombre del Pasivo:</label>
                    <input type="text" name="nombre_deuda" required placeholder="Ej. Visa Galicia o Préstamo Auto">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Saldo Total Adeudado ($):</label>
                    <input type="number" step="0.01" name="saldo_total" required min="0">
                </div>
                <div class="form-group">
                    <label>Cuota Mensual / Pago Mínimo ($):</label>
                    <input type="number" step="0.01" name="cuota_mensual" id="cuota_mensual" min="0">
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>CFT (%) - Prioridad Avalancha:</label>
                    <input type="number" step="0.01" name="cft" required min="0.01" title="Costo Financiero Total">
                </div>
                <div class="form-group">
                    <label>TNA (%) - Opcional:</label>
                    <input type="number" step="0.01" name="tna" min="0">
                </div>
                <div class="form-group">
                    <label>TEA (%) - Opcional:</label>
                    <input type="number" step="0.01" name="tea" min="0">
                </div>
            </div>

            <div id="campos_prestamo" style="background: #f9f9f9; padding: 10px; border-left: 4px solid #007bff; margin-bottom: 15px;">
                <div class="grid-3">
                    <div class="form-group">
                        <label>Cuotas Totales:</label>
                        <input type="number" name="cuotas_totales" min="1">
                    </div>
                    <div class="form-group">
                        <label>Cuotas Pagadas:</label>
                        <input type="number" name="cuotas_pagadas" min="0">
                    </div>
                    <div class="form-group">
                        <label>Día de Vencimiento:</label>
                        <input type="number" name="dia_vencimiento" min="1" max="31">
                    </div>
                </div>
            </div>

            <div id="campos_tarjeta" style="background: #f9f9f9; padding: 10px; border-left: 4px solid #28a745; margin-bottom: 15px; display: none;">
                <div class="grid-3">
                    <div class="form-group">
                        <label>Límite de Crédito ($):</label>
                        <input type="number" step="0.01" name="limite_credito" id="limite_credito" min="0">
                    </div>
                    <div class="form-group">
                        <label>Día de Cierre:</label>
                        <input type="number" name="dia_cierre" min="1" max="31">
                    </div>
                    <div class="form-group">
                        <label>Día de Vencimiento:</label>
                        <input type="number" name="dia_vencimiento_tarjeta" min="1" max="31">
                    </div>
                </div>
            </div>

            <button type="submit">Guardar Deuda / Tarjeta</button>
        </form>
    </div>

    <div class="tabla-container">
        <h3>Prioridades de Pago (Ordenadas por CFT)</h3>
        <p style="font-size: 0.9em; color: #555;">La tabla simula el impacto de un pago adicional de $50,000 en la deuda de mayor costo.</p>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Saldo Total</th>
                    <th>CFT (%)</th>
                    <th>Cuota Mensual</th>
                    <th>Simulación (+50k)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos)): ?>
                    <?php foreach ($datos as $deuda): ?>
                        <tr>
                            <td><?= htmlspecialchars($deuda['nombre_deuda']) ?></td>
                            <td><?= $deuda['tipo_deuda'] === 'tarjeta_credito' ? 'Tarjeta' : 'Préstamo' ?></td>
                            <td>$<?= number_format($deuda['saldo_total'], 2) ?></td>
                            <td style="font-weight: bold; color: #dc3545;"><?= number_format($deuda['cft'], 2) ?>%</td>
                            <td>$<?= number_format($deuda['cuota_mensual'], 2) ?></td>
                            <td>
                                <?php if ($deuda['meses_ahorrados'] > 0): ?>
                                    <span class="ahorro">Ahorras <?= round($deuda['meses_ahorrados']) ?> meses</span><br>
                                    <span class="ahorro">Evitas $<?= number_format($deuda['intereses_ahorrados'], 2) ?> en int.</span>
                                <?php else: ?>
                                    <span style="color: #666;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?action=editar_deuda&id=<?= $deuda['id_deuda'] ?>" style="color: #007bff; text-decoration: none;">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No tienes deudas registradas. ¡Excelente!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleCamposDinámicos() {
            const tipo = document.getElementById('tipo_deuda').value;
            const camposPrestamo = document.getElementById('campos_prestamo');
            const camposTarjeta = document.getElementById('campos_tarjeta');
            
            if (tipo === 'prestamo') {
                camposPrestamo.style.display = 'block';
                camposTarjeta.style.display = 'none';
            } else {
                camposPrestamo.style.display = 'none';
                camposTarjeta.style.display = 'block';
            }
        }
        // Ejecutar al cargar la página
        window.onload = toggleCamposDinámicos;
    </script>
</body>
</html>