<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asesor de Deudas - Gestor Financiero</title>
</head>
<body>
    <nav style="background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <a href="index.php?action=dashboard" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Dashboard Financiero</a>
        <a href="index.php?action=deudas" style="color: white; text-decoration: none; font-weight: bold;">Asesor de Deudas</a>
    </nav>
    
    <h2>Plan de Eliminación de Deudas (Método Avalancha)</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Deuda</th>
                <th>Saldo Total</th>
                <th>Tasa de Interés (TNA)</th>
                <th>Cuota Actual</th>
                <th>Simulación (Pago Extra: $50,000)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deudas as $deuda): ?>
                <tr>
                    <td><?= htmlspecialchars($deuda['nombre_deuda']) ?></td>
                    <td>$<?= number_format($deuda['saldo_total'], 2) ?></td>
                    <td><?= number_format($deuda['tasa_intereses'], 2) ?>%</td>
                    <td>$<?= number_format($deuda['cuota_mensual'], 2) ?></td>
                    <td>
                        <?php 
                            // Ejecutamos el simulador del controlador para cada deuda
                            $simulacion = $controlador->simularPagoExtra(
                                $deuda['saldo_total'], 
                                $deuda['tasa_intereses'], 
                                $deuda['cuota_mensual'], 
                                50000 // Valor hardcodeado para la prueba
                            );
                            
                            if (is_string($simulacion)) {
                                echo $simulacion; // Imprime advertencia de deuda impagable
                            } else {
                                echo "Ahorro de tiempo: " . $simulacion['ahorro_meses'] . " meses<br>";
                                echo "Ahorro en intereses: $" . number_format($simulacion['ahorro_intereses'], 2);
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>