<?php $page_title = 'Deudas'; require_once 'app/views/partials/layout_header.php'; 
$metodo_actual = $_GET['metodo'] ?? 'avalancha';
?>

    <div class="header-actions-row">
        <div class="page-header" style="margin-bottom: 0;">
            <h2>Asesor de Prioridades de Deuda</h2>
            <p><?= $metodo_actual === 'bolanieve' ? 'Método Bola de Nieve — Las deudas con menor saldo se liquidan primero' : 'Método Avalancha — Las deudas con mayor costo financiero se pagan primero' ?></p>
        </div>
        <button type="button" class="info-btn" onclick="toggleAyuda()"><i class="fa-solid fa-circle-info"></i> Conceptos del Módulo</button>
    </div>

    <!-- Guía conceptual integrada (Ayuda interactiva) -->
    <div class="help-card" id="ayudaDeudas">
        <h4><i class="fa-solid fa-graduation-cap"></i> Educación Financiera: Deudas y Amortización</h4>
        <div class="help-grid">
            <div class="help-item">
                <strong>CFT (Costo Financiero Total)</strong>
                Es el costo real del crédito. Incluye la tasa de interés base más comisiones, cargos, seguros y gastos administrativos. Siempre debés usar el CFT para evaluar qué tan caro es un préstamo.
            </div>
            <div class="help-item">
                <strong>TNA vs TEA</strong>
                La TNA (Tasa Nominal Anual) es la tasa de referencia sin capitalización. La TEA (Tasa Efectiva Anual) contempla la capitalización (interés sobre interés) al pagar en cuotas. La TEA refleja el interés real anual que pagás.
            </div>
            <div class="help-item">
                <strong>Día de Cierre vs Vencimiento</strong>
                El Cierre es la fecha límite en que consumos ingresan en el resumen de tarjeta. El Vencimiento es la fecha máxima para pagar ese resumen. Los consumos posteriores al cierre pasan al ciclo del mes siguiente.
            </div>
            <div class="help-item">
                <strong>Avalancha vs Bola de Nieve</strong>
                El método Avalancha ataca la deuda con mayor CFT primero para ahorrar el máximo interés posible. La Bola de Nieve liquida las deudas de menor saldo primero para lograr victorias rápidas que motivan psicológicamente.
            </div>
        </div>
    </div>

    <!-- Selector de Estrategia -->
    <div class="strategy-selector" style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px; background: var(--bg-card); padding: 12px 18px; border: 1px solid var(--border); border-radius: var(--radius-sm);">
        <span style="font-weight: 600; font-size: 0.9rem; color: var(--text-secondary);">Estrategia de visualización:</span>
        <a href="index.php?action=deudas&metodo=avalancha" class="btn <?= $metodo_actual === 'avalancha' ? 'btn-primary' : 'btn-cancel' ?> btn-sm">Método Avalancha (CFT)</a>
        <a href="index.php?action=deudas&metodo=bolanieve" class="btn <?= $metodo_actual === 'bolanieve' ? 'btn-primary' : 'btn-cancel' ?> btn-sm">Método Bola de Nieve (Saldo)</a>
    </div>

    <!-- Simulador de Desendeudamiento Unificado -->
    <div class="card" style="margin-bottom: 28px;">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 12px;"><i class="fa-solid fa-calculator text-accent"></i> Simulador de Desendeudamiento Unificado</h3>
        <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 18px;">
            Ingresá un monto extra mensual por encima de tus cuotas mínimas y mirá cómo impacta de forma unificada en toda tu cartera de deudas con efecto cascada (método de prioridades).
        </p>
        
        <div class="form-grid-3" style="align-items: flex-end; gap: 16px; margin-bottom: 24px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Aporte Mensual Extra ($)</label>
                <input type="number" id="sim_aporte_extra" min="0" step="1000" value="50000" placeholder="Ej: 50000">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Método de Simulación</label>
                <select id="sim_metodo">
                    <option value="avalancha" <?= $metodo_actual === 'avalancha' ? 'selected' : '' ?>>Avalancha (Mayor CFT primero)</option>
                    <option value="bolanieve" <?= $metodo_actual === 'bolanieve' ? 'selected' : '' ?>>Bola de Nieve (Menor Saldo primero)</option>
                </select>
            </div>
            <button type="button" class="btn btn-primary" onclick="simularDesendeudamiento()"><i class="fa-solid fa-play"></i> Calcular Proyección</button>
        </div>

        <!-- Resultados de Simulación -->
        <div id="sim_resultados" class="results-block" style="display: none; border-left-color: var(--green) !important; background: rgba(16, 185, 129, 0.02);">
            <div class="form-grid-3 text-center" style="margin-bottom: 20px;">
                <div class="stat-card positive" style="padding: 16px;">
                    <div class="card-title" style="font-size: 0.78rem;">Fecha de Fin de Deudas</div>
                    <div class="card-value text-green" id="res_mes_fin" style="font-size: 1.45rem;">-</div>
                    <div class="card-subtitle" id="res_tiempo_total">-</div>
                </div>
                <div class="stat-card positive" style="padding: 16px;">
                    <div class="card-title" style="font-size: 0.78rem;">Meses Ahorrados</div>
                    <div class="card-value text-green" id="res_meses_ahorrad" style="font-size: 1.45rem;">-</div>
                    <div class="card-subtitle">Amortización acelerada</div>
                </div>
                <div class="stat-card positive" style="padding: 16px;">
                    <div class="card-title" style="font-size: 0.78rem;">Intereses Evitados</div>
                    <div class="card-value text-green" id="res_intereses_ahorrad" style="font-size: 1.45rem;">-</div>
                    <div class="card-subtitle">Ahorro financiero real</div>
                </div>
            </div>

            <div id="sim_alerta_insuficiente" class="alert alert-danger" style="display: none; margin-bottom: 16px;">
                <h4><i class="fa-solid fa-triangle-exclamation"></i> Alerta Crítica del Asesor</h4>
                <p>
                    Tus aportes mensuales (incluyendo cuotas mínimas) no son suficientes para cubrir los intereses acumulados de tus deudas. 
                    El saldo total está aumentando. Se recomienda aportar al menos <strong id="sim_min_sugerido">$0</strong> adicionales por mes para amortizar las deudas.
                </p>
            </div>

            <h4 style="font-size: 0.92rem; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">Cronograma de Cancelación Cascada:</h4>
            <ul id="res_cronograma" style="list-style: none; padding-left: 0; font-size: 0.85rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 8px;">
                <!-- Cargado dinámicamente -->
            </ul>
        </div>
    </div>

    <!-- Formulario de Registro de Deudas -->
    <div class="form-card">
        <h3>Registrar Nueva Deuda / Tarjeta</h3>
        <form action="index.php?action=registrar_deuda" method="POST">

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Tipo de Deuda</label>
                    <select name="tipo_deuda" id="tipo_deuda" required onchange="toggleCampos()">
                        <option value="prestamo">Préstamo / Crédito Amortizable</option>
                        <option value="tarjeta_credito">Tarjeta de Crédito (Revolvente)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nombre del Pasivo</label>
                    <input type="text" name="nombre_deuda" required placeholder="Ej. Visa Galicia o Préstamo Auto">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Saldo Total Adeudado ($)</label>
                    <input type="number" step="0.01" name="saldo_total" required min="0" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Cuota Mensual / Pago Mínimo ($)</label>
                    <input type="number" step="0.01" name="cuota_mensual" id="cuota_mensual" min="0" placeholder="0.00">
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label>CFT (%) — Prioridad Avalancha</label>
                    <input type="number" step="0.01" name="cft" required min="0.01" title="Costo Financiero Total" placeholder="Ej. 185.50">
                </div>
                <div class="form-group">
                    <label>TNA (%) — Opcional</label>
                    <input type="number" step="0.01" name="tna" min="0" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label>TEA (%) — Opcional</label>
                    <input type="number" step="0.01" name="tea" min="0" placeholder="Opcional">
                </div>
            </div>

            <!-- Campos Dinámicos: Préstamos -->
            <div id="campos_prestamo" class="conditional-block">
                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Cuotas Totales</label>
                        <input type="number" name="cuotas_totales" min="1" placeholder="Ej. 48">
                    </div>
                    <div class="form-group">
                        <label>Cuotas Pagadas</label>
                        <input type="number" name="cuotas_pagadas" min="0" placeholder="Ej. 12">
                    </div>
                    <div class="form-group">
                        <label>Día de Vencimiento</label>
                        <input type="number" name="dia_vencimiento" min="1" max="31" placeholder="1-31">
                    </div>
                </div>
            </div>

            <!-- Campos Dinámicos: Tarjetas de Crédito -->
            <div id="campos_tarjeta" class="conditional-block green" style="display: none;">
                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Límite de Crédito ($)</label>
                        <input type="number" step="0.01" name="limite_credito" id="limite_credito" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Día de Cierre</label>
                        <input type="number" name="dia_cierre" min="1" max="31" placeholder="1-31">
                    </div>
                    <div class="form-group">
                        <label>Día de Vencimiento Pago</label>
                        <input type="number" name="dia_vencimiento_tarjeta" min="1" max="31" placeholder="1-31">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Deuda / Tarjeta</button>
        </form>
    </div>

    <!-- Tabla de Estrategia -->
    <div class="table-card">
        <div class="table-header">
            <h3>Prioridades de Pago (Ordenadas por <?= $metodo_actual === 'bolanieve' ? 'Saldo Mínimo' : 'CFT' ?>)</h3>
            <p>La tabla simula el impacto de un pago adicional de $50,000 en cada deuda.</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Saldo Total</th>
                    <th>Progreso</th>
                    <th>CFT (%)</th>
                    <th>Cuota Mensual</th>
                    <th>Simulación (+$50k)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos)): ?>
                    <?php $prioridad = 1; foreach ($datos as $deuda): ?>
                        <tr>
                            <td>
                                <?php
                                    $badge_class = $prioridad === 1 ? 'high' : ($prioridad <= 3 ? 'medium' : 'low');
                                ?>
                                <span class="priority-badge <?= $badge_class ?>">#<?= $prioridad ?></span>
                            </td>
                            <td><strong><?= htmlspecialchars($deuda['nombre_deuda']) ?></strong></td>
                            <td>
                                <span class="type-badge <?= $deuda['tipo_deuda'] === 'tarjeta_credito' ? 'tarjeta' : 'prestamo' ?>">
                                    <?= $deuda['tipo_deuda'] === 'tarjeta_credito' ? 'Tarjeta' : 'Préstamo' ?>
                                </span>
                            </td>
                            <td><strong>$<?= number_format($deuda['saldo_total'], 2) ?></strong></td>
                            <td>
                                <?php
                                $progreso = 0;
                                if ($deuda['tipo_deuda'] === 'prestamo' && $deuda['cuotas_totales'] > 0) {
                                    $progreso = ($deuda['cuotas_pagadas'] / $deuda['cuotas_totales']) * 100;
                                } elseif ($deuda['tipo_deuda'] === 'tarjeta_credito' && $deuda['limite_credito'] > 0) {
                                    $progreso = (($deuda['limite_credito'] - $deuda['saldo_total']) / $deuda['limite_credito']) * 100;
                                }
                                $progreso = min(max($progreso, 0), 100);
                                ?>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div class="progress-track" style="width: 80px;" title="<?= round($progreso) ?>% pagado/libre">
                                        <div class="progress-fill <?= $progreso >= 100 ? 'complete' : '' ?>" style="width: <?= $progreso ?>%;"></div>
                                    </div>
                                    <span style="font-size: 0.78rem; font-weight: 600; color: var(--text-secondary);"><?= round($progreso) ?>%</span>
                                </div>
                            </td>
                            <td class="text-red"><strong><?= number_format($deuda['cft'], 2) ?>%</strong></td>
                            <td>$<?= number_format($deuda['cuota_mensual'], 2) ?></td>
                            <td>
                                <?php if ($deuda['meses_ahorrados'] > 0): ?>
                                    <span class="text-green">Ahorrás <?= round($deuda['meses_ahorrados']) ?> meses</span><br>
                                    <span class="text-green" style="font-size: 0.82rem;">Evitás $<?= number_format($deuda['intereses_ahorrados'], 2) ?> en int.</span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <button type="button" class="action-link pay" onclick="togglePago(<?= $deuda['id_deuda'] ?>)"><i class="fa-solid fa-coins"></i> Pagar</button>
                                    <a href="index.php?action=editar_deuda&id=<?= $deuda['id_deuda'] ?>" class="action-link edit">Editar</a>
                                    <a href="index.php?action=eliminar_deuda&id=<?= $deuda['id_deuda'] ?>" class="action-link delete" onclick="return confirm('¿Eliminar este pasivo? Esta acción es irreversible.');">Eliminar</a>
                                </div>
                                <!-- Formulario de pago inline -->
                                <form id="pago-<?= $deuda['id_deuda'] ?>" class="pago-inline" style="display:none; flex-direction: column; gap: 8px; margin-top: 10px; width: 220px;" action="index.php?action=registrar_adelanto_deuda" method="POST">
                                    <input type="hidden" name="id_deuda" value="<?= $deuda['id_deuda'] ?>">
                                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);">Pagar/Adelantar ($):</span>
                                        <input type="number" step="0.01" name="monto_pago" required min="0.01" 
                                               value="<?= $deuda['cuota_mensual'] ?>" style="width: 100px; padding: 4px 8px;">
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);">Bono/Descuento ($):</span>
                                        <input type="number" step="0.01" name="descuento" min="0" value="0" style="width: 100px; padding: 4px 8px;">
                                    </div>
                                    <div style="display: flex; gap: 6px; width: 100%; margin-top: 4px;">
                                        <button type="submit" class="btn btn-success btn-sm" style="flex: 1; padding: 4px;">Confirmar</button>
                                        <button type="button" class="btn btn-cancel btn-sm" onclick="togglePago(<?= $deuda['id_deuda'] ?>)" style="padding: 4px 8px;">✕</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php $prioridad++; endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 30px;">No tenés deudas registradas. ¡Excelente!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Serialización de los datos originales de deudas desde PHP
        const deudasOriginales = <?= json_encode($datos ?: []) ?>;

        function toggleCampos() {
            const tipo = document.getElementById('tipo_deuda').value;
            document.getElementById('campos_prestamo').style.display = tipo === 'prestamo' ? 'block' : 'none';
            document.getElementById('campos_tarjeta').style.display = tipo === 'tarjeta_credito' ? 'block' : 'none';
        }

        function togglePago(id) {
            const form = document.getElementById('pago-' + id);
            form.style.display = form.style.display === 'none' ? 'flex' : 'none';
        }

        function toggleAyuda() {
            const card = document.getElementById('ayudaDeudas');
            if (card) {
                card.classList.toggle('active');
            }
        }

        function obtenerFechaFutura(meses) {
            const mesesNombres = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            const hoy = new Date();
            // Simular fecha fija base del sistema (Junio 2026)
            hoy.setFullYear(2026);
            hoy.setMonth(5); // Junio es mes 5 indexado en JS
            hoy.setMonth(hoy.getMonth() + meses);
            return mesesNombres[hoy.getMonth()] + ' ' + hoy.getFullYear();
        }

        function ejecutarSimulacion(deudasOriginalesList, extra, metodo) {
            // Copiar deudas
            const deudas = deudasOriginalesList.map(d => ({...d}));
            
            let mes = 0;
            let interesAcumulado = 0;
            let cronograma = [];
            let insolvente = false;
            let minAporteRequerido = 0;

            const totalMinOriginal = deudas.reduce((sum, d) => sum + parseFloat(d.cuota_mensual || 0), 0);

            while (deudas.some(d => parseFloat(d.saldo_total) > 0) && mes < 360) {
                mes++;
                let interesEsteMes = 0;
                let minCuotasRequeridas = 0;

                // 1. Devengar intereses mensuales
                deudas.forEach(d => {
                    let saldo = parseFloat(d.saldo_total) || 0;
                    if (saldo > 0) {
                        const cft = parseFloat(d.cft) || 0;
                        const tasaMensual = (cft / 12) / 100;
                        const interes = saldo * tasaMensual;
                        d.saldo_total = saldo + interes;
                        interesEsteMes += interes;
                        minCuotasRequeridas += parseFloat(d.cuota_mensual) || 0;
                    }
                });

                // Alerta de insolvencia si los intereses devengados son mayores que los pagos mínimos + extra
                if (interesEsteMes >= (totalMinOriginal + extra) && mes === 1) {
                    insolvente = true;
                    minAporteRequerido = Math.ceil(interesEsteMes - totalMinOriginal + 1000);
                    return { insolvente, minAporteRequerido };
                }

                interesAcumulado += interesEsteMes;

                // 2. Realizar pagos mínimos
                let minPagados = 0;
                deudas.forEach(d => {
                    let saldo = parseFloat(d.saldo_total) || 0;
                    if (saldo > 0) {
                        const cuota = parseFloat(d.cuota_mensual) || 0;
                        const pago = Math.min(saldo, cuota);
                        d.saldo_total = saldo - pago;
                        minPagados += pago;
                    }
                });

                // 3. Aplicar pago adicional (cascada)
                let cashDisponible = (totalMinOriginal + extra) - minPagados;

                // Ordenar según método
                if (metodo === 'avalancha') {
                    deudas.sort((a, b) => parseFloat(b.cft || 0) - parseFloat(a.cft || 0));
                } else {
                    deudas.sort((a, b) => {
                        let saldoA = parseFloat(a.saldo_total) || 0;
                        let saldoB = parseFloat(b.saldo_total) || 0;
                        if (saldoA <= 0) return 1;
                        if (saldoB <= 0) return -1;
                        return saldoA - saldoB;
                    });
                }

                // Aplicar excedente en cascada
                for (let i = 0; i < deudas.length; i++) {
                    const d = deudas[i];
                    let saldo = parseFloat(d.saldo_total) || 0;
                    if (saldo > 0 && cashDisponible > 0) {
                        const pagoCascada = Math.min(saldo, cashDisponible);
                        d.saldo_total = saldo - pagoCascada;
                        cashDisponible -= pagoCascada;
                    }
                }

                // Registrar deudas saldadas este mes
                deudas.forEach(d => {
                    let saldo = parseFloat(d.saldo_total) || 0;
                    if (saldo <= 0 && !cronograma.some(c => c.id_deuda === d.id_deuda)) {
                        cronograma.push({
                            id_deuda: d.id_deuda,
                            nombre: d.nombre_deuda,
                            mes: mes,
                            fecha: obtenerFechaFutura(mes),
                            interesDeuda: 0
                        });
                    }
                });
            }

            if (mes >= 360) {
                return { insolvente: true, minAporteRequerido: 5000 };
            }

            return {
                meses: mes,
                interesAcumulado: interesAcumulado,
                cronograma: cronograma,
                insolvente: false
            };
        }

        function simularDesendeudamiento() {
            const aporteExtra = parseFloat(document.getElementById('sim_aporte_extra').value) || 0;
            const metodo = document.getElementById('sim_metodo').value;

            if (!deudasOriginales || deudasOriginales.length === 0) {
                document.getElementById('sim_resultados').style.display = 'block';
                document.getElementById('res_cronograma').innerHTML = '<li>Registrá alguna deuda para poder simular.</li>';
                return;
            }

            const deudasBase = deudasOriginales.map(d => ({
                id_deuda: d.id_deuda,
                nombre_deuda: d.nombre_deuda,
                saldo_total: parseFloat(d.saldo_total) || 0,
                cuota_mensual: parseFloat(d.cuota_mensual) || 0,
                cft: parseFloat(d.cft) || 0,
                tipo_deuda: d.tipo_deuda
            }));

            // Simulación Baseline (sin aporte extra)
            const baseline = ejecutarSimulacion(deudasBase, 0, metodo);

            // Simulación con aporte extra
            const conExtra = ejecutarSimulacion(deudasBase, aporteExtra, metodo);

            document.getElementById('sim_resultados').style.display = 'block';

            if (conExtra.insolvente) {
                document.getElementById('sim_alerta_insuficiente').style.display = 'block';
                document.getElementById('sim_min_sugerido').innerText = '$' + conExtra.minAporteRequerido.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('res_mes_fin').innerText = 'Indefinida';
                document.getElementById('res_tiempo_total').innerText = 'Saldo creciente';
                document.getElementById('res_meses_ahorrad').innerText = '0';
                document.getElementById('res_intereses_ahorrad').innerText = '$0.00';
                document.getElementById('res_cronograma').innerHTML = '<li class="text-red">Los pagos mínimos no cubren la tasa de interés acumulada. Aumentá el aporte extra.</li>';
                return;
            } else {
                document.getElementById('sim_alerta_insuficiente').style.display = 'none';
            }

            const fechaFin = obtenerFechaFutura(conExtra.meses);
            document.getElementById('res_mes_fin').innerText = fechaFin;
            document.getElementById('res_tiempo_total').innerText = `En ${conExtra.meses} meses`;

            const mesesAhorrados = Math.max(0, baseline.meses - conExtra.meses);
            document.getElementById('res_meses_ahorrad').innerText = mesesAhorrados > 0 ? `${mesesAhorrados} meses` : '0 (ya al mínimo)';

            const interesesEvitados = Math.max(0, baseline.interesAcumulado - conExtra.interesAcumulado);
            document.getElementById('res_intereses_ahorrad').innerText = '$' + interesesEvitados.toLocaleString(undefined, {minimumFractionDigits: 2});

            let cronHtml = '';
            conExtra.cronograma.forEach(item => {
                cronHtml += `
                    <li style="display: flex; align-items: center; gap: 8px;">
                        <span class="priority-badge low" style="padding: 1px 6px; font-size: 0.7rem;"><i class="fa-solid fa-circle-check"></i> Mes ${item.mes}</span>
                        <span><strong>${item.nombre}</strong> cancelada en <strong>${item.fecha}</strong></span>
                    </li>
                `;
            });
            if (cronHtml === '') {
                cronHtml = '<li>No hay deudas que amortizar.</li>';
            }
            document.getElementById('res_cronograma').innerHTML = cronHtml;
        }

        window.onload = function() {
            toggleCampos();
            // Ejecutar simulación automática al cargar
            simularDesendeudamiento();
        };
    </script>

<?php require_once 'app/views/partials/layout_footer.php'; ?>