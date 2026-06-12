<?php
require_once 'app/models/InversionModel.php';
require_once 'app/models/MetaModel.php';

class InversionController {
    private $inversionModel;
    private $metaModel;

    public function __construct() {
        $this->inversionModel = new InversionModel();
        $this->metaModel = new MetaModel();
    }

    // Obtiene las inversiones activas, las metas de ahorro y genera recomendaciones del analista financiero
    public function obtenerDatosVista($id_usuario) {
        $inversiones = $this->inversionModel->obtenerInversionesUsuario($id_usuario);
        $metas = $this->metaModel->obtenerMetasUsuario($id_usuario);

        $analisis_metas = [];
        foreach ($metas as $meta) {
            $saldo_restante = max($meta['monto_objetivo'] - $meta['saldo_actual'], 0);
            
            // Calcular meses restantes utilizando DateTime
            $fecha_limite = new DateTime($meta['fecha_limite']);
            $fecha_actual = new DateTime();
            $diferencia = $fecha_actual->diff($fecha_limite);
            
            // Meses aproximados, garantizando al menos 1 para evitar divisiones por cero
            $meses_restantes = ($diferencia->y * 12) + $diferencia->m;
            if ($diferencia->d > 0) {
                $meses_restantes += 1;
            }
            if ($meses_restantes <= 0) {
                $meses_restantes = 1;
            }

            $deposito_sugerido = $saldo_restante / $meses_restantes;

            // Determinar horizonte de tiempo en días
            $dias_restantes = $diferencia->days;
            if ($fecha_limite < $fecha_actual) {
                $dias_restantes = 0;
            }

            // Reglas heurísticas del analista financiero
            if ($dias_restantes <= 30) {
                $recomendacion_plataforma = "Mercado Pago / Naranja X (Billeteras digitales)";
                $motivo = "Dado que tu meta vence en menos de 30 días, necesitas liquidez inmediata y no te conviene inmovilizar el capital en un Plazo Fijo.";
            } elseif ($dias_restantes <= 180) {
                $recomendacion_plataforma = "Plazo Fijo Tradicional (Tasa fija a 30 días)";
                $motivo = "Para un horizonte de 1 a 6 meses, un Plazo Fijo renovado mensualmente ofrece una de las tasas de retorno estables más altas sin riesgo de volatilidad.";
            } else {
                $recomendacion_plataforma = "Fondo Común de Inversión (FCI) o Plazo Fijo UVA";
                $motivo = "A largo plazo (más de 6 meses), es recomendable protegerse de la inflación mediante plazos fijos UVA o fondos comunes de inversión diversificados.";
            }

            $analisis_metas[] = [
                'meta' => $meta,
                'saldo_restante' => $saldo_restante,
                'meses_restantes' => $meses_restantes,
                'deposito_sugerido' => $deposito_sugerido,
                'recomendacion_plataforma' => $recomendacion_plataforma,
                'motivo' => $motivo
            ];
        }

        return [
            'inversiones' => $inversiones,
            'metas' => $metas,
            'analisis_metas' => $analisis_metas
        ];
    }

    // Registra una nueva inversión activa en la base de datos
    public function procesarNuevaInversion($post_data, $id_usuario) {
        $plataforma = $post_data['plataforma'] ?? '';
        $monto_invertido = floatval($post_data['monto_invertido'] ?? 0);
        $tasa_retorno_mensual = floatval($post_data['tasa_retorno_mensual'] ?? 0);
        $fecha_inicio = !empty($post_data['fecha_inicio']) ? $post_data['fecha_inicio'] : date('Y-m-d');

        if (empty($plataforma) || $monto_invertido <= 0 || $tasa_retorno_mensual < 0) {
            return "Error: Datos de inversión inválidos.";
        }

        $exito = $this->inversionModel->registrarInversion($id_usuario, $plataforma, $monto_invertido, $tasa_retorno_mensual, $fecha_inicio);
        return $exito ? true : "Error: No se pudo registrar la inversión.";
    }

    // Elimina una inversión activa del usuario
    public function procesarEliminarInversion($id_inversion, $id_usuario) {
        $exito = $this->inversionModel->eliminarInversion($id_inversion, $id_usuario);
        return $exito ? true : "Error: No se pudo eliminar la inversión.";
    }

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