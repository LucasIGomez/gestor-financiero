<?php $page_title = 'Inversiones'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="header-actions-row">
        <div class="page-header" style="margin-bottom: 0;">
            <h2>Inversiones y Planificación Inteligente</h2>
            <p>Hacé crecer tus ahorros con proyecciones y el asesoramiento del analista financiero</p>
        </div>
        <button type="button" class="info-btn" onclick="toggleAyuda()"><i class="fa-solid fa-circle-info"></i> Conceptos del Módulo</button>
    </div>

    <!-- Guía conceptual integrada (Ayuda interactiva) -->
    <div class="help-card" id="ayudaInversiones">
        <h4><i class="fa-solid fa-graduation-cap"></i> Educación Financiera: Inversiones y Multiplicación</h4>
        <div class="help-grid">
            <div class="help-item">
                <strong>Interés Compuesto</strong>
                Ocurre al reinvertir las ganancias de intereses obtenidas, sumándolas al capital original. En el siguiente ciclo, se calculan intereses sobre el nuevo monto. Genera un crecimiento exponencial a largo plazo.
            </div>
            <div class="help-item">
                <strong>Tasa de Retorno Real</strong>
                Es el rendimiento financiero real restando el efecto de la inflación. Si tu inversión rinde 80% pero la inflación es 70%, tu retorno real es del 10%. Es la métrica real que protege tu poder de compra.
            </div>
            <div class="help-item">
                <strong>Lógica del Asesor de ClariFi</strong>
                El sistema evalúa el plazo de tus objetivos: corto plazo (< 6 meses) sugiere billeteras digitales por liquidez; mediano plazo (6-12 meses) sugiere plazos fijos estables; largo plazo (> 12 meses) sugiere Fondos Comunes de Inversión o CEDEARs para ganarle a la inflación.
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
        
        <!-- COLUMNA IZQUIERDA: FORMULARIOS -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- 1. Registrar Inversión Activa -->
            <div class="form-card" style="margin-bottom: 0;">
                <h3>Registrar Inversión Activa</h3>
                <form action="index.php?action=registrar_inversion" method="POST">
                    <div class="form-group">
                        <label>Plataforma / Instrumento</label>
                        <input type="text" name="plataforma" required placeholder="Ej. Mercado Pago, Plazo Fijo, FCI Galileo">
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Monto Invertido ($)</label>
                            <input type="number" step="0.01" name="monto_invertido" required min="0.01" placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label>Tasa Retorno Mensual (%)</label>
                            <input type="number" step="0.01" name="tasa_retorno_mensual" required min="0" placeholder="Ej. 5.5">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Inicio</label>
                        <input type="date" name="fecha_inicio" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Registrar Inversión</button>
                </form>
            </div>

            <!-- 2. Simulador de Interés Compuesto -->
            <div class="form-card" style="margin-bottom: 0;">
                <h3>Simular Proyección a Futuro</h3>
                <form action="index.php?action=inversiones" method="POST">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Capital Inicial ($)</label>
                            <input type="number" step="0.01" name="inversion_inicial" required min="0" value="<?= $_POST['inversion_inicial'] ?? '10000' ?>">
                        </div>
                        <div class="form-group">
                            <label>Aporte Mensual ($)</label>
                            <input type="number" step="0.01" name="adicion_mensual" required min="0" value="<?= $_POST['adicion_mensual'] ?? '5000' ?>">
                        </div>
                    </div>
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label>Tasa Anual Estimada (%)</label>
                            <input type="number" step="0.01" name="tasa_anual" required min="0" value="<?= $_POST['tasa_anual'] ?? '10' ?>">
                        </div>
                        <div class="form-group">
                            <label>Años de Proyección</label>
                            <input type="number" name="anos" required min="1" max="50" value="<?= $_POST['anos'] ?? '10' ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center;">Calcular Proyección</button>
                </form>
            </div>
            
            <?php if (isset($resultados) && !empty($resultados)): ?>
                <!-- Tabla de Resultados de Simulación -->
                <div class="table-card" style="margin-bottom: 0;">
                    <div class="table-header">
                        <h3>Resultados de la Simulación</h3>
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
                                    <td><strong>$<?= number_format($fila['saldo_final'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>

        <!-- COLUMNA DERECHA: ANALISTA Y DATOS ACTIVOS -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- 1. Recomendaciones del Analista Financiero -->
            <div class="card" style="border-left: 4px solid var(--accent); background: radial-gradient(circle at 100% 0%, rgba(99,102,241,0.05) 0%, transparent 50%), var(--bg-card);">
                <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(99,102,241,0.12); color: var(--accent); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-bottom: 12px;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Analista Financiero Personal
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Planificación de Ahorro para Metas</h3>
                <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 16px;">
                    Calculamos tus depósitos necesarios recomendando la plataforma ideal según el plazo de tus objetivos.
                </p>

                <?php if (!empty($datos['analisis_metas'])): ?>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php foreach ($datos['analisis_metas'] as $analisis): ?>
                            <div style="background: rgba(0,0,0,0.15); border: 1px solid var(--border); padding: 14px; border-radius: var(--radius-sm);">
                                <h4 style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 6px;">
                                    <i class="fa-solid fa-bullseye"></i> Meta: <?= htmlspecialchars($analisis['meta']['nombre_meta']) ?>
                                </h4>
                                <ul style="list-style: none; padding: 0; font-size: 0.85rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 4px;">
                                    <li>• Faltan ahorrar: <strong>$<?= number_format($analisis['saldo_restante'], 2) ?></strong></li>
                                    <li>• Plazo restante: <strong><?= $analisis['meses_restantes'] ?> meses</strong></li>
                                    <li>• Depósito mensual sugerido: <strong class="text-accent" style="font-size: 0.92rem;">$<?= number_format($analisis['deposito_sugerido'], 2) ?> / mes</strong></li>
                                    <li style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed var(--border);">
                                        <i class="fa-solid fa-rocket"></i> <strong>Plataforma sugerida:</strong> <span class="text-green"><?= $analisis['recomendacion_plataforma'] ?></span>
                                    </li>
                                    <li style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; margin-top: 2px;">
                                        <?= $analisis['motivo'] ?>
                                    </li>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 10px 0;">
                        No tienes metas de ahorro registradas. Ve a la sección de "Metas de Ahorro" para añadir una.
                    </p>
                <?php endif; ?>
            </div>

            <!-- 2. Listado de Inversiones Activas -->
            <div class="table-card" style="margin-bottom: 0;">
                <div class="table-header">
                    <h3>Inversiones Activas</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Plataforma</th>
                            <th>Invertido</th>
                            <th>Retorno Mensual</th>
                            <th>Inicio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($datos['inversiones'])): ?>
                            <?php foreach ($datos['inversiones'] as $inv): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($inv['plataforma']) ?></strong></td>
                                    <td><strong>$<?= number_format($inv['monto_invertido'], 2) ?></strong></td>
                                    <td class="text-green"><strong><?= number_format($inv['tasa_retorno_mensual'], 2) ?>%</strong></td>
                                    <td><?= $inv['fecha_inicio'] ?></td>
                                    <td>
                                        <a href="index.php?action=eliminar_inversion&id=<?= $inv['id_inversion'] ?>" 
                                           class="action-link delete" 
                                           onclick="return confirm('¿Seguro que deseas eliminar esta inversión?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                    No tienes inversiones registradas. Registra una a la izquierda.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <script>
        function toggleAyuda() {
            const card = document.getElementById('ayudaInversiones');
            if (card) {
                card.classList.toggle('active');
            }
        }
    </script>

<?php require_once 'app/views/partials/layout_footer.php'; ?>