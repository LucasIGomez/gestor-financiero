<?php

class InversionController {

    // Función principal para proyectar el interés compuesto año a año
    public function calcularProyeccion($inversion_inicial, $adicion_mensual, $tasa_anual, $anos) {
        $resultados = [];
        
        // Convertir la tasa porcentual a decimal
        $r = $tasa_anual / 100;
        // Capitalización mensual (12 veces al año)
        $n = 12; 
        
        $saldo_actual = $inversion_inicial;
        $total_aportado = $inversion_inicial;

        // Bucle para iterar sobre cada año de la proyección
        for ($i = 1; $i <= $anos; $i++) {
            
            // Bucle interno para capitalizar mes a mes
            for ($mes = 1; $mes <= 12; $mes++) {
                // Fórmula: Saldo * (1 + tasa_mensual) + nuevo_aporte
                $saldo_actual = $saldo_actual * (1 + ($r / $n)) + $adicion_mensual;
                $total_aportado += $adicion_mensual;
            }

            // Guardar el estado al final de cada año
            $resultados[] = [
                'ano' => $i,
                'total_aportado' => round($total_aportado, 2),
                'interes_ganado' => round($saldo_actual - $total_aportado, 2),
                'saldo_final' => round($saldo_actual, 2)
            ];
        }

        return $resultados;
    }
}
?>