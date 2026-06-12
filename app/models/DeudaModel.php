<?php
require_once 'Conexion.php';

class DeudaModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // 1. Obtiene las deudas ordenadas estrictamente por CFT (Método Avalancha Real)
    public function obtenerDeudasAvalancha($id_usuario) {
        $sql = "SELECT * FROM deudas WHERE id_usuario = :id_usuario ORDER BY cft DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Extrae una deuda específica (usado para cargar el formulario de Edición)
    public function obtenerDeudaPorId($id_deuda, $id_usuario) {
        $sql = "SELECT * FROM deudas WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Registra una nueva deuda (Préstamo o Tarjeta)
    public function registrarDeuda($datos) {
        // Utilizamos un array $datos donde las llaves coinciden exactamente con los placeholders (:llave)
        $sql = "INSERT INTO deudas (id_usuario, nombre_deuda, tipo_deuda, saldo_total, cft, tna, tea, cuota_mensual, limite_credito, dia_cierre, dia_vencimiento, cuotas_totales, cuotas_pagadas, fecha_inicio) 
                VALUES (:id_usuario, :nombre_deuda, :tipo_deuda, :saldo_total, :cft, :tna, :tea, :cuota_mensual, :limite_credito, :dia_cierre, :dia_vencimiento, :cuotas_totales, :cuotas_pagadas, :fecha_inicio)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($datos);
    }

    // 4. Actualiza una deuda existente
    public function actualizarDeuda($datos) {
        $sql = "UPDATE deudas SET 
                nombre_deuda = :nombre_deuda, 
                tipo_deuda = :tipo_deuda,
                saldo_total = :saldo_total, 
                cft = :cft, 
                tna = :tna, 
                tea = :tea, 
                cuota_mensual = :cuota_mensual,
                limite_credito = :limite_credito, 
                dia_cierre = :dia_cierre, 
                dia_vencimiento = :dia_vencimiento, 
                cuotas_totales = :cuotas_totales, 
                cuotas_pagadas = :cuotas_pagadas, 
                fecha_inicio = :fecha_inicio
                WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($datos);
    }

    // Obtiene solo las tarjetas de crédito del usuario para el menú desplegable
    public function obtenerTarjetasCredito($id_usuario) {
        $sql = "SELECT id_deuda, nombre_deuda FROM deudas WHERE id_usuario = :id_usuario AND tipo_deuda = 'tarjeta_credito'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Suma automáticamente un nuevo gasto al saldo de la tarjeta
    public function sumarGastoTarjeta($id_deuda, $id_usuario, $monto) {
        $sql = "UPDATE deudas SET saldo_total = saldo_total + :monto 
                WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario AND tipo_deuda = 'tarjeta_credito'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // 5. Elimina una deuda específica de la base de datos
    public function eliminarDeuda($id_deuda, $id_usuario) {
        $sql = "DELETE FROM deudas WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // 6. Registra un pago contra una deuda (tarjeta o préstamo): resta saldo y sube cuotas pagadas
    public function registrarPagoDeuda($id_deuda, $id_usuario, $monto) {
        // Restar el monto del saldo total
        $sql = "UPDATE deudas SET saldo_total = GREATEST(saldo_total - :monto, 0) 
                WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // Si es préstamo, incrementar cuotas pagadas
        $sql2 = "UPDATE deudas SET cuotas_pagadas = cuotas_pagadas + 1 
                 WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario AND tipo_deuda = 'prestamo'";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
        $stmt2->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt2->execute();
    }

    // Obtiene las deudas ordenadas por el método Bola de Nieve (saldo total ascendente)
    public function obtenerDeudasBolaNieve($id_usuario) {
        $sql = "SELECT * FROM deudas WHERE id_usuario = :id_usuario ORDER BY saldo_total ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registra una amortización anticipada (pago adelantado) restando saldo de deuda y guardando historial
    public function registrarPagoAdelantado($id_deuda, $id_usuario, $monto_pagado, $descuento_obtenido, $fecha_pago) {
        $total_amortizacion = $monto_pagado + $descuento_obtenido;

        // 1. Reducir el saldo_total de la deuda
        $sql = "UPDATE deudas SET saldo_total = GREATEST(saldo_total - :total_amortizacion, 0)
                WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':total_amortizacion', $total_amortizacion, PDO::PARAM_STR);
        $stmt->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // 2. Si es un préstamo, registrar la cuota pagada
        $deuda = $this->obtenerDeudaPorId($id_deuda, $id_usuario);
        if ($deuda && $deuda['tipo_deuda'] === 'prestamo' && $deuda['cuota_mensual'] > 0) {
            $cuotas_adelantadas = ceil($total_amortizacion / $deuda['cuota_mensual']);
            $sql2 = "UPDATE deudas SET cuotas_pagadas = LEAST(cuotas_pagadas + :cuotas, cuotas_totales)
                     WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->bindParam(':cuotas', $cuotas_adelantadas, PDO::PARAM_INT);
            $stmt2->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
            $stmt2->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt2->execute();
        }

        // 3. Registrar en la tabla pagos_adelantados
        $sql3 = "INSERT INTO pagos_adelantados (id_deuda, monto_pagado, descuento_obtenido, fecha_pago)
                 VALUES (:id_deuda, :monto_pagado, :descuento_obtenido, :fecha_pago)";
        $stmt3 = $this->db->prepare($sql3);
        $stmt3->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
        $stmt3->bindParam(':monto_pagado', $monto_pagado, PDO::PARAM_STR);
        $stmt3->bindParam(':descuento_obtenido', $descuento_obtenido, PDO::PARAM_STR);
        $stmt3->bindParam(':fecha_pago', $fecha_pago, PDO::PARAM_STR);
        return $stmt3->execute();
    }
}
?>