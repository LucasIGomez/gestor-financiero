<?php
require_once 'app/models/TransaccionModel.php';
require_once 'app/models/CategoriaModel.php';
require_once 'app/models/DeudaModel.php';
require_once 'app/models/GastoRecurrenteModel.php';
require_once 'app/models/MetaModel.php';

class TransaccionController {
    private $transaccionModel;
    private $categoriaModel;
    private $deudaModel;
    private $gastoRecurrenteModel;
    private $metaModel;

    public function __construct() {
        $this->transaccionModel = new TransaccionModel();
        $this->categoriaModel = new CategoriaModel();
        $this->deudaModel = new DeudaModel();
        $this->gastoRecurrenteModel = new GastoRecurrenteModel();
        $this->metaModel = new MetaModel();
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

    public function obtenerDatosDashboard($id_usuario) {
        // Ejecutar automatización
        $this->automatizarGastosRecurrentes($id_usuario);

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

        $total_deudas = 0;
        foreach ($deudas as $deuda) {
            $total_deudas += $deuda['saldo_total'];
        }

        $total_ahorros = 0;
        foreach ($metas as $meta) {
            $total_ahorros += $meta['saldo_actual'];
        }

        $liquidez_actual = $total_ingresos - $total_gastos;
        $patrimonio_neto = $liquidez_actual + $total_ahorros - $total_deudas;

        // NUEVO: Algoritmo de Gasto Diario Seguro (Safe-to-Spend)
        $dias_en_mes = date('t'); // Retorna la cantidad total de días del mes actual (ej. 28, 30, 31)
        $dia_actual = date('j');  // Retorna el día actual del mes (ej. 15)
        $dias_restantes = $dias_en_mes - $dia_actual + 1; // Sumamos 1 para incluir el día de hoy como gastable

        // Si la liquidez es 0 o negativa, el límite diario cae a 0 para no agravar la deuda
        $limite_diario_seguro = $liquidez_actual > 0 ? ($liquidez_actual / $dias_restantes) : 0;

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
            'tarjetas'             => $tarjetas
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
}
?>