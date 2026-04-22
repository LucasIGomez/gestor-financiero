<?php
require_once 'Conexion.php';

class MetaModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // 1. Leer: Obtiene todas las metas activas del usuario ordenadas por vencimiento
    public function obtenerMetasUsuario($id_usuario) {
        $sql = "SELECT * FROM metas_financieras 
                WHERE id_usuario = :id_usuario 
                ORDER BY fecha_limite ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Crear: Registra un nuevo objetivo financiero
    public function registrarMeta($id_usuario, $nombre_meta, $monto_objetivo, $fecha_limite) {
        $sql = "INSERT INTO metas_financieras (id_usuario, nombre_meta, monto_objetivo, fecha_limite) 
                VALUES (:id_usuario, :nombre_meta, :monto_objetivo, :fecha_limite)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':nombre_meta', $nombre_meta, PDO::PARAM_STR);
        $stmt->bindParam(':monto_objetivo', $monto_objetivo, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_limite', $fecha_limite, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // 3. Actualizar (Ahorrar): Suma un depósito lógico al saldo actual de la meta
    public function agregarAhorro($id_meta, $id_usuario, $monto_deposito) {
        // La condición AND id_usuario = :id_usuario previene que un usuario altere metas ajenas
        $sql = "UPDATE metas_financieras 
                SET saldo_actual = saldo_actual + :monto_deposito 
                WHERE id_meta = :id_meta AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':monto_deposito', $monto_deposito, PDO::PARAM_STR);
        $stmt->bindParam(':id_meta', $id_meta, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>