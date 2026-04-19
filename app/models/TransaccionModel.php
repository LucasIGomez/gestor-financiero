<?php
require_once 'Conexion.php';

class TransaccionModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Extrae el historial de transacciones combinando datos de la tabla categorías
    public function obtenerTransacciones($id_usuario) {
        // Utilizamos INNER JOIN para traer el nombre y tipo de flujo de la categoría asignada
        $sql = "SELECT t.id_transaccion, t.monto, t.descripcion, t.fecha_transaccion, 
                       c.nombre_categoria, c.tipo_flujo 
                FROM transacciones t
                INNER JOIN categorias c ON t.id_categoria = c.id_categoria
                WHERE t.id_usuario = :id_usuario 
                ORDER BY t.fecha_transaccion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Inserta un nuevo ingreso o gasto en la base de datos
    public function registrarTransaccion($id_usuario, $id_categoria, $monto, $descripcion, $fecha_transaccion) {
        $sql = "INSERT INTO transacciones (id_usuario, id_categoria, monto, descripcion, fecha_transaccion) 
                VALUES (:id_usuario, :id_categoria, :monto, :descripcion, :fecha_transaccion)";
        
        $stmt = $this->db->prepare($sql);
        
        // Vinculación segura de parámetros contra Inyección SQL
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_transaccion', $fecha_transaccion, PDO::PARAM_STR);
        
        return $stmt->execute(); // Retorna true si la inserción fue exitosa
    }
}
?>