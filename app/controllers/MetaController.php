<?php
require_once 'app/models/MetaModel.php';

class MetaController {
    private $metaModel;

    public function __construct() {
        $this->metaModel = new MetaModel();
    }

    // 1. Obtener y procesar las metas para la Vista
    public function obtenerDatosMetas($id_usuario) {
        $metas = $this->metaModel->obtenerMetasUsuario($id_usuario);
        
        // Calcular el porcentaje de completitud para cada meta
        foreach ($metas as &$meta) {
            if ($meta['monto_objetivo'] > 0) {
                $porcentaje = ($meta['saldo_actual'] / $meta['monto_objetivo']) * 100;
                // Limitamos el porcentaje visual al 100% en caso de que el ahorro supere la meta
                $meta['porcentaje_avance'] = $porcentaje > 100 ? 100 : round($porcentaje, 2);
            } else {
                $meta['porcentaje_avance'] = 0;
            }
        }
        
        return $metas;
    }

    // 2. Validar y procesar el registro de una nueva meta
    public function procesarNuevaMeta($id_usuario, $nombre_meta, $monto_objetivo, $fecha_limite) {
        if ($monto_objetivo <= 0) {
            return "Error: El monto objetivo debe ser mayor a cero.";
        }
        
        // Validación básica de fecha para asegurar que no se pongan metas en el pasado
        if (strtotime($fecha_limite) < strtotime(date('Y-m-d'))) {
            return "Error: La fecha límite debe ser en el futuro.";
        }

        $exito = $this->metaModel->registrarMeta($id_usuario, $nombre_meta, $monto_objetivo, $fecha_limite);
        return $exito ? true : "Error: No se pudo guardar la meta financiera.";
    }

    // 3. Validar y procesar un depósito (ahorro) a una meta existente
    public function procesarAhorro($id_meta, $id_usuario, $monto_deposito) {
        if ($monto_deposito <= 0) {
            return "Error: El monto a ahorrar debe ser mayor a cero.";
        }

        $exito = $this->metaModel->agregarAhorro($id_meta, $id_usuario, $monto_deposito);
        return $exito ? true : "Error: No se pudo registrar el ahorro.";
    }
}
?>