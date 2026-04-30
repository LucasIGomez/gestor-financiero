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
}
?>