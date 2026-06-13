<?php
require_once 'app/models/TransaccionModel.php';
require_once 'app/models/CategoriaModel.php';
require_once 'app/models/DeudaModel.php';
require_once 'app/models/GastoRecurrenteModel.php';
require_once 'app/models/MetaModel.php';
require_once 'app/models/PresupuestoModel.php';
require_once 'app/models/HistoricoModel.php';
require_once 'app/models/ConexionModel.php';

class TransaccionController {
    private $transaccionModel;
    private $categoriaModel;
    private $deudaModel;
    private $gastoRecurrenteModel;
    private $metaModel;
    private $presupuestoModel;
    private $historicoModel;
    private $conexionModel;

    public function __construct() {
        $this->transaccionModel = new TransaccionModel();
        $this->categoriaModel = new CategoriaModel();
        $this->deudaModel = new DeudaModel();
        $this->gastoRecurrenteModel = new GastoRecurrenteModel();
        $this->metaModel = new MetaModel();
        $this->presupuestoModel = new PresupuestoModel();
        $this->historicoModel = new HistoricoModel();
        $this->conexionModel = new ConexionModel();
    }

    // Lógica del Pseudo-Cron de automatización de gastos fijos
    private function automatizarGastosRecurrentes($id_usuario) {
        $gastos_pendientes = $this->gastoRecurrenteModel->obtenerGastosPendientes($id_usuario);
        
        foreach ($gastos_pendientes as $gasto) {
            $fecha_actual = date('Y-m-d');
            $descripcion_automatica = $gasto['descripcion'] . ' (Autogenerado)';
            
            $this->transaccionModel->registrarTransaccion(
                $id_usuario, 
                $gasto['id_categoria'], 
                $gasto['monto'], 
                $descripcion_automatica, 
                $fecha_actual
            );
            
            $this->gastoRecurrenteModel->marcarComoProcesado($gasto['id_recurrente']);
        }
    }

    // Automatiza la amortización de cuotas/pagos mínimos al llegar la fecha de vencimiento
    private function automatizarDeudasVencidas($id_usuario) {
        $deudas = $this->deudaModel->obtenerDeudasAvalancha($id_usuario);
        $fecha_actual = date('Y-m-d');
        $dia_actual = intval(date('j'));
        
        foreach ($deudas as $deuda) {
            if ($deuda['saldo_total'] > 0 && $deuda['dia_vencimiento'] > 0) {
                if ($dia_actual >= intval($deuda['dia_vencimiento'])) {
                    if (!$this->transaccionModel->existePagoDeudaEsteMes($deuda['id_deuda'])) {
                        $id_categoria = $this->categoriaModel->obtenerOCrearCategoriaPagoDeudas($id_usuario);
                        
                        $monto_pago = $deuda['cuota_mensual'];
                        if ($deuda['tipo_deuda'] === 'tarjeta_credito' && $monto_pago <= 0) {
                            $monto_pago = $deuda['saldo_total'];
                        }
                        
                        if ($monto_pago > 0) {
                            $monto_pago = min($monto_pago, $deuda['saldo_total']);
                            $descripcion_automatica = "Cuota automática: " . $deuda['nombre_deuda'];
                            
                            // 1. Registrar la transacción de egreso
                            $this->transaccionModel->registrarTransaccion(
                                $id_usuario, 
                                $id_categoria, 
                                $monto_pago, 
                                $descripcion_automatica, 
                                $fecha_actual,
                                $deuda['id_deuda']
                            );
                            
                            // 2. Actualizar el saldo e historial de la deuda
                            $this->deudaModel->registrarPagoDeuda($deuda['id_deuda'], $id_usuario, $monto_pago);
                        }
                    }
                }
            }
        }
    }

