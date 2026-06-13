<?php $page_title = 'Conexiones API'; require_once 'app/views/partials/layout_header.php'; ?>

    <div class="header-actions-row">
        <div class="page-header" style="margin-bottom: 0;">
            <h2>Centro de Integración y Conexión API</h2>
            <p>Vinculá tus billeteras virtuales y configurá la categorización automatizada inteligente</p>
        </div>
        <button type="button" class="info-btn" onclick="toggleAyuda()"><i class="fa-solid fa-circle-info"></i> Conceptos del Módulo</button>
    </div>

    <!-- Guía conceptual integrada (Ayuda interactiva) -->
    <div class="help-card" id="ayudaConexiones">
        <h4><i class="fa-solid fa-graduation-cap"></i> Educación Financiera: APIs y Open Banking</h4>
        <div class="help-grid">
            <div class="help-item">
                <strong>¿Qué es Open Banking?</strong>
                Es una tecnología segura que permite compartir tu información financiera (en modo lectura) con aplicaciones autorizadas para automatizar la contabilidad, el control de deudas y los rendimientos.
            </div>
            <div class="help-item">
                <strong>Seguridad y Encriptación</strong>
                ClariFi nunca almacena tus contraseñas ni tiene acceso a tu dinero. Las credenciales/tokens de API se encriptan con algoritmos bancarios AES-256 en la base de datos y se usan con permisos restringidos de solo lectura.
            </div>
            <div class="help-item">
                <strong>Mapeo Inteligente (Smart Rules)</strong>
                Dado que muchos comercios cobran mediante transferencias directas a cuentas personales, las Reglas Inteligentes te permiten asociar esos alias o nombres a una categoría (ej: "verduleria.pepe" -> Alimentación) para auto-categorizar futuros egresos.
            </div>
        </div>
    </div>

    <?php if (isset($_GET['sync']) && $_GET['sync'] == 1): ?>
        <div class="alert alert-success">
            <h4><i class="fa-solid fa-circle-check"></i> Sincronización Completada</h4>
            <p>Se descargaron y procesaron los últimos movimientos bancarios simulados. Las transacciones coincidentes se categorizaron automáticamente, y el resto se envió a "Por Clasificar".</p>
        </div>
    <?php endif; ?>

    <!-- HUB DE BILLETERAS -->
    <div class="stats-grid" style="margin-bottom: 28px;">
        
        <!-- Mercado Pago -->
        <?php
        $mp = null;
        foreach ($datos['conexiones'] as $con) {
            if ($con['billetera'] === 'mercado_pago') $mp = $con;
        }
        ?>
        <div class="stat-card <?= $mp ? 'positive' : '' ?>" style="text-align: left; display: flex; flex-direction: column; justify-content: space-between; min-height: 160px; padding: 20px;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 1.15rem; font-weight: 700; color: #009ee3;"><i class="fa-solid fa-wallet"></i> Mercado Pago</span>
                    <span class="priority-badge <?= $mp ? 'low' : 'high' ?>"><?= $mp ? 'Conectado' : 'Desconectado' ?></span>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary);">Rendimiento Actual: <strong class="text-green"><?= $datos['tasas_mercado']['mercado_pago'] ?>% TNA</strong></div>
                <?php if ($mp): ?>
                    <div style="font-size: 1.25rem; font-weight: 700; margin-top: 8px;">$<?= number_format($mp['saldo_simulado'], 2) ?></div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Alias: <?= htmlspecialchars($mp['alias_personal']) ?></div>
                <?php else: ?>
                    <div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 8px; font-style: italic;">Sin sincronización activa</div>
                <?php endif; ?>
            </div>
            <div style="margin-top: 14px;">
                <?php if ($mp): ?>
                    <a href="index.php?action=desconectar_billetera&id=<?= $mp['id_conexion'] ?>" class="btn btn-danger btn-sm" style="width: 100%;">Desconectar API</a>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-sm" style="width: 100%;" onclick="abrirModalConexion('mercado_pago', 'Mercado Pago')">Vincular Cuenta</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Naranja X -->
        <?php
        $nx = null;
        foreach ($datos['conexiones'] as $con) {
            if ($con['billetera'] === 'naranja_x') $nx = $con;
        }
        ?>
        <div class="stat-card <?= $nx ? 'positive' : '' ?>" style="text-align: left; display: flex; flex-direction: column; justify-content: space-between; min-height: 160px; padding: 20px;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 1.15rem; font-weight: 700; color: #ff5000;"><i class="fa-solid fa-wallet"></i> Naranja X</span>
                    <span class="priority-badge <?= $nx ? 'low' : 'high' ?>"><?= $nx ? 'Conectado' : 'Desconectado' ?></span>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary);">Rendimiento Actual: <strong class="text-green"><?= $datos['tasas_mercado']['naranja_x'] ?>% TNA</strong></div>
                <?php if ($nx): ?>
                    <div style="font-size: 1.25rem; font-weight: 700; margin-top: 8px;">$<?= number_format($nx['saldo_simulado'], 2) ?></div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Alias: <?= htmlspecialchars($nx['alias_personal']) ?></div>
                <?php else: ?>
                    <div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 8px; font-style: italic;">Sin sincronización activa</div>
                <?php endif; ?>
            </div>
            <div style="margin-top: 14px;">
                <?php if ($nx): ?>
                    <a href="index.php?action=desconectar_billetera&id=<?= $nx['id_conexion'] ?>" class="btn btn-danger btn-sm" style="width: 100%;">Desconectar API</a>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-sm" style="width: 100%;" onclick="abrirModalConexion('naranja_x', 'Naranja X')">Vincular Cuenta</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ualá -->
        <?php
        $ua = null;
        foreach ($datos['conexiones'] as $con) {
            if ($con['billetera'] === 'uala') $ua = $con;
        }
        ?>
        <div class="stat-card <?= $ua ? 'positive' : '' ?>" style="text-align: left; display: flex; flex-direction: column; justify-content: space-between; min-height: 160px; padding: 20px;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 1.15rem; font-weight: 700; color: #6366f1;"><i class="fa-solid fa-wallet"></i> Ualá</span>
                    <span class="priority-badge <?= $ua ? 'low' : 'high' ?>"><?= $ua ? 'Conectado' : 'Desconectado' ?></span>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary);">Rendimiento Actual: <strong class="text-green"><?= $datos['tasas_mercado']['uala'] ?>% TNA</strong></div>
                <?php if ($ua): ?>
                    <div style="font-size: 1.25rem; font-weight: 700; margin-top: 8px;">$<?= number_format($ua['saldo_simulado'], 2) ?></div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Alias: <?= htmlspecialchars($ua['alias_personal']) ?></div>
                <?php else: ?>
                    <div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 8px; font-style: italic;">Sin sincronización activa</div>
                <?php endif; ?>
            </div>
            <div style="margin-top: 14px;">
                <?php if ($ua): ?>
                    <a href="index.php?action=desconectar_billetera&id=<?= $ua['id_conexion'] ?>" class="btn btn-danger btn-sm" style="width: 100%;">Desconectar API</a>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-sm" style="width: 100%;" onclick="abrirModalConexion('uala', 'Ualá')">Vincular Cuenta</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lemon Cash -->
        <?php
        $lc = null;
        foreach ($datos['conexiones'] as $con) {
            if ($con['billetera'] === 'lemon_cash') $lc = $con;
        }
        ?>
        <div class="stat-card <?= $lc ? 'positive' : '' ?>" style="text-align: left; display: flex; flex-direction: column; justify-content: space-between; min-height: 160px; padding: 20px;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 1.15rem; font-weight: 700; color: #8ef700;"><i class="fa-solid fa-wallet"></i> Lemon Cash</span>
                    <span class="priority-badge <?= $lc ? 'low' : 'high' ?>"><?= $lc ? 'Conectado' : 'Desconectado' ?></span>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary);">Rendimiento Actual: <strong class="text-green"><?= $datos['tasas_mercado']['lemon_cash'] ?>% TNA</strong></div>
                <?php if ($lc): ?>
                    <div style="font-size: 1.25rem; font-weight: 700; margin-top: 8px;">$<?= number_format($lc['saldo_simulado'], 2) ?></div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 2px;">Alias: <?= htmlspecialchars($lc['alias_personal']) ?></div>
                <?php else: ?>
                    <div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 8px; font-style: italic;">Sin sincronización activa</div>
                <?php endif; ?>
            </div>
            <div style="margin-top: 14px;">
                <?php if ($lc): ?>
                    <a href="index.php?action=desconectar_billetera&id=<?= $lc['id_conexion'] ?>" class="btn btn-danger btn-sm" style="width: 100%;">Desconectar API</a>
                <?php else: ?>
                    <button type="button" class="btn btn-primary btn-sm" style="width: 100%;" onclick="abrirModalConexion('lemon_cash', 'Lemon Cash')">Vincular Cuenta</button>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- SECCIÓN DE DETALLES Y REGLAS -->
    <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 24px; align-items: start;">
        
        <!-- COLUMNA IZQUIERDA: ASESOR Y SIMULADOR DE API -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Widget Asesor de Rendimiento (Yield Advisor) -->
            <div class="card" style="border-left: 4px solid var(--accent); background: radial-gradient(circle at 100% 0%, rgba(99,102,241,0.05) 0%, transparent 50%), var(--bg-card);">
                <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(99,102,241,0.12); color: var(--accent); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-bottom: 12px;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Analista de Rendimiento y Tasas
                </div>
                
                <?php if (!empty($datos['recomendaciones'])): ?>
                    <?php foreach ($datos['recomendaciones'] as $rec): ?>
                        <div style="margin-bottom: 12px;">
                            <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-lightbulb text-yellow"></i> <?= htmlspecialchars($rec['titulo']) ?>
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                                <?= $rec['mensaje'] ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                        <i class="fa-solid fa-circle-check text-green"></i> Tus activos y saldos están optimizados entre tus billeteras conectadas para capturar las mejores tasas de rendimiento vigentes del mercado.
                    </p>
                <?php endif; ?>
            </div>

            <!-- Simulador de Sincronización Sandbox -->
            <div class="card" style="border-left: 4px solid var(--green);">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 10px;"><i class="fa-solid fa-rotate text-green"></i> Simulador de Sincronización (Sandbox API)</h3>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px; line-height: 1.5;">
                    Simulá la descarga de movimientos reales de tus billeteras conectadas. 
                    El sistema procesará cobros de rendimientos y transferencias egresadas aplicando tus reglas inteligentes en tiempo real.
                </p>
                <form action="index.php?action=sincronizar_billetera" method="POST">
                    <button type="submit" class="btn btn-success" style="width: 100%; justify-content: center;"><i class="fa-solid fa-cloud-arrow-down"></i> Sincronizar Billeteras Vinculadas</button>
                </form>
            </div>

        </div>

        <!-- COLUMNA DERECHA: REGLAS INTELIGENTES -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 10px;"><i class="fa-solid fa-gear text-accent"></i> Reglas de Categorización Inteligente</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 18px; line-height: 1.5;">
                Configurá reglas de palabras clave o alias frecuentes para clasificar de manera automática los movimientos entrantes por transferencias.
            </p>

            <!-- Formulario de Regla -->
            <form action="index.php?action=registrar_regla" method="POST" style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                <div class="form-group">
                    <label>Patrón de Coincidencia (Alias, CBU o Nombre)</label>
                    <input type="text" name="patron" required placeholder="Ej: verduleria.pepe, JUAN PEREZ, etc.">
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Categoría Destino</label>
                        <select name="id_categoria" required>
                            <option value="" disabled selected>Seleccionar...</option>
                            <?php foreach ($datos['categorias'] as $cat): ?>
                                <?php if ($cat['tipo_flujo'] === 'gasto'): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nombre de Fantasía (UX)</label>
                        <input type="text" name="nombre_fantasia" placeholder="Ej: Verdulería Pepe">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">Agregar Regla Inteligente</button>
            </form>

            <!-- Listado de Reglas -->
            <h4 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">Tus Reglas Activas:</h4>
            <?php if (!empty($datos['reglas'])): ?>
                <div style="display: flex; flex-direction: column; gap: 10px; max-height: 250px; overflow-y: auto; padding-right: 4px;">
                    <?php foreach ($datos['reglas'] as $regla): ?>
                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($regla['patron']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                    Mapea a: <span class="text-accent"><?= htmlspecialchars($regla['nombre_categoria']) ?></span>
                                    <?php if ($regla['nombre_fantasia']): ?>
                                        | Nombre: <em><?= htmlspecialchars($regla['nombre_fantasia']) ?></em>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="index.php?action=eliminar_regla&id=<?= $regla['id_regla'] ?>" class="action-link delete" style="font-size: 0.8rem; padding: 4px 8px;">✕</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; padding: 16px;">
                    No tenés reglas configuradas. Añadí una arriba para automatizar la clasificación.
                </p>
            <?php endif; ?>

        </div>

    </div>

    <!-- MODAL DE CONEXIÓN DE BILLETERA (Simulado) -->
    <div id="modalConexion" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
        <div class="auth-card" style="width: 100%; max-width: 440px; margin: 20px; animation: none;">
            <div class="brand">
                <h1 id="modal_titulo">Vincular Cuenta</h1>
                <p id="modal_subtitulo">Integración segura mediante OAuth Sandbox</p>
            </div>
            
            <form action="index.php?action=vincular_billetera" method="POST" onsubmit="console.log('Vinculando...');">
                <input type="hidden" name="billetera" id="modal_billetera">

                <div class="form-group">
                    <label>Access Token / API Key</label>
                    <input type="password" name="access_token" required placeholder="APP_USR-845183...">
                    <small style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 2px;">
                        Las llaves de producción se almacenan encriptadas con cifrado simétrico AES-256.
                    </small>
                </div>

                <div class="form-group">
                    <label>Tu Alias Personal (CVU/Alias)</label>
                    <input type="text" name="alias_personal" required placeholder="Ej: luki.pagos.mp">
                </div>

                <div class="form-group">
                    <label>Saldo Inicial de la Billetera ($)</label>
                    <input type="number" step="0.01" name="saldo_inicial" required min="0" value="150000.00">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Conectar Cuenta</button>
                    <button type="button" class="btn btn-cancel" onclick="cerrarModalConexion()" style="padding: 10px 16px;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAyuda() {
            const card = document.getElementById('ayudaConexiones');
            if (card) {
                card.classList.toggle('active');
            }
        }

        function abrirModalConexion(billetera, nombre) {
            document.getElementById('modal_billetera').value = billetera;
            document.getElementById('modal_titulo').innerText = "Vincular " + nombre;
            document.getElementById('modal_subtitulo').innerText = "Conectar API Sandbox de " + nombre;
            document.getElementById('modalConexion').style.display = 'flex';
        }

        function cerrarModalConexion() {
            document.getElementById('modalConexion').style.display = 'none';
        }
    </script>

<?php require_once 'app/views/partials/layout_footer.php'; ?>
