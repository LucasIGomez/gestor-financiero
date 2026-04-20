<?php

class ImpuestoController {

    // Simula el cálculo de retenciones y reserva fiscal mensual
    public function calcularReservaFiscal($ingresos_brutos, $gastos_deducibles, $porcentaje_iva, $porcentaje_ganancias, $porcentaje_iibb) {
        
        // Validaciones básicas de negocio
        if ($ingresos_brutos <= 0) {
            return "Error: Los ingresos brutos deben ser mayores a cero.";
        }

        // 1. Cálculo de IVA e Ingresos Brutos (Generalmente sobre el facturado bruto)
        $monto_iva = $ingresos_brutos * ($porcentaje_iva / 100);
        $monto_iibb = $ingresos_brutos * ($porcentaje_iibb / 100);

        // 2. Cálculo de Ganancias (Sobre la base imponible neta)
        $base_imponible = $ingresos_brutos - $gastos_deducibles;
        if ($base_imponible < 0) {
            $base_imponible = 0; // No se puede pagar impuesto a las ganancias sobre pérdidas
        }
        $monto_ganancias = $base_imponible * ($porcentaje_ganancias / 100);

        // 3. Totales
        $reserva_total = $monto_iva + $monto_iibb + $monto_ganancias;
        $ingreso_neto_real = $ingresos_brutos - $reserva_total;

        return [
            'monto_iva' => round($monto_iva, 2),
            'monto_iibb' => round($monto_iibb, 2),
            'monto_ganancias' => round($monto_ganancias, 2),
            'reserva_total' => round($reserva_total, 2),
            'ingreso_neto_real' => round($ingreso_neto_real, 2)
        ];
    }
}
?>