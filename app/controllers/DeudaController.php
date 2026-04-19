<?php
require_once 'app/models/DeudaModel.php';

class DeudaController {
    private $modelo;

    public function __construct() {
        $this->modelo = new DeudaModel();
    }

    // Obtiene las deudas ya ordenadas por el Modelo (Método Avalancha)
    public function obtenerResumenAvalancha($id_usuario) {
        return $this->modelo->obtenerDeudasAvalancha($id_usuario);
    }

    // Simula el impacto de un pago extra en una deuda específica
    public function simularPagoExtra($saldo, $tasa_anual, $cuota_minima, $pago_extra) {
        // Convertir TNA (Tasa Nominal Anual) a tasa mensual decimal
        $r = ($tasa_anual / 100) / 12;
        
        $cuota_nueva = $cuota_minima + $pago_extra;

        $meses_normal = $this->calcularMesesAmortizacion($saldo, $r, $cuota_minima);
        $meses_extra = $this->calcularMesesAmortizacion($saldo, $r, $cuota_nueva);

        // Si la deuda es impagable con la cuota mínima (interés > cuota)
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

    // Algoritmo interno para calcular el tiempo de liquidación
    private function calcularMesesAmortizacion($P, $r, $A) {
        // Si el pago es menor o igual al interés mensual, la deuda crece o se estanca
        if ($A <= ($P * $r)) {
            return INF; 
        }
        
        // Fórmula matemática para el cálculo de cuotas
        $n = -log(1 - ($r * $P) / $A) / log(1 + $r);
        return $n;
    }
}
?>