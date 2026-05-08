<?php $page_title = 'Inversiones'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="page-header">
        <h2>Proyección de Interés Compuesto</h2>
        <p>Simulá el crecimiento de tu capital con aportes mensuales a lo largo del tiempo</p>
    </div>

    <!-- Formulario -->
    <div class="form-card" style="max-width: 620px;">
        <h3>Parámetros de Simulación</h3>
        <form action="index.php?action=inversiones" method="POST">
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Capital Inicial ($)</label>
                    <input type="number" step="0.01" name="inversion_inicial" required min="0" value="<?= isset($_POST['inversion_inicial']) ? $_POST['inversion_inicial'] : '10000' ?>">
                </div>
                <div class="form-group">
                    <label>Aporte Mensual ($)</label>
                    <input type="number" step="0.01" name="adicion_mensual" required min="0" value="<?= isset($_POST['adicion_mensual']) ? $_POST['adicion_mensual'] : '5000' ?>">
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Tasa Anual Estimada (%)</label>
                    <input type="number" step="0.01" name="tasa_anual" required min="0" value="<?= isset($_POST['tasa_anual']) ? $_POST['tasa_anual'] : '10' ?>">
                </div>
                <div class="form-group">
                    <label>Años de Proyección</label>
                    <input type="number" name="anos" required min="1" max="50" value="<?= isset($_POST['anos']) ? $_POST['anos'] : '10' ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-success">Calcular Proyección</button>
        </form>
    </div>

    <?php if (isset($resultados) && !empty($resultados)): ?>
        <div class="table-card">
            <div class="table-header">
                <h3>Resultados Año a Año</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Año</th>
                        <th>Total Aportado</th>
                        <th>Interés Compuesto</th>
                        <th>Saldo Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $fila): ?>
                        <tr>
                            <td style="text-align: center;"><strong><?= $fila['ano'] ?></strong></td>
                            <td>$<?= number_format($fila['total_aportado'], 2) ?></td>
                            <td class="text-green"><strong>$<?= number_format($fila['interes_ganado'], 2) ?></strong></td>
                            <td><strong style="font-size: 1.05em;">$<?= number_format($fila['saldo_final'], 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php require_once 'app/views/partials/layout_footer.php'; ?>