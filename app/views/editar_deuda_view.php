<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Deuda - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;}
        button:hover { background-color: #218838; }
        .btn-cancelar { background-color: #dc3545; text-decoration: none; padding: 10px 15px; color: white; border-radius: 4px; font-weight: bold; display: inline-block; text-align: center; }
        .btn-cancelar:hover { background-color: #c82333; }
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

    <div class="form-container">
        <h2>Editar Deuda / Tarjeta</h2>
        <form action="index.php?action=editar_deuda_procesar" method="POST">
            <input type="hidden" name="id_deuda" value="<?= $deuda['id_deuda'] ?>">
            
            <div class="grid-2">
                <div class="form-group">
                    <label>Tipo de Deuda:</label>
                    <select name="tipo_deuda" id="tipo_deuda" required onchange="toggleCamposDinámicos()">
                        <option value="prestamo" <?= $deuda['tipo_deuda'] === 'prestamo' ? 'selected' : '' ?>>Préstamo / Crédito Amortizable</option>
                        <option value="tarjeta_credito" <?= $deuda['tipo_deuda'] === 'tarjeta_credito' ? 'selected' : '' ?>>Tarjeta de Crédito (Revolvente)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nombre del Pasivo:</label>
                    <input type="text" name="nombre_deuda" required value="<?= htmlspecialchars($deuda['nombre_deuda']) ?>">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Saldo Total Adeudado ($):</label>
                    <input type="number" step="0.01" name="saldo_total" required min="0" value="<?= $deuda['saldo_total'] ?>">
                </div>
                <div class="form-group">
                    <label>Cuota Mensual / Pago Mínimo ($):</label>
                    <input type="number" step="0.01" name="cuota_mensual" id="cuota_mensual" min="0" value="<?= $deuda['cuota_mensual'] ?>">
                </div>
            </div>

            <div class="grid-3">
                <div class="form-group">
                    <label>CFT (%) - Prioridad Avalancha:</label>
                    <input type="number" step="0.01" name="cft" required min="0.01" value="<?= $deuda['cft'] ?>">
                </div>
                <div class="form-group">
                    <label>TNA (%) - Opcional:</label>
                    <input type="number" step="0.01" name="tna" min="0" value="<?= $deuda['tna'] ?>">
                </div>
                <div class="form-group">
                    <label>TEA (%) - Opcional:</label>
                    <input type="number" step="0.01" name="tea" min="0" value="<?= $deuda['tea'] ?>">
                </div>
            </div>

            <div id="campos_prestamo" style="background: #f9f9f9; padding: 10px; border-left: 4px solid #007bff; margin-bottom: 15px;">
                <div class="grid-3">
                    <div class="form-group">
                        <label>Cuotas Totales:</label>
                        <input type="number" name="cuotas_totales" min="1" value="<?= $deuda['cuotas_totales'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Cuotas Pagadas:</label>
                        <input type="number" name="cuotas_pagadas" min="0" value="<?= $deuda['cuotas_pagadas'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Día de Vencimiento:</label>
                        <input type="number" name="dia_vencimiento" min="1" max="31" value="<?= $deuda['tipo_deuda'] === 'prestamo' ? $deuda['dia_vencimiento'] : '' ?>">
                    </div>
                </div>
            </div>

            <div id="campos_tarjeta" style="background: #f9f9f9; padding: 10px; border-left: 4px solid #28a745; margin-bottom: 15px; display: none;">
                <div class="grid-3">
                    <div class="form-group">
                        <label>Límite de Crédito ($):</label>
                        <input type="number" step="0.01" name="limite_credito" id="limite_credito" min="0" value="<?= $deuda['limite_credito'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Día de Cierre:</label>
                        <input type="number" name="dia_cierre" min="1" max="31" value="<?= $deuda['dia_cierre'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Día de Vencimiento:</label>
                        <input type="number" name="dia_vencimiento_tarjeta" min="1" max="31" value="<?= $deuda['tipo_deuda'] === 'tarjeta_credito' ? $deuda['dia_vencimiento'] : '' ?>">
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; text-align: right;">
                <a href="index.php?action=deudas" class="btn-cancelar">Cancelar</a>
                <button type="submit">Actualizar Datos</button>
            </div>
        </form>
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
        // Ejecuta el toggle al cargar la página para mostrar los campos correspondientes a lo que ya está guardado en la base de datos
        window.onload = toggleCamposDinámicos;
    </script>
</body>
</html>