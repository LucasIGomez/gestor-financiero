<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metas de Ahorro - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        nav { background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        nav a { color: white; text-decoration: none; font-weight: bold; margin-right: 15px; }
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px; max-width: 600px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;}
        button:hover { background-color: #218838; }
        .metas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .meta-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-left: 5px solid #007bff; }
        .meta-card h3 { margin-top: 0; color: #333; }
        .barra-fondo { width: 100%; background-color: #e9ecef; border-radius: 5px; overflow: hidden; margin: 15px 0; }
        .barra-progreso { height: 20px; background-color: #007bff; text-align: center; color: white; font-size: 12px; line-height: 20px; transition: width 0.5s; }
        .meta-stats { display: flex; justify-content: space-between; font-size: 0.9em; margin-bottom: 15px; color: #555; }
        .form-ahorro { display: flex; gap: 10px; }
        .form-ahorro input { flex: 1; }
        .btn-ahorro { background-color: #007bff; }
        .btn-ahorro:hover { background-color: #0056b3; }
        .completada { border-left-color: #28a745; }
        .completada .barra-progreso { background-color: #28a745; }
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

    <h2>Gestión de Metas y Fondo de Emergencia</h2>

    <div class="form-container">
        <h3>Definir Nueva Meta</h3>
        <form action="index.php?action=registrar_meta" method="POST">
            <div class="form-group">
                <label>Nombre del Objetivo:</label>
                <input type="text" name="nombre_meta" required maxlength="100" placeholder="Ej. Fondo de Emergencia 6 Meses">
            </div>
            <div class="form-group">
                <label>Monto a Alcanzar ($):</label>
                <input type="number" step="0.01" name="monto_objetivo" required min="0.01">
            </div>
            <div class="form-group">
                <label>Fecha Límite:</label>
                <input type="date" name="fecha_limite" required>
            </div>
            <button type="submit">Crear Meta</button>
        </form>
    </div>

    <h3>Tus Objetivos Financieros</h3>
    <div class="metas-grid">
        <?php if (!empty($metas)): ?>
            <?php foreach ($metas as $meta): ?>
                <div class="meta-card <?= $meta['porcentaje_avance'] >= 100 ? 'completada' : '' ?>">
                    <h3><?= htmlspecialchars($meta['nombre_meta']) ?></h3>
                    
                    <div class="meta-stats">
                        <span>Ahorrado: $<?= number_format($meta['saldo_actual'], 2) ?></span>
                        <span>Objetivo: $<?= number_format($meta['monto_objetivo'], 2) ?></span>
                    </div>

                    <div class="barra-fondo">
                        <div class="barra-progreso" style="width: <?= $meta['porcentaje_avance'] ?>%;">
                            <?= $meta['porcentaje_avance'] ?>%
                        </div>
                    </div>

                    <div class="meta-stats">
                        <span>Vence: <?= date('d/m/Y', strtotime($meta['fecha_limite'])) ?></span>
                    </div>

                    <?php if ($meta['porcentaje_avance'] < 100): ?>
                        <form action="index.php?action=agregar_ahorro" method="POST" class="form-ahorro">
                            <input type="hidden" name="id_meta" value="<?= $meta['id_meta'] ?>">
                            <input type="number" step="0.01" name="monto_deposito" required min="0.01" placeholder="Aportar ($)">
                            <button type="submit" class="btn-ahorro">Ahorrar</button>
                        </form>
                    <?php else: ?>
                        <p style="color: #28a745; font-weight: bold; text-align: center; margin: 0;">¡Meta Completada!</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes metas financieras activas. ¡Comienza a planificar tu futuro!</p>
        <?php endif; ?>
    </div>

</body>
</html>