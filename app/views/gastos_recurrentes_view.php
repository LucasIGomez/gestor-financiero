<?php $page_title = 'Gastos Fijos'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="page-header">
        <h2>Automatización de Gastos Fijos</h2>
        <p>Configurá plantillas de gastos que se registran automáticamente cada mes</p>
    </div>

    <!-- Formulario -->
    <div class="form-card" style="max-width: 620px;">
        <h3>Añadir Nueva Plantilla</h3>
        <form action="index.php?action=registrar_gasto_recurrente" method="POST">
            <div class="form-group">
                <label>Categoría del Gasto</label>
                <select name="id_categoria" required>
                    <?php foreach ($datos['categorias'] as $categoria): ?>
                        <?php if ($categoria['tipo_flujo'] === 'gasto'): ?>
                            <option value="<?= $categoria['id_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Monto Fijo ($)</label>
                    <input type="number" step="0.01" name="monto" required min="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Día de Cobro Mensual (1-31)</label>
                    <input type="number" name="dia_cobro" required min="1" max="31" placeholder="Ej. 10">
                </div>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" required maxlength="255" placeholder="Ej. Alquiler, Seguro Auto">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Gasto Fijo</button>
        </form>
    </div>

    <!-- Tabla de Plantillas -->
    <div class="table-card">
        <div class="table-header">
            <h3>Plantillas Activas</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Día de Cobro</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Monto ($)</th>
                    <th>Último Procesamiento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($datos['plantillas'])): ?>
                    <?php foreach ($datos['plantillas'] as $plantilla): ?>
                        <tr>
                            <td style="text-align: center;"><strong><?= $plantilla['dia_cobro'] ?></strong></td>
                            <td><?= htmlspecialchars($plantilla['nombre_categoria']) ?></td>
                            <td><?= htmlspecialchars($plantilla['descripcion']) ?></td>
                            <td><strong>$<?= number_format($plantilla['monto'], 2) ?></strong></td>
                            <td>
                                <?php if ($plantilla['ultimo_procesamiento']): ?>
                                    <span class="text-green"><?= date('d/m/Y', strtotime($plantilla['ultimo_procesamiento'])) ?></span>
                                <?php else: ?>
                                    <span class="text-yellow">Pendiente</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">No hay gastos fijos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php require_once 'app/views/partials/layout_footer.php'; ?>