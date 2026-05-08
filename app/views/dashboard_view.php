<?php $page_title = 'Dashboard'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="page-header">
        <h2>Dashboard Financiero</h2>
        <p>Resumen del mes actual — <?php
            if (class_exists('IntlDateFormatter')) {
                $fmt = new IntlDateFormatter('es_AR', IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'MMMM yyyy');
                echo ucfirst($fmt->format(new DateTime()));
            } else {
                $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                echo $meses[date('n')-1] . ' ' . date('Y');
            }
        ?></p>
    </div>

    <!-- Alertas de comportamiento financiero -->
    <?php if (!empty($datos['alertas'])): ?>
        <div class="alert alert-warning">
            <h4>⚠️ Alertas de Comportamiento Financiero</h4>
            <ul>
                <?php foreach ($datos['alertas'] as $alerta): ?>
                    <li><?= $alerta ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Tarjetas de Resumen -->
    <div class="stats-grid">
        <div class="stat-card highlight">
            <div class="card-title">Límite Diario Seguro</div>
            <div class="card-value <?= $datos['limite_diario_seguro'] > 0 ? 'text-green' : 'text-red' ?>">
                $<?= number_format($datos['limite_diario_seguro'], 2) ?>
            </div>
            <div class="card-subtitle">Por los próximos <?= $datos['dias_restantes'] ?> días</div>
        </div>
        <div class="stat-card">
            <div class="card-title">Ingresos del Mes</div>
            <div class="card-value text-green">$<?= number_format($datos['ingresos'], 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="card-title">Gastos del Mes</div>
            <div class="card-value text-red">$<?= number_format($datos['gastos'], 2) ?></div>
        </div>
        <div class="stat-card">
            <div class="card-title">Liquidez Restante</div>
            <div class="card-value <?= $datos['liquidez'] >= 0 ? 'text-green' : 'text-red' ?>">
                $<?= number_format($datos['liquidez'], 2) ?>
            </div>
        </div>
        <div class="stat-card">
            <div class="card-title">Pasivos Totales</div>
            <div class="card-value text-red">$<?= number_format($datos['total_deudas'], 2) ?></div>
        </div>
        <div class="stat-card <?= $datos['patrimonio_neto'] >= 0 ? 'positive' : 'negative' ?>">
            <div class="card-title">Patrimonio Neto</div>
            <div class="card-value <?= $datos['patrimonio_neto'] >= 0 ? 'text-green' : 'text-red' ?>">
                $<?= number_format($datos['patrimonio_neto'], 2) ?>
            </div>
        </div>
    </div>

    <!-- Gráfico de Gastos -->
    <div class="chart-card" style="margin: 0 auto 28px auto;">
        <h3>Distribución de Gastos (Este Mes)</h3>
        <canvas id="graficoGastos"></canvas>
    </div>

    <!-- Formulario de Nueva Transacción -->
    <div class="form-card">
        <h3>Registrar Nueva Transacción</h3>
        <form action="index.php?action=registrar" method="POST">
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="id_categoria" required>
                        <optgroup label="── Ingresos ──">
                            <?php foreach ($datos['categorias'] as $categoria): ?>
                                <?php if ($categoria['tipo_flujo'] === 'ingreso'): ?>
                                    <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="── Gastos ──">
                            <?php foreach ($datos['categorias'] as $categoria): ?>
                                <?php if ($categoria['tipo_flujo'] === 'gasto'): ?>
                                    <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group">
                    <label>Método de Pago</label>
                    <select name="id_deuda">
                        <option value="">Efectivo / Débito</option>
                        <?php if (!empty($datos['tarjetas'])): ?>
                            <optgroup label="── Tarjetas de Crédito ──">
                                <?php foreach ($datos['tarjetas'] as $tarjeta): ?>
                                    <option value="<?= $tarjeta['id_deuda'] ?>"><?= htmlspecialchars($tarjeta['nombre_deuda']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Monto ($)</label>
                    <input type="number" step="0.01" name="monto" required min="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha_transaccion" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" maxlength="255" placeholder="Ej. Compra de supermercado">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Transacción</button>
        </form>
    </div>

    <!-- Historial de Movimientos -->
    <div class="table-card">
        <div class="table-header">
            <h3>Historial de Movimientos</h3>
        </div>
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
                            <td><?= date('d/m/Y', strtotime($transaccion['fecha_transaccion'])) ?></td>
                            <td><?= htmlspecialchars($transaccion['nombre_categoria']) ?></td>
                            <td><?= htmlspecialchars($transaccion['descripcion']) ?></td>
                            <td>
                                <span class="<?= $transaccion['tipo_flujo'] === 'ingreso' ? 'text-green' : 'text-red' ?>">
                                    <?= ucfirst($transaccion['tipo_flujo']) ?>
                                </span>
                            </td>
                            <td><strong>$<?= number_format($transaccion['monto'], 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">No hay transacciones registradas este mes.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('graficoGastos').getContext('2d');
        const etiquetas = <?= $datos['grafico_etiquetas'] ?>;
        const valores = <?= $datos['grafico_valores'] ?>;

        Chart.defaults.color = '#9ca3af';
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: etiquetas,
                datasets: [{
                    data: valores,
                    backgroundColor: [
                        '#6366f1', '#06b6d4', '#f59e0b', '#10b981',
                        '#ef4444', '#a78bfa', '#ec4899', '#8b5cf6'
                    ],
                    borderColor: '#1c1e2b',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right', labels: { padding: 16, usePointStyle: true } }
                },
                cutout: '65%'
            }
        });
    </script>

<?php require_once 'app/views/partials/layout_footer.php'; ?>