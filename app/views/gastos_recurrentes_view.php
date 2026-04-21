<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gastos Recurrentes - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4;}
        nav { background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        nav a { color: white; text-decoration: none; font-weight: bold; margin-right: 20px; }
        .form-container { background: white; padding: 20px; border: 1px solid #007bff; border-radius: 5px; margin-bottom: 30px; max-width: 600px;}
        .form-group { margin-bottom: 10px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; font-weight: bold;}
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
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

    <h2>Automatización de Gastos Fijos</h2>

    <div class="form-container">
        <h3>Añadir Nueva Plantilla</h3>
        <form action="index.php?action=registrar_recurrente" method="POST">
            <div class="form-group">
                <label>Categoría del Gasto:</label>
                <select name="id_categoria" required>
                    <?php foreach ($datos['categorias'] as $categoria): ?>
                        <?php if ($categoria['tipo_flujo'] === 'gasto'): ?>
                            <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Monto Fijo ($):</label>
                <input type="number" step="0.01" name="monto" required min="0.01">
            </div>
            <div class="form-group">
                <label>Descripción:</label>
                <input type="text" name="descripcion" required maxlength="255" placeholder="Ej. Alquiler, Seguro Auto">
            </div>
            <div class="form-group">
                <label>Día de Cobro Mensual (1-31):</label>
                <input type="number" name="dia_cobro" required min="1" max="31">
            </div>
            <button type="submit">Guardar Gasto Fijo</button>
        </form>
    </div>

    <h3>Plantillas Activas</h3>
    <table>
        <thead>
            <tr>
                <th>Día de Cobro</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Monto ($)</th>
                <th>Último Procesamiento</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($datos['plantillas'])): ?>
                <?php foreach ($datos['plantillas'] as $plantilla): ?>
                    <tr>
                        <td style="text-align:center; font-weight:bold;"><?= $plantilla['dia_cobro'] ?></td>
                        <td><?= htmlspecialchars($plantilla['nombre_categoria']) ?></td>
                        <td><?= htmlspecialchars($plantilla['descripcion']) ?></td>
                        <td>$<?= number_format($plantilla['monto'], 2) ?></td>
                        <td><?= $plantilla['ultimo_procesamiento'] ? $plantilla['ultimo_procesamiento'] : 'Pendiente' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">No hay gastos fijos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>