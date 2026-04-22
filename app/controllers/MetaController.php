<?php
require_once 'app/models/MetaModel.php';
require_once 'app/models/TransaccionModel.php';
require_once 'app/models/CategoriaModel.php';

class MetaController {
    private $metaModel;
    private $transaccionModel;
    private $categoriaModel;

    public function __construct() {
        $this->metaModel = new MetaModel();
        $this->transaccionModel = new TransaccionModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function obtenerDatosMetas($id_usuario) {
        $metas = $this->metaModel->obtenerMetasUsuario($id_usuario);
        
        foreach ($metas as &$meta) {
            if ($meta['monto_objetivo'] > 0) {
                $porcentaje = ($meta['saldo_actual'] / $meta['monto_objetivo']) * 100;
                $meta['porcentaje_avance'] = $porcentaje > 100 ? 100 : round($porcentaje, 2);
            } else {
                $meta['porcentaje_avance'] = 0;
            }
        }
        return $metas;
    }

    public function procesarNuevaMeta($id_usuario, $nombre_meta, $monto_objetivo, $fecha_limite) {
        if ($monto_objetivo <= 0) return "Error: El monto objetivo debe ser mayor a cero.";
        if (strtotime($fecha_limite) < strtotime(date('Y-m-d'))) return "Error: La fecha límite debe ser en el futuro.";

        $exito = $this->metaModel->registrarMeta($id_usuario, $nombre_meta, $monto_objetivo, $fecha_limite);
        return $exito ? true : "Error: No se pudo guardar la meta financiera.";
    }

    public function procesarAhorro($id_meta, $id_usuario, $monto_deposito) {
        if ($monto_deposito <= 0) return "Error: El monto a ahorrar debe ser mayor a cero.";

        // 1. Sumar el dinero al saldo actual de la meta
        $exito_meta = $this->metaModel->agregarAhorro($id_meta, $id_usuario, $monto_deposito);

        if ($exito_meta) {
            // 2. Extraer o crear la categoría de ahorros
            $id_categoria_ahorro = $this->categoriaModel->obtenerOCrearCategoriaAhorro($id_usuario);
            
            // 3. Registrar el descuento automático en el flujo de caja
            $descripcion = "Depósito en Meta de Ahorro";
            $fecha_actual = date('Y-m-d');
            $this->transaccionModel->registrarTransaccion(
                $id_usuario, $id_categoria_ahorro, $monto_deposito, $descripcion, $fecha_actual
            );
            return true;
        }
        
        return "Error: No se pudo registrar el ahorro.";
    }
}
?>