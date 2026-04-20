<?php
require_once 'app/models/DeudaModel.php';

class DeudaController {
    private $modelo;

    public function __construct() {
        $this->modelo = new DeudaModel();
    }

    // [Existente] Obtiene las deudas ya ordenadas por el Modelo
    public function obtenerResumenAvalancha($id_usuario) {
        return $this->modelo->obtenerDeudasAvalancha($id_usuario);
    }

    // [NUEVO] Procesa y valida el registro de una nueva deuda
    public function procesarNuevaDeuda($id_usuario, $nombre_deuda, $saldo_total, $tasa_intereses, $cuota_mensual) {
        $validacion = $this->validarDatosDeuda($saldo_total, $tasa_intereses, $cuota_mensual);
        if ($validacion !== true) {
            return $validacion; // Retorna el mensaje de error
        }

        $exito = $this->modelo->registrarDeuda($id_usuario, $nombre_deuda, $saldo_total, $tasa_intereses, $cuota_mensual);
        return $exito ? true : "Error: No se pudo registrar la deuda en la base de datos.";
    }

    // [NUEVO] Procesa y valida la actualización de una deuda existente
    public function procesarActualizacionDeuda($id_deuda, $id_usuario, $nombre_deuda, $saldo_total, $tasa_intereses, $cuota_mensual) {
        $validacion = $this->validarDatosDeuda($saldo_total, $tasa_intereses, $cuota_mensual);
        if ($validacion !== true) {
            return $validacion;
        }

        $exito = $this->modelo->actualizarDeuda($id_deuda, $id_usuario, $nombre_deuda, $saldo_total, $tasa_intereses, $cuota_mensual);
        return $exito ? true : "Error: No se pudo actualizar la deuda en la base de datos.";
    }

    // [NUEVO] Función privada para centralizar las reglas de negocio
    private function validarDatosDeuda($saldo_total, $tasa_intereses, $cuota_mensual) {
        if ($saldo_total <= 0) return "Error: El saldo total debe ser mayor a cero.";
        if ($tasa_intereses < 0) return "Error: La tasa de interés no puede ser negativa.";
        if ($cuota_mensual <= 0) return "Error: La cuota mensual debe ser mayor a cero.";
        return true;
    }

    // [Existente] Simula el impacto de un pago extra en una deuda específica
    public function simularPagoExtra($saldo, $tasa_anual, $cuota_minima, $pago_extra) {
        $r = ($tasa_anual / 100) / 12;
        $cuota_nueva = $cuota_minima + $pago_extra;

        $meses_normal = $this->calcularMesesAmortizacion($saldo, $r, $cuota_minima);
        $meses_extra = $this->calcularMesesAmortizacion($saldo, $r, $cuota_nueva);

        if ($meses_normal === INF) {
            return "La cuota mínima no cubre los intereses generados. Deuda impagable.";
        }

        $interes_normal = ($meses_normal * $cuota_minima) - $saldo;
        $interes_extra = ($meses_extra * $cuota_nueva) - $saldo;

        return [
            'meses_original' => ceil($meses_normal),
            'meses_con_extra' => ceil($meses_extra),
            'ahorro_meses' => ceil($meses_normal - $meses_extra),
            'ahorro_intereses' => round($interes_normal - $interes_extra, 2)
        ];
    }

    // [Existente] Algoritmo interno para calcular el tiempo de liquidación
    private function calcularMesesAmortizacion($P, $r, $A) {
        if ($A <= ($P * $r)) {
            return INF; 
        }
        $n = -log(1 - ($r * $P) / $A) / log(1 + $r);
        return $n;
    }
    
    // Extrae una deuda validando que pertenezca al usuario
    public function obtenerDeudaEspecifica($id_deuda, $id_usuario) {
        return $this->modelo->obtenerDeudaPorId($id_deuda, $id_usuario);
    }
}
?>