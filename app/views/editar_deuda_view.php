<?php $page_title = 'Editar Deuda'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="page-header">
        <h2>Editar Deuda / Tarjeta</h2>
        <p>Actualizá los datos de: <?= htmlspecialchars($deuda['nombre_deuda']) ?></p>
    </div>

    <div class="form-card" style="max-width: 860px;">
        <form action="index.php?action=editar_deuda_procesar" method="POST">
            <input type="hidden" name="id_deuda" value="<?= $deuda['id_deuda'] ?>">

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Tipo de Deuda</label>
                    <select name="tipo_deuda" id="tipo_deuda" required onchange="toggleCampos()">
                        <option value="prestamo" <?= $deuda['tipo_deuda'] === 'prestamo' ? 'selected' : '' ?>>Préstamo / Crédito Amortizable</option>
                        <option value="tarjeta_credito" <?= $deuda['tipo_deuda'] === 'tarjeta_credito' ? 'selected' : '' ?>>Tarjeta de Crédito (Revolvente)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nombre del Pasivo</label>
                    <input type="text" name="nombre_deuda" required value="<?= htmlspecialchars($deuda['nombre_deuda']) ?>">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Saldo Total Adeudado ($)</label>
                    <input type="number" step="0.01" name="saldo_total" required min="0" value="<?= $deuda['saldo_total'] ?>">
                </div>
                <div class="form-group">
                    <label>Cuota Mensual / Pago Mínimo ($)</label>
                    <input type="number" step="0.01" name="cuota_mensual" min="0" value="<?= $deuda['cuota_mensual'] ?>">
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-group">
                    <label>CFT (%) — Prioridad Avalancha</label>
                    <input type="number" step="0.01" name="cft" required min="0.01" value="<?= $deuda['cft'] ?>">
                </div>
                <div class="form-group">
                    <label>TNA (%) — Opcional</label>
                    <input type="number" step="0.01" name="tna" min="0" value="<?= $deuda['tna'] ?>">
                </div>
                <div class="form-group">
                    <label>TEA (%) — Opcional</label>
                    <input type="number" step="0.01" name="tea" min="0" value="<?= $deuda['tea'] ?>">
                </div>
            </div>

            <!-- Campos Préstamo -->
            <div id="campos_prestamo" class="conditional-block">
                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Cuotas Totales</label>
                        <input type="number" name="cuotas_totales" min="1" value="<?= $deuda['cuotas_totales'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Cuotas Pagadas</label>
                        <input type="number" name="cuotas_pagadas" min="0" value="<?= $deuda['cuotas_pagadas'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Día de Vencimiento</label>
                        <input type="number" name="dia_vencimiento" min="1" max="31" value="<?= $deuda['tipo_deuda'] === 'prestamo' ? $deuda['dia_vencimiento'] : '' ?>">
                    </div>
                </div>
            </div>

            <!-- Campos Tarjeta -->
            <div id="campos_tarjeta" class="conditional-block green" style="display: none;">
                <div class="form-grid-3">
                    <div class="form-group">
                        <label>Límite de Crédito ($)</label>
                        <input type="number" step="0.01" name="limite_credito" min="0" value="<?= $deuda['limite_credito'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Día de Cierre</label>
                        <input type="number" name="dia_cierre" min="1" max="31" value="<?= $deuda['dia_cierre'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Día de Vencimiento Pago</label>
                        <input type="number" name="dia_vencimiento_tarjeta" min="1" max="31" value="<?= $deuda['tipo_deuda'] === 'tarjeta_credito' ? $deuda['dia_vencimiento'] : '' ?>">
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 8px;">
                <a href="index.php?action=deudas" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar Datos</button>
            </div>
        </form>
    </div>

    <script>
        function toggleCampos() {
            const tipo = document.getElementById('tipo_deuda').value;
            document.getElementById('campos_prestamo').style.display = tipo === 'prestamo' ? 'block' : 'none';
            document.getElementById('campos_tarjeta').style.display = tipo === 'tarjeta_credito' ? 'block' : 'none';
        }
        window.onload = toggleCampos;
    </script>

<?php require_once 'app/views/partials/layout_footer.php'; ?>