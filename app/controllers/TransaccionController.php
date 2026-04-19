<?php
require_once 'app/models/TransaccionModel.php';
require_once 'app/models/CategoriaModel.php';

class TransaccionController {
    private $transaccionModel;
    private $categoriaModel;

    public function __construct() {
        $this->transaccionModel = new TransaccionModel();
        $this->categoriaModel = new CategoriaModel();
    }

    // Centraliza la obtención de datos y cálculos matemáticos para el Dashboard
    public function obtenerDatosDashboard($id_usuario) {
        $transacciones = $this->transaccionModel->obtenerTransacciones($id_usuario);
        $categorias = $this->categoriaModel->obtenerCategorias($id_usuario);

        $total_ingresos = 0;
        $total_gastos = 0;

        // Iterar sobre las transacciones para calcular subtotales y balance
        foreach ($transacciones as $transaccion) {
            if ($transaccion['tipo_flujo'] === 'ingreso') {
                $total_ingresos += $transaccion['monto'];
            } elseif ($transaccion['tipo_flujo'] === 'gasto') {
                $total_gastos += $transaccion['monto'];
            }
        }

        $balance_total = $total_ingresos - $total_gastos;

        // Retorna un arreglo asociativo con todos los datos empaquetados para la Vista
        return [
            'transacciones' => $transacciones,
            'categorias'    => $categorias,
            'ingresos'      => $total_ingresos,
            'gastos'        => $total_gastos,
            'balance'       => $balance_total
        ];
    }

    // Procesa y valida la inserción de una nueva transacción
    public function procesarNuevaTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion) {
        // Validación básica de negocio: El monto no puede ser negativo o cero
        if ($monto <= 0) {
            return "Error: El monto de la transacción debe ser mayor a cero.";
        }

        $exito = $this->transaccionModel->registrarTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion);
        
        if ($exito) {
            return true;
        } else {
            return "Error: No se pudo registrar la transacción en la base de datos.";
        }
    }
}
?>