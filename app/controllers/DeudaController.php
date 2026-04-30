<?php
require_once 'app/models/DeudaModel.php';

class DeudaController {
    private $deudaModel;

    public function __construct() {
        $this->deudaModel = new DeudaModel();
    }

    // Extrae y simula el pago de deudas basado en el CFT
    public function obtenerDatosDeudas($id_usuario) {
        $deudas = $this->deudaModel->obtenerDeudasAvalancha($id_usuario);
        
        foreach ($deudas as &$deuda) {
            $pago_extra = 50000; 
            $saldo = $deuda['saldo_total'];
            $cuota = $deuda['cuota_mensual'];
            
            // Transformación matemática: ahora la capitalización se calcula sobre el CFT
            $tasa_mensual = ($deuda['cft'] / 12) / 100;

            if ($cuota > ($saldo * $tasa_mensual)) {
                $meses_originales = $this->calcularMesesAmortizacion($saldo, $tasa_mensual, $cuota);
                $meses_nuevos = $this->calcularMesesAmortizacion($saldo, $tasa_mensual, $cuota + $pago_extra);
                
                $deuda['meses_ahorrados'] = $meses_originales - $meses_nuevos;
                
                $costo_original = ($meses_originales * $cuota) - $saldo;
                $costo_nuevo = ($meses_nuevos * ($cuota + $pago_extra)) - $saldo;
                
                $deuda['intereses_ahorrados'] = $costo_original - $costo_nuevo;
            } else {
                $deuda['meses_ahorrados'] = 0;
                $deuda['intereses_ahorrados'] = 0;
            }
        }
        
        return $deudas;
    }

    public function obtenerDeuda($id_deuda, $id_usuario) {
        return $this->deudaModel->obtenerDeudaPorId($id_deuda, $id_usuario);
    }

    public function procesarNuevaDeuda($post_data, $id_usuario) {
        $datos = $this->empaquetarDatos($post_data, $id_usuario);
        
        $validacion = $this->validarDatosDeuda($datos);
        if ($validacion !== true) return $validacion;

        $exito = $this->deudaModel->registrarDeuda($datos);
        return $exito ? true : "Error: No se pudo registrar la deuda.";
    }

    public function procesarEdicionDeuda($post_data, $id_usuario) {
        $datos = $this->empaquetarDatos($post_data, $id_usuario);
        $datos['id_deuda'] = $post_data['id_deuda']; // Requerido para el UPDATE
        
        $validacion = $this->validarDatosDeuda($datos);
        if ($validacion !== true) return $validacion;

        $exito = $this->deudaModel->actualizarDeuda($datos);
        return $exito ? true : "Error: No se pudo actualizar la deuda.";
    }

    // Aisla el formateo de datos y el manejo de variables nulas
    private function empaquetarDatos($post, $id_usuario) {
        return [
            'id_usuario'      => $id_usuario,
            'nombre_deuda'    => $post['nombre_deuda'],
            'tipo_deuda'      => $post['tipo_deuda'],
            'saldo_total'     => $post['saldo_total'],
            'cft'             => $post['cft'],
            'tna'             => !empty($post['tna']) ? $post['tna'] : null,
            'tea'             => !empty($post['tea']) ? $post['tea'] : null,
            'cuota_mensual'   => !empty($post['cuota_mensual']) ? $post['cuota_mensual'] : 0,
            'limite_credito'  => !empty($post['limite_credito']) ? $post['limite_credito'] : null,
            'dia_cierre'      => !empty($post['dia_cierre']) ? $post['dia_cierre'] : null,
            'dia_vencimiento' => !empty($post['dia_vencimiento']) ? $post['dia_vencimiento'] : null,
            'cuotas_totales'  => !empty($post['cuotas_totales']) ? $post['cuotas_totales'] : null,
            'cuotas_pagadas'  => !empty($post['cuotas_pagadas']) ? $post['cuotas_pagadas'] : null,
            'fecha_inicio'    => !empty($post['fecha_inicio']) ? $post['fecha_inicio'] : null
        ];
    }

    // Inteligencia de negocio para validar la realidad bancaria
    private function validarDatosDeuda($datos) {
        if ($datos['saldo_total'] < 0) return "Error: El saldo total no puede ser negativo.";
        if ($datos['cft'] <= 0) return "Error: El CFT debe ser mayor a cero para el cálculo avalancha.";
        
        if ($datos['tipo_deuda'] === 'prestamo' && $datos['cuota_mensual'] <= 0) {
            return "Error: Los préstamos requieren una cuota mensual pactada mayor a cero.";
        }
        
        if ($datos['tipo_deuda'] === 'tarjeta_credito' && empty($datos['limite_credito'])) {
            return "Error: Las tarjetas de crédito requieren establecer un límite de crédito.";
        }

        return true;
    }

    private function calcularMesesAmortizacion($principal, $tasa_mensual, $pago_mensual) {
        if ($tasa_mensual == 0) return $principal / $pago_mensual;
        return -log(1 - ($tasa_mensual * $principal) / $pago_mensual) / log(1 + $tasa_mensual);
    }
}
?>