    public function obtenerDatosDashboard($id_usuario) {
        // Ejecutar automatizaciones
        $this->automatizarGastosRecurrentes($id_usuario);
        $this->automatizarDeudasVencidas($id_usuario);

        // Extraer datos (Nota: transacciones ahora solo trae las del mes actual)
        $transacciones = $this->transaccionModel->obtenerTransacciones($id_usuario);
        $categorias = $this->categoriaModel->obtenerCategorias($id_usuario);
        $deudas = $this->deudaModel->obtenerDeudasAvalancha($id_usuario);
        $metas = $this->metaModel->obtenerMetasUsuario($id_usuario);
        $tarjetas = $this->deudaModel->obtenerTarjetasCredito($id_usuario);

        $total_ingresos = 0;
        $total_gastos = 0;
        $gastos_por_categoria = [];

        foreach ($transacciones as $transaccion) {
            if ($transaccion['tipo_flujo'] === 'ingreso') {
                $total_ingresos += $transaccion['monto'];
            } elseif ($transaccion['tipo_flujo'] === 'gasto') {
                $total_gastos += $transaccion['monto'];
                
                $nombre_cat = $transaccion['nombre_categoria'];
                if (!isset($gastos_por_categoria[$nombre_cat])) {
                    $gastos_por_categoria[$nombre_cat] = 0;
                }
                $gastos_por_categoria[$nombre_cat] += $transaccion['monto'];
            }
        }

        $alertas = [];
        if ($total_ingresos > 0) {
            foreach ($gastos_por_categoria as $nombre_cat => $monto_gastado) {
                $porcentaje = ($monto_gastado / $total_ingresos) * 100;
                if ($porcentaje > 20) {
                    $alertas[] = "¡Atención! Has destinado el " . round($porcentaje, 1) . "% de tus ingresos de este mes a '$nombre_cat'. Evalúa reducir este gasto.";
                }
            }
        }

        // Alerta si hay transacciones pendientes de clasificar
        $sin_clasificar_count = 0;
        foreach ($transacciones as $t) {
            if ($t['nombre_categoria'] === 'Por Clasificar') {
                $sin_clasificar_count++;
            }
        }
        if ($sin_clasificar_count > 0) {
            $alertas[] = "Tenés <strong>$sin_clasificar_count</strong> transferencias sin categorizar. <a href='index.php?action=conexiones' style='font-weight:700; text-decoration:underline; color:inherit;'>Clasificalos ahora</a> para ordenarlas.";
        }

        $total_deudas = 0;
        foreach ($deudas as $deuda) {
            $total_deudas += $deuda['saldo_total'];
        }

        $total_ahorros = 0;
        foreach ($metas as $meta) {
            $total_ahorros += $meta['saldo_actual'];
        }

        // Sumar saldos de billeteras conectadas
        $conexiones = $this->conexionModel->obtenerConexionesUsuario($id_usuario);
        $saldo_billeteras = 0.00;
        foreach ($conexiones as $con) {
            $saldo_billeteras += (float)$con['saldo_simulado'];
        }

        $liquidez_actual = ($total_ingresos - $total_gastos) + $saldo_billeteras;
        $patrimonio_neto = $liquidez_actual + $total_ahorros - $total_deudas;

        // NUEVO: Algoritmo de Gasto Diario Seguro (Safe-to-Spend)
        $dias_en_mes = date('t'); // Retorna la cantidad total de días del mes actual (ej. 28, 30, 31)
        $dia_actual = date('j');  // Retorna el día actual del mes (ej. 15)
        $dias_restantes = $dias_en_mes - $dia_actual + 1; // Sumamos 1 para incluir el día de hoy como gastable

        // Si la liquidez es 0 o negativa, el límite diario cae a 0 para no agravar la deuda
        $limite_diario_seguro = $liquidez_actual > 0 ? ($liquidez_actual / $dias_restantes) : 0;

        // Guardar la instantánea en tiempo real del mes actual
        $periodo_actual = date('Y-m-01');
        $this->historicoModel->guardarInstantaneaMensual(
            $id_usuario,
            $periodo_actual,
            $total_ingresos,
            $total_gastos,
            $total_deudas,
            $total_ahorros,
            $patrimonio_neto
        );

        // Obtener trayectoria histórica y presupuestos
        $trayectoria = $this->historicoModel->obtenerTrayectoriaPatrimonial($id_usuario);
        $presupuestos = $this->presupuestoModel->obtenerPresupuestosYConsumos($id_usuario, $periodo_actual);

        $grafico_etiquetas = json_encode(array_keys($gastos_por_categoria));
        $grafico_valores = json_encode(array_values($gastos_por_categoria));

        return [
            'transacciones'        => $transacciones,
            'categorias'           => $categorias,
            'ingresos'             => $total_ingresos,
            'gastos'               => $total_gastos,
            'liquidez'             => $liquidez_actual,
            'total_deudas'         => $total_deudas,
            'patrimonio_neto'      => $patrimonio_neto,
            'grafico_etiquetas'    => $grafico_etiquetas,
            'grafico_valores'      => $grafico_valores,
            'alertas'              => $alertas,
            // Empaquetamos las variables del nuevo algoritmo para la Vista
            'limite_diario_seguro' => $limite_diario_seguro,
            'dias_restantes'       => $dias_restantes,
            'tarjetas'             => $tarjetas,
            'trayectoria'          => $trayectoria,
            'presupuestos'         => $presupuestos
        ];
    }

    public function procesarNuevaTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion, $id_deuda = null) {
        if ($monto <= 0) return "Error: El monto de la transacción debe ser mayor a cero.";
        
        // Filtramos para asegurar que si viene vacío desde HTML, se convierta en null nativo
        $id_deuda_procesado = !empty($id_deuda) ? $id_deuda : null;

        $exito = $this->transaccionModel->registrarTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion, $id_deuda_procesado);
        
        if ($exito) {
            // Si la transacción se guardó y se usó una tarjeta, le sumamos la deuda automáticamente
            if ($id_deuda_procesado) {
                $this->deudaModel->sumarGastoTarjeta($id_deuda_procesado, $id_usuario, $monto);
            }
            return true;
        }
        
        return "Error: No se pudo registrar la transacción.";
    }

    public function procesarNuevoGastoRecurrente($id_usuario, $id_categoria, $monto, $descripcion, $dia_cobro) {
        if ($monto <= 0) return "Error: El monto debe ser mayor a cero.";
        if ($dia_cobro < 1 || $dia_cobro > 31) return "Error: El día de cobro debe estar entre 1 y 31.";
        
        $exito = $this->gastoRecurrenteModel->registrarGastoRecurrente($id_usuario, $id_categoria, $monto, $descripcion, $dia_cobro);
        return $exito ? true : "Error: No se pudo guardar la plantilla del gasto.";
    }

    public function obtenerDatosGastosRecurrentes($id_usuario) {
        $categorias = $this->categoriaModel->obtenerCategorias($id_usuario);
        $plantillas = $this->gastoRecurrenteModel->obtenerPlantillasUsuario($id_usuario);
        return ['categorias' => $categorias, 'plantillas' => $plantillas];
    }

    public function procesarEstablecerPresupuesto($id_usuario, $id_categoria, $monto_limite, $periodo) {
        if ($monto_limite < 0) return "Error: El monto límite no puede ser negativo.";
        $exito = $this->presupuestoModel->establecerPresupuesto($id_usuario, $id_categoria, $monto_limite, $periodo);
        return $exito ? true : "Error: No se pudo registrar el presupuesto.";
    }
}
?>