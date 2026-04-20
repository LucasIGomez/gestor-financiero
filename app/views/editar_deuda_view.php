<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Deuda - Gestor Financiero</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .form-container {
            max-width: 500px;
            padding: 20px;
            border: 1px solid #ffc107;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            background-color: #ffc107;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #e0a800;
        }

        a {
            text-decoration: none;
            color: #333;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <nav style="background-color: #333; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <a href="index.php?action=dashboard" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Dashboard Financiero</a>
        <a href="index.php?action=deudas" style="color: white; text-decoration: none; font-weight: bold; margin-right: 20px;">Asesor de Deudas</a>
        <a href="index.php?action=inversiones" style="color: white; text-decoration: none; font-weight: bold;">Simulador de Inversiones</a>
    </nav>
    
    <h2>Modificar Registro de Deuda</h2>
    <div class="form-container">
        <form action="index.php?action=actualizar_deuda" method="POST">
            <input type="hidden" name="id_deuda" value="<?= $deuda['id_deuda'] ?>">

            <div class="form-group">
                <label>Nombre de la Deuda:</label>
                <input type="text" name="nombre_deuda" required maxlength="100"
                    value="<?= htmlspecialchars($deuda['nombre_deuda']) ?>">
            </div>

            <div class="form-group">
                <label>Saldo Total Adeudado ($):</label>
                <input type="number" step="0.01" name="saldo_total" required min="0.01"
                    value="<?= $deuda['saldo_total'] ?>">
            </div>

            <div class="form-group">
                <label>Tasa de Interés Nominal Anual (TNA %):</label>
                <input type="number" step="0.01" name="tasa_intereses" required min="0"
                    value="<?= $deuda['tasa_intereses'] ?>">
            </div>

            <div class="form-group">
                <label>Cuota Mensual Mínima ($):</label>
                <input type="number" step="0.01" name="cuota_mensual" required min="0.01"
                    value="<?= $deuda['cuota_mensual'] ?>">
            </div>

            <button type="submit">Actualizar Deuda</button>
            <a href="index.php?action=deudas">Cancelar</a>
        </form>
    </div>
</body>

</html>