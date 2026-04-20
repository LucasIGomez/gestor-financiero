<?php
require_once 'app/models/TransaccionModel.php';
require_once 'app/models/CategoriaModel.php';
require_once 'app/models/DeudaModel.php'; // 1. Importamos el modelo de deudas

class TransaccionController {
    private $transaccionModel;
    private $categoriaModel;
    private $deudaModel;

    public function __construct() {
        $this->transaccionModel = new TransaccionModel();
        $this->categoriaModel = new CategoriaModel();
        $this->deudaModel = new DeudaModel(); // 2. Instanciamos el modelo
    }

    public function obtenerDatosDashboard($id_usuario) {
        $transacciones = $this->transaccionModel->obtenerTransacciones($id_usuario);
        $categorias = $this->categoriaModel->obtenerCategorias($id_usuario);
        
        // 3. Obtenemos las deudas del usuario
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

        // 4. Sumamos el saldo total de todas las deudas activas
        foreach ($deudas as $deuda) {
            $total_deudas += $deuda['saldo_total'];
        }

        $liquidez_actual = $total_ingresos - $total_gastos;
        
        // 5. Cálculo del Patrimonio Neto Real
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