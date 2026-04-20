<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asesor de Deudas - Gestor Financiero</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav { background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        nav a { color: white; text-decoration: none; font-weight: bold; margin-right: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .form-container { margin-bottom: 30px; padding: 20px; border: 1px solid #dc3545; border-radius: 5px; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <nav style="background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <a href="index.php?action=dashboard" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Dashboard Financiero</a>
        <a href="index.php?action=deudas" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Asesor de Deudas</a>
        <a href="index.php?action=inversiones" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Simulador de Inversiones</a>
        <a href="index.php?action=impuestos" style="color: white; text-decoration: none; font-weight: bold;">Calculadora Fiscal</a>
    </nav>

    <h1>Plan de Eliminación de Deudas (Método Avalancha)</h1>

    <div class="form-container">
        <h2>Registrar Nueva Deuda</h2>
        <form action="index.php?action=registrar_deuda" method="POST">
            <div class="form-group">
                <label>Nombre de la Deuda:</label>
                <input type="text" name="nombre_deuda" required maxlength="100" placeholder="Ej. Tarjeta de Crédito, Préstamo Auto">
            </div>
            
            <div class="form-group">
                <label>Saldo Total Adeudado ($):</label>
                <input type="number" step="0.01" name="saldo_total" required min="0.01">
            </div>

            <div class="form-group">
                <label>Tasa de Interés Nominal Anual (TNA %):</label>
                <input type="number" step="0.01" name="tasa_intereses" required min="0">
            </div>

            <div class="form-group">
                <label>Cuota Mensual Mínima ($):</label>
                <input type="number" step="0.01" name="cuota_mensual" required min="0.01">
            </div>

            <button type="submit">Guardar Deuda</button>
        </form>
    </div>

    <h2>Prioridades de Pago</h2>
    <table>
        <thead>
            <tr>
                <th>Deuda</th>
                <th>Saldo Total</th>
                <th>Tasa de Interés (TNA)</th>
                <th>Cuota Actual</th>
                <th>Simulación (Pago Extra: $50,000)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($deudas)): ?>
                <?php foreach ($deudas as $deuda): ?>
                    <tr>
                        <td>
                            <a href="index.php?action=editar_deuda&id=<?= $deuda['id_deuda'] ?>" style="background-color: #ffc107; padding: 5px 10px; text-decoration: none; color: black; border-radius: 3px;">Editar</a>
                        </td>
                        <td><?= htmlspecialchars($deuda['nombre_deuda']) ?></td>
                        <td>$<?= number_format($deuda['saldo_total'], 2) ?></td>
                        <td><?= number_format($deuda['tasa_intereses'], 2) ?>%</td>
                        <td>$<?= number_format($deuda['cuota_mensual'], 2) ?></td>
                        <td>
                            <?php 
                                $simulacion = $controlador->simularPagoExtra(
                                    $deuda['saldo_total'], 
                                    $deuda['tasa_intereses'], 
                                    $deuda['cuota_mensual'], 
                                    50000 
                                );
                                
                                if (is_string($simulacion)) {
                                    echo "<span style='color:red'>" . $simulacion . "</span>"; 
                                } else {
                                    echo "Ahorro de tiempo: " . $simulacion['ahorro_meses'] . " meses<br>";
                                    echo "Ahorro en intereses: <strong style='color:green'>$" . number_format($simulacion['ahorro_intereses'], 2) . "</strong>";
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No tienes deudas registradas. ¡Felicitaciones!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>