<?php $page_title = 'Dashboard'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="header-actions-row">
        <div class="page-header" style="margin-bottom: 0;">
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
        <button type="button" class="info-btn" onclick="toggleAyuda()"><i class="fa-solid fa-circle-info"></i> Conceptos del Módulo</button>
    </div>

    <!-- Guía conceptual integrada (Ayuda interactiva) -->
    <div class="help-card" id="ayudaDashboard">
        <h4><i class="fa-solid fa-graduation-cap"></i> Educación Financiera: Conceptos Clave</h4>
        <div class="help-grid">
            <div class="help-item">
                <strong>Límite Diario Seguro</strong>
                Calcula cuánto podés gastar hoy sin comprometer tu capacidad de pago futura. Resta tus gastos mensuales fijos y deudas del ingreso total, y divide el remanente por los días restantes del mes.
            </div>
            <div class="help-item">
                <strong>Patrimonio Neto</strong>
                Representa tu riqueza real. Se calcula restando todos tus pasivos (deudas, saldo de tarjetas de crédito) a tus activos (liquidez en efectivo y metas de ahorro).
            </div>
            <div class="help-item">
                <strong>Liquidez Restante</strong>
                Es el dinero disponible que te queda en el mes actual (Ingresos menos Gastos). No incluye los ahorros ya apartados para metas a largo plazo.
            </div>
            <div class="help-item">
                <strong>Ingresos Brutos vs Netos</strong>
                El Bruto es el monto percibido antes de deducciones e impuestos. El Neto es el dinero real disponible de bolsillo, libre de cargas impositivas, que debés usar para tu planificación.
            </div>
        </div>
    </div>

    <!-- Alertas de comportamiento financiero -->
    <?php if (!empty($datos['alertas'])): ?>
        <div class="alert alert-warning">
            <h4><i class="fa-solid fa-triangle-exclamation"></i> Alertas de Comportamiento Financiero</h4>
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

    <!-- Sección de Gráficos -->
    <div class="form-grid-2" style="margin-bottom: 28px;">
        <div class="card">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 16px; text-align: center;">Distribución de Gastos (Este Mes)</h3>
            <div style="max-height: 250px; position: relative; display: flex; justify-content: center; align-items: center;">
                <canvas id="graficoGastos" style="max-height: 250px;"></canvas>
            </div>
        </div>
        <div class="card">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 16px; text-align: center;">Trayectoria de Patrimonio y Tasa de Ahorro</h3>
            <div style="max-height: 250px; position: relative;">
                <canvas id="graficoTrayectoria" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Sección de Presupuestos -->
    <div class="card" style="margin-bottom: 28px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
            <h3 style="font-size: 1.15rem; font-weight: 600;">Límites de Presupuestos por Categoría</h3>
            
            <!-- Formulario Rápido de Presupuesto -->
            <form action="index.php?action=registrar_presupuesto" method="POST" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <select name="id_categoria" required style="width: auto; padding: 6px 12px; font-size: 0.85rem;">
                    <option value="" disabled selected>Seleccionar Categoría</option>
                    <?php foreach ($datos['categorias'] as $categoria): ?>
                        <?php if ($categoria['tipo_flujo'] === 'gasto'): ?>
                            <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <input type="number" step="0.01" name="monto_limite" required placeholder="Límite ($)" style="width: 120px; padding: 6px 12px; font-size: 0.85rem;">
                <button type="submit" class="btn btn-primary btn-sm">Establecer Límite</button>
            </form>
        </div>

        <div class="budget-grid">
            <?php if (!empty($datos['presupuestos'])): ?>
                <?php foreach ($datos['presupuestos'] as $presupuesto): ?>
                    <?php
                    $limite = (float)$presupuesto['monto_limite'];
                    $gastado = (float)$presupuesto['monto_gastado'];
                    $porcentaje = $limite > 0 ? ($gastado / $limite) * 100 : 0;
                    
                    // Semáforo color classes
                    $fill_class = 'normal';
                    $badge = '';
                    if ($limite > 0) {
                        if ($porcentaje >= 80 && $porcentaje <= 100) {
                            $fill_class = 'warning';
                        } elseif ($porcentaje > 100) {
                            $fill_class = 'danger';
                            $badge = '<span class="priority-badge high" style="padding: 1px 6px; font-size: 0.65rem;">Excedido</span>';
                        }
                    }
                    ?>
                    <div class="budget-card">
                        <div class="budget-info">
                            <span style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($presupuesto['nombre_categoria']) ?> <?= $badge ?></span>
                            <span style="color: var(--text-secondary);">
                                $<?= number_format($gastado, 2) ?> / 
                                <?= $limite > 0 ? '$' . number_format($limite, 2) : '<span class="text-muted" style="font-style: italic;">Sin límite</span>' ?>
                            </span>
                        </div>
                        <div class="budget-bar-track">
                            <div class="budget-bar-fill <?= $fill_class ?>" style="width: min(100, <?= min($porcentaje, 100) ?>)%;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted);">
                            <span><?= $limite > 0 ? round($porcentaje) . '% de límite' : '0% usado' ?></span>
                            <?php if ($limite > 0): ?>
                                <span><?= $gastado > $limite ? 'Excedido por $' . number_format($gastado - $limite, 2) : 'Quedan $' . number_format($limite - $gastado, 2) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 16px;">No hay categorías de gastos configuradas.</p>
            <?php endif; ?>
        </div>
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

    <?php
    // Preparar datos para el gráfico de trayectoria patrimonial e histórico
    $meses_abrev = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $trayectoria_etiquetas = [];
    $trayectoria_neto = [];
    $trayectoria_ahorro = [];
    if (!empty($datos['trayectoria'])) {
        foreach ($datos['trayectoria'] as $reg) {
            $fecha = new DateTime($reg['periodo']);
            $mes_idx = (int)$fecha->format('n') - 1;
            $trayectoria_etiquetas[] = $meses_abrev[$mes_idx] . ' ' . $fecha->format('y');
            $trayectoria_neto[] = (float)$reg['patrimonio_neto'];
            
            // Tasa de ahorro: (ingresos - gastos) / ingresos * 100
            $ing = (float)$reg['total_ingresos'];
            $gas = (float)$reg['total_gastos'];
            $tasa = $ing > 0 ? (($ing - $gas) / $ing) * 100 : 0;
            $trayectoria_ahorro[] = round(max($tasa, 0), 1);
        }
    }
    $js_trayectoria_etiquetas = json_encode($trayectoria_etiquetas);
    $js_trayectoria_neto = json_encode($trayectoria_neto);
    $js_trayectoria_ahorro = json_encode($trayectoria_ahorro);
    ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle Ayuda
        function toggleAyuda() {
            const card = document.getElementById('ayudaDashboard');
            if (card) {
                card.classList.toggle('active');
            }
        }

        Chart.defaults.color = '#9ca3af';

        // 1. Gráfico de Distribución de Gastos (Doughnut)
        const ctxGastos = document.getElementById('graficoGastos').getContext('2d');
        const etiquetasGastos = <?= $datos['grafico_etiquetas'] ?>;
        const valoresGastos = <?= $datos['grafico_valores'] ?>;

        new Chart(ctxGastos, {
            type: 'doughnut',
            data: {
                labels: etiquetasGastos,
                datasets: [{
                    data: valoresGastos,
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { padding: 12, usePointStyle: true, boxWidth: 8 } }
                },
                cutout: '65%'
            }
        });

        // 2. Gráfico de Trayectoria (Líneas Doble Eje)
        const ctxTrayectoria = document.getElementById('graficoTrayectoria').getContext('2d');
        const etiquetasTrayectoria = <?= $js_trayectoria_etiquetas ?>;
        const valoresNeto = <?= $js_trayectoria_neto ?>;
        const valoresAhorro = <?= $js_trayectoria_ahorro ?>;

        new Chart(ctxTrayectoria, {
            type: 'line',
            data: {
                labels: etiquetasTrayectoria,
                datasets: [
                    {
                        label: 'Patrimonio Neto ($)',
                        data: valoresNeto,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'yNeto'
                    },
                    {
                        label: 'Tasa de Ahorro (%)',
                        data: valoresAhorro,
                        borderColor: '#06b6d4',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.3,
                        yAxisID: 'yAhorro'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8 } }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.05)' }
                    },
                    yNeto: {
                        type: 'linear',
                        position: 'left',
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    yAhorro: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        min: 0,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>

<?php require_once 'app/views/partials/layout_footer.php'; ?>