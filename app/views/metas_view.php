<?php $page_title = 'Metas de Ahorro'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="page-header">
        <h2>Gestión de Metas y Fondo de Emergencia</h2>
        <p>Definí objetivos financieros y hacé un seguimiento de tu progreso</p>
    </div>

    <!-- Formulario -->
    <div class="form-card" style="max-width: 620px;">
        <h3>Definir Nueva Meta</h3>
        <form action="index.php?action=registrar_meta" method="POST">
            <div class="form-group">
                <label>Nombre del Objetivo</label>
                <input type="text" name="nombre_meta" required maxlength="100" placeholder="Ej. Fondo de Emergencia 6 Meses">
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Monto a Alcanzar ($)</label>
                    <input type="number" step="0.01" name="monto_objetivo" required min="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Fecha Límite</label>
                    <input type="date" name="fecha_limite" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Crear Meta</button>
        </form>
    </div>

    <!-- Cards de Metas -->
    <h3 style="margin-bottom: 16px; font-size: 1.1rem;">Tus Objetivos Financieros</h3>
    <div class="metas-grid">
        <?php if (!empty($metas)): ?>
            <?php foreach ($metas as $meta): ?>
                <div class="meta-card <?= $meta['porcentaje_avance'] >= 100 ? 'completada' : '' ?>">
                    <h3><?= htmlspecialchars($meta['nombre_meta']) ?></h3>

                    <div class="meta-stats">
                        <span>Ahorrado: <strong class="text-green">$<?= number_format($meta['saldo_actual'], 2) ?></strong></span>
                        <span>Objetivo: <strong>$<?= number_format($meta['monto_objetivo'], 2) ?></strong></span>
                    </div>

                    <div class="progress-track">
                        <div class="progress-fill <?= $meta['porcentaje_avance'] >= 100 ? 'complete' : '' ?>" style="width: <?= $meta['porcentaje_avance'] ?>%;"></div>
                    </div>

                    <div class="meta-stats" style="margin-top: 10px;">
                        <span><?= $meta['porcentaje_avance'] ?>% completado</span>
                        <span>Vence: <?= date('d/m/Y', strtotime($meta['fecha_limite'])) ?></span>
                    </div>

                    <?php if ($meta['porcentaje_avance'] < 100): ?>
                        <form action="index.php?action=agregar_ahorro" method="POST" class="form-ahorro">
                            <input type="hidden" name="id_meta" value="<?= $meta['id_meta'] ?>">
                            <input type="number" step="0.01" name="monto_deposito" required min="0.01" placeholder="Aportar ($)">
                            <button type="submit" class="btn btn-primary btn-sm">Ahorrar</button>
                        </form>
                    <?php else: ?>
                        <p class="text-green" style="text-align: center; font-weight: 600; margin-top: 14px;"><i class="fa-solid fa-trophy"></i> ¡Meta Completada!</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted" style="padding: 20px;">No tenés metas financieras activas. ¡Empezá a planificar tu futuro!</p>
        <?php endif; ?>
    </div>

<?php require_once 'app/views/partials/layout_footer.php'; ?>