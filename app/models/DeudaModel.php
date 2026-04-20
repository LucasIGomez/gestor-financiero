<?php
require_once 'Conexion.php';

class DeudaModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // [Existente] Extrae las deudas ordenadas para el Método Avalancha
    public function obtenerDeudasAvalancha($id_usuario) {
        $sql = "SELECT id_deuda, nombre_deuda, saldo_total, tasa_intereses, cuota_mensual 
                FROM deudas 
                WHERE id_usuario = :id_usuario 
                ORDER BY tasa_intereses DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // [NUEVO] Inserta una nueva deuda en la base de datos
    public function registrarDeuda($id_usuario, $nombre_deuda, $saldo_total, $tasa_intereses, $cuota_mensual) {
        $sql = "INSERT INTO deudas (id_usuario, nombre_deuda, saldo_total, tasa_intereses, cuota_mensual) 
                VALUES (:id_usuario, :nombre_deuda, :saldo_total, :tasa_intereses, :cuota_mensual)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':nombre_deuda', $nombre_deuda, PDO::PARAM_STR);
        $stmt->bindParam(':saldo_total', $saldo_total, PDO::PARAM_STR);
        $stmt->bindParam(':tasa_intereses', $tasa_intereses, PDO::PARAM_STR);
        $stmt->bindParam(':cuota_mensual', $cuota_mensual, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    // [NUEVO] Actualiza los valores de una deuda existente
    public function actualizarDeuda($id_deuda, $id_usuario, $nombre_deuda, $saldo_total, $tasa_intereses, $cuota_mensual) {
        // La condición AND id_usuario previene que un usuario modifique deudas de otro
        $sql = "UPDATE deudas 
                SET nombre_deuda = :nombre_deuda, 
                    saldo_total = :saldo_total, 
                    tasa_intereses = :tasa_intereses, 
                    cuota_mensual = :cuota_mensual 
                WHERE id_deuda = :id_deuda AND id_usuario = :id_usuario";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':nombre_deuda', $nombre_deuda, PDO::PARAM_STR);
        $stmt->bindParam(':saldo_total', $saldo_total, PDO::PARAM_STR);
        $stmt->bindParam(':tasa_intereses', $tasa_intereses, PDO::PARAM_STR);
        $stmt->bindParam(':cuota_mensual', $cuota_mensual, PDO::PARAM_STR);
        $stmt->bindParam(':id_deuda', $id_deuda, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>