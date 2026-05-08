<?php $page_title = 'Deudas'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="page-header">
        <h2>Asesor de Prioridades de Deuda</h2>
        <p>Método Avalancha — Las deudas con mayor costo financiero se pagan primero</p>
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

    <!-- Tabla de Estrategia Avalancha -->
    <div class="table-card">
        <div class="table-header">
            <h3>Prioridades de Pago (Ordenadas por CFT)</h3>
            <p>La tabla simula el impacto de un pago adicional de $50,000 en cada deuda.</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Saldo Total</th>
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
                                    <button type="button" class="action-link pay" onclick="togglePago(<?= $deuda['id_deuda'] ?>)">💰 Pagar</button>
                                    <a href="index.php?action=editar_deuda&id=<?= $deuda['id_deuda'] ?>" class="action-link edit">Editar</a>
                                    <a href="index.php?action=eliminar_deuda&id=<?= $deuda['id_deuda'] ?>" class="action-link delete" onclick="return confirm('¿Eliminar este pasivo? Esta acción es irreversible.');">Eliminar</a>
                                </div>
                                <!-- Formulario de pago inline -->
                                <form id="pago-<?= $deuda['id_deuda'] ?>" class="pago-inline" style="display:none;" action="index.php?action=registrar_pago_deuda" method="POST">
                                    <input type="hidden" name="id_deuda" value="<?= $deuda['id_deuda'] ?>">
                                    <input type="number" step="0.01" name="monto_pago" required min="0.01" 
                                           placeholder="$<?= number_format($deuda['cuota_mensual'], 0) ?>" 
                                           value="<?= $deuda['cuota_mensual'] ?>"
                                           style="width: 120px;">
                                    <button type="submit" class="btn btn-success btn-sm">Confirmar</button>
                                    <button type="button" class="btn btn-cancel btn-sm" onclick="togglePago(<?= $deuda['id_deuda'] ?>)">✕</button>
                                </form>
                            </td>
                        </tr>
                    <?php $prioridad++; endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px;">No tenés deudas registradas. ¡Excelente!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleCampos() {
            const tipo = document.getElementById('tipo_deuda').value;
            document.getElementById('campos_prestamo').style.display = tipo === 'prestamo' ? 'block' : 'none';
            document.getElementById('campos_tarjeta').style.display = tipo === 'tarjeta_credito' ? 'block' : 'none';
        }

        function togglePago(id) {
            const form = document.getElementById('pago-' + id);
            form.style.display = form.style.display === 'none' ? 'flex' : 'none';
        }

        window.onload = toggleCampos;
    </script>

<?php require_once 'app/views/partials/layout_footer.php'; ?>