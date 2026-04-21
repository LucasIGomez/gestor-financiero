<?php
require_once 'app/models/TransaccionModel.php';
require_once 'app/models/CategoriaModel.php';
require_once 'app/models/DeudaModel.php';
require_once 'app/models/GastoRecurrenteModel.php'; // 1. Importación del nuevo modelo

class TransaccionController {
    private $transaccionModel;
    private $categoriaModel;
    private $deudaModel;
    private $gastoRecurrenteModel;

    public function __construct() {
        $this->transaccionModel = new TransaccionModel();
        $this->categoriaModel = new CategoriaModel();
        $this->deudaModel = new DeudaModel();
        $this->gastoRecurrenteModel = new GastoRecurrenteModel(); // 2. Instanciación
    }

    // 3. Lógica del Pseudo-Cron de automatización
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
        // 4. Ejecución del motor de automatización antes de renderizar la vista
        $this->automatizarGastosRecurrentes($id_usuario);

        // Bloque original de cálculo de balances
        $transacciones = $this->transaccionModel->obtenerTransacciones($id_usuario);
        $categorias = $this->categoriaModel->obtenerCategorias($id_usuario);
        $deudas = $this->deudaModel->obtenerDeudasAvalancha($id_usuario);

        $total_ingresos = 0;
        $total_gastos = 0;
        $total_deudas = 0;

        foreach ($transacciones as $transaccion) {
            if ($transaccion['tipo_flujo'] === 'ingreso') {
                $total_ingresos += $transaccion['monto'];
            } elseif ($transaccion['tipo_flujo'] === 'gasto') {
                $total_gastos += $transaccion['monto'];
            }
        }

        foreach ($deudas as $deuda) {
            $total_deudas += $deuda['saldo_total'];
        }

        $liquidez_actual = $total_ingresos - $total_gastos;
        $patrimonio_neto = $liquidez_actual - $total_deudas;

        return [
            'transacciones'   => $transacciones,
            'categorias'      => $categorias,
            'ingresos'        => $total_ingresos,
            'gastos'          => $total_gastos,
            'liquidez'        => $liquidez_actual,
            'total_deudas'    => $total_deudas,
            'patrimonio_neto' => $patrimonio_neto
        ];
    }

    public function procesarNuevaTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion) {
        if ($monto <= 0) {
            return "Error: El monto de la transacción debe ser mayor a cero.";
        }
        $exito = $this->transaccionModel->registrarTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion);
        return $exito ? true : "Error: No se pudo registrar la transacción.";
    }
}
?>