<?php
require_once 'Conexion.php';

class TransaccionModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Obtiene las transacciones cruzadas con su categoría, filtradas estrictamente al mes y año en curso
    public function obtenerTransacciones($id_usuario) {
        $sql = "SELECT t.*, c.nombre_categoria, c.tipo_flujo 
                FROM transacciones t 
                INNER JOIN categorias c ON t.id_categoria = c.id_categoria 
                WHERE t.id_usuario = :id_usuario 
                AND MONTH(t.fecha_transaccion) = MONTH(CURRENT_DATE()) 
                AND YEAR(t.fecha_transaccion) = YEAR(CURRENT_DATE())
                ORDER BY t.fecha_transaccion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registra una nueva transacción en el flujo de caja
    public function registrarTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion) {
        $sql = "INSERT INTO transacciones (id_usuario, id_categoria, monto, descripcion, fecha_transaccion) 
                VALUES (:id_usuario, :id_categoria, :monto, :descripcion, :fecha_transaccion)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_transaccion', $fecha_transaccion, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
?>