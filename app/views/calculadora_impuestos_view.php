<?php $page_title = 'Fiscal'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="header-actions-row">
        <div class="page-header" style="margin-bottom: 0;">
            <h2>Automatización Fiscal para Independientes</h2>
            <p>Calculá la reserva que debés apartar mensualmente para impuestos</p>
        </div>
        <button type="button" class="info-btn" onclick="toggleAyuda()"><i class="fa-solid fa-circle-info"></i> Conceptos del Módulo</button>
    </div>

    <!-- Guía conceptual integrada (Ayuda interactiva) -->
    <div class="help-card" id="ayudaImpuestos">
        <h4><i class="fa-solid fa-graduation-cap"></i> Educación Financiera: Conceptos Fiscales</h4>
        <div class="help-grid">
            <div class="help-item">
                <strong>IVA (Impuesto al Valor Agregado)</strong>
                Es un impuesto al consumo. Si emitís facturas tipo A o B, cobrás IVA débito (ej. 21%) a tus clientes. A esto le restás el IVA crédito de tus compras de insumos para calcular el saldo a pagar.
            </div>
            <div class="help-item">
                <strong>Ingresos Brutos (IIBB)</strong>
                Impuesto provincial que se cobra sobre la facturación bruta total obtenida en el mes, sin importar tus gastos o ganancias reales. Cada provincia maneja sus propias alícuotas (comúnmente entre 3% y 5%).
            </div>
            <div class="help-item">
                <strong>Impuesto a las Ganancias</strong>
                Es un tributo progresivo sobre tu utilidad neta anual. Se calcula restando todos los gastos comerciales deducibles necesarios para tu negocio de tu ingreso bruto facturado.
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="form-card" style="max-width: 620px;">
        <h3>Datos del Período</h3>
        <form action="index.php?action=impuestos" method="POST">
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Ingresos Brutos Mensuales ($)</label>
                    <input type="number" step="0.01" name="ingresos_brutos" required min="0.01" value="<?= isset($_POST['ingresos_brutos']) ? $_POST['ingresos_brutos'] : '' ?>" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Gastos Deducibles ($)</label>
                    <input type="number" step="0.01" name="gastos_deducibles" required min="0" value="<?= isset($_POST['gastos_deducibles']) ? $_POST['gastos_deducibles'] : '0' ?>">
                </div>
            </div>

            <h3 style="margin-top: 8px;">Configuración de Alícuotas</h3>
            <div class="form-grid-3">
                <div class="form-group">
                    <label>IVA (%)</label>
                    <input type="number" step="0.01" name="porcentaje_iva" required min="0" value="<?= isset($_POST['porcentaje_iva']) ? $_POST['porcentaje_iva'] : '21' ?>">
                </div>
                <div class="form-group">
                    <label>Ingresos Brutos (%)</label>
                    <input type="number" step="0.01" name="porcentaje_iibb" required min="0" value="<?= isset($_POST['porcentaje_iibb']) ? $_POST['porcentaje_iibb'] : '3' ?>">
                </div>
                <div class="form-group">
                    <label>Ganancias (%)</label>
                    <input type="number" step="0.01" name="porcentaje_ganancias" required min="0" value="<?= isset($_POST['porcentaje_ganancias']) ? $_POST['porcentaje_ganancias'] : '10' ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Calcular Retenciones</button>
        </form>
    </div>

    <?php if (isset($resultados)): ?>
        <?php if (is_string($resultados)): ?>
            <div class="alert alert-danger"><?= $resultados ?></div>
        <?php else: ?>
            <div class="results-block">
                <h2>Resumen de Obligaciones Fiscales</h2>
                <p><strong>IVA a pagar:</strong> <span class="text-red">$<?= number_format($resultados['monto_iva'], 2) ?></span></p>
                <p><strong>Ingresos Brutos a pagar:</strong> <span class="text-red">$<?= number_format($resultados['monto_iibb'], 2) ?></span></p>
                <p><strong>Impuesto a las Ganancias estimado:</strong> <span class="text-red">$<?= number_format($resultados['monto_ganancias'], 2) ?></span></p>
                <hr>
                <p style="font-size: 1.15rem;"><strong>Total Reserva Fiscal:</strong> <strong class="text-red">$<?= number_format($resultados['reserva_total'], 2) ?></strong></p>
                <p style="font-size: 1.15rem;"><strong>Ingreso Neto Real (Bolsillo):</strong> <strong class="text-green">$<?= number_format($resultados['ingreso_neto_real'], 2) ?></strong></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <script>
        function toggleAyuda() {
            const card = document.getElementById('ayudaImpuestos');
            if (card) {
                card.classList.toggle('active');
            }
        }
    </script>

<?php require_once 'app/views/partials/layout_footer.php'; ?>