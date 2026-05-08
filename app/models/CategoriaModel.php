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

    // Busca la categoría "Ahorros" del usuario. Si no existe, la crea automáticamente.
    public function obtenerOCrearCategoriaAhorro($id_usuario) {
        $sql = "SELECT id_categoria FROM categorias 
                WHERE id_usuario = :id_usuario AND nombre_categoria = 'Ahorros' AND tipo_flujo = 'gasto'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return $resultado['id_categoria'];
        } else {
            $sql_insert = "INSERT INTO categorias (id_usuario, nombre_categoria, tipo_flujo) 
                           VALUES (:id_usuario, 'Ahorros', 'gasto')";
            $stmt_insert = $this->db->prepare($sql_insert);
            $stmt_insert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt_insert->execute();
            return $this->db->lastInsertId();
        }
    }

    // Busca la categoría "Pago de Deudas" del usuario. Si no existe, la crea automáticamente.
    public function obtenerOCrearCategoriaPagoDeudas($id_usuario) {
        $sql = "SELECT id_categoria FROM categorias 
                WHERE id_usuario = :id_usuario AND nombre_categoria = 'Pago de Deudas' AND tipo_flujo = 'gasto'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return $resultado['id_categoria'];
        } else {
            $sql_insert = "INSERT INTO categorias (id_usuario, nombre_categoria, tipo_flujo) 
                           VALUES (:id_usuario, 'Pago de Deudas', 'gasto')";
            $stmt_insert = $this->db->prepare($sql_insert);
            $stmt_insert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt_insert->execute();
            return $this->db->lastInsertId();
        }
    }

}?>