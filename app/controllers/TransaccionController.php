<?php
require_once 'app/models/TransaccionModel.php';
require_once 'app/models/CategoriaModel.php';
require_once 'app/models/DeudaModel.php';
require_once 'app/models/GastoRecurrenteModel.php';
require_once 'app/models/MetaModel.php'; // Integración del modelo de metas

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
        $this->metaModel = new MetaModel(); // Instanciación del modelo de metas
    }

    // Lógica del Pseudo-Cron de automatización de gastos fijos
    private function automatizarGastosRecurrentes($id_usuario) {
        $gastos_pendientes = $this->gastoRecurrenteModel->obtenerGastosPendientes($id_usuario);
        
        foreach ($gastos_pendientes as $gasto) {
            $fecha_actual = date('Y-m-d');
            $descripcion_automatica = $gasto['descripcion'] . ' (Autogenerado)';
            
            // Registra el gasto directamente en el flujo de caja
            $this->transaccionModel->registrarTransaccion(
                $id_usuario, 
                $gasto['id_categoria'], 
                $gasto['monto'], 
                $descripcion_automatica, 
                $fecha_actual
            );
            
            // Sella el registro para no volver a cobrarlo este mes
            $this->gastoRecurrenteModel->marcarComoProcesado($gasto['id_recurrente']);
        }
    }

    public function obtenerDatosDashboard($id_usuario) {
        // Ejecución del motor de automatización antes de renderizar la vista
        $this->automatizarGastosRecurrentes($id_usuario);

        // Extracción de datos de los distintos modelos
        $transacciones = $this->transaccionModel->obtenerTransacciones($id_usuario);
        $categorias = $this->categoriaModel->obtenerCategorias($id_usuario);
        $deudas = $this->deudaModel->obtenerDeudasAvalancha($id_usuario);

        $total_ingresos = 0;
        $total_gastos = 0;
        $total_deudas = 0;
        $gastos_por_categoria = [];

        // Sumatoria y agrupación de transacciones
        foreach ($transacciones as $transaccion) {
            if ($transaccion['tipo_flujo'] === 'ingreso') {
                $total_ingresos += $transaccion['monto'];
            } elseif ($transaccion['tipo_flujo'] === 'gasto') {
                $total_gastos += $transaccion['monto'];
                
                // Lógica de agrupación matemática para gráficos y alertas
                $nombre_cat = $transaccion['nombre_categoria'];
                if (!isset($gastos_por_categoria[$nombre_cat])) {
                    $gastos_por_categoria[$nombre_cat] = 0;
                }
                $gastos_por_categoria[$nombre_cat] += $transaccion['monto'];
            }
        }

        // Motor de Alertas de Presupuesto (Regla del 20%)
        $alertas = [];
        if ($total_ingresos > 0) {
            foreach ($gastos_por_categoria as $nombre_cat => $monto_gastado) {
                $porcentaje = ($monto_gastado / $total_ingresos) * 100;
                
                // Si el gasto en una categoría supera el 20% de los ingresos, generar alerta
                if ($porcentaje > 20) {
                    $alertas[] = "¡Atención! Has destinado el " . round($porcentaje, 1) . "% de tus ingresos a '$nombre_cat'. Evalúa reducir este gasto.";
                }
            }
        }

        // Sumatoria de pasivos
        foreach ($deudas as $deuda) {
            $total_deudas += $deuda['saldo_total'];
        }

        // Calcular el total de dinero apartado en todas las metas (ahorros reales)
        $metas = $this->metaModel->obtenerMetasUsuario($id_usuario);
        $total_ahorros = 0;
        foreach ($metas as $meta) {
            $total_ahorros += $meta['saldo_actual'];
        }

        // FÓRMULAS CORREGIDAS PARA EL BALANCE PATRIMONIAL
        $liquidez_actual = $total_ingresos - $total_gastos; // Los ahorros en metas ya están restados dentro de $total_gastos
        
        // El Patrimonio Neto suma la liquidez disponible, suma el efectivo guardado en las metas, y resta las deudas
        $patrimonio_neto = $liquidez_actual + $total_ahorros - $total_deudas;

        // Codificación a JSON para que la Vista y Chart.js puedan leerlos
        $grafico_etiquetas = json_encode(array_keys($gastos_por_categoria));
        $grafico_valores = json_encode(array_values($gastos_por_categoria));

        return [
            'transacciones'     => $transacciones,
            'categorias'        => $categorias,
            'ingresos'          => $total_ingresos,
            'gastos'            => $total_gastos,
            'liquidez'          => $liquidez_actual,
            'total_deudas'      => $total_deudas,
            'patrimonio_neto'   => $patrimonio_neto,
            'grafico_etiquetas' => $grafico_etiquetas,
            'grafico_valores'   => $grafico_valores,
            'alertas'           => $alertas
        ];
    }

    public function procesarNuevaTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion) {
        if ($monto <= 0) {
            return "Error: El monto de la transacción debe ser mayor a cero.";
        }
        $exito = $this->transaccionModel->registrarTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion);
        return $exito ? true : "Error: No se pudo registrar la transacción.";
    }

    // Procesa y valida el registro de una nueva plantilla de gasto recurrente
    public function procesarNuevoGastoRecurrente($id_usuario, $id_categoria, $monto, $descripcion, $dia_cobro) {
        if ($monto <= 0) return "Error: El monto debe ser mayor a cero.";
        if ($dia_cobro < 1 || $dia_cobro > 31) return "Error: El día de cobro debe estar entre 1 y 31.";
        
        $exito = $this->gastoRecurrenteModel->registrarGastoRecurrente($id_usuario, $id_categoria, $monto, $descripcion, $dia_cobro);
        return $exito ? true : "Error: No se pudo guardar la plantilla del gasto.";
    }

    // Extrae las categorías y las plantillas para enviarlas a la Vista de Gastos Fijos
    public function obtenerDatosGastosRecurrentes($id_usuario) {
        $categorias = $this->categoriaModel->obtenerCategorias($id_usuario);
        $plantillas = $this->gastoRecurrenteModel->obtenerPlantillasUsuario($id_usuario);
        return ['categorias' => $categorias, 'plantillas' => $plantillas];
    }
}
?>