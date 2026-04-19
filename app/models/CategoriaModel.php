<?php
require_once 'Conexion.php';

class CategoriaModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Extrae las categorías de un usuario para mostrarlas en el formulario de transacciones
    public function obtenerCategorias($id_usuario) {
        $sql = "SELECT id_categoria, nombre_categoria, tipo_flujo 
                FROM categorias 
                WHERE id_usuario = :id_usuario 
                ORDER BY tipo_flujo ASC, nombre_categoria ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>