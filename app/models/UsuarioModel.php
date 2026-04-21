<?php
require_once 'Conexion.php';

class UsuarioModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Registra un nuevo usuario, encripta contraseña y asigna categorías por defecto
    public function registrarUsuario($nombre, $email, $password_hash) {
        try {
            // 1. Insertar el usuario
            $sql = "INSERT INTO usuarios (nombre, email, password_hash, fecha_registro) 
                    VALUES (:nombre, :email, :password_hash, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->execute();

            // 2. Capturar el ID asignado por MySQL al nuevo usuario
            $id_usuario_nuevo = $this->db->lastInsertId();

            // 3. Inyectar categorías base atadas a este nuevo usuario
            $sql_categorias = "INSERT INTO categorias (id_usuario, nombre_categoria, tipo_flujo) VALUES 
                (:id, 'Sueldo', 'ingreso'),
                (:id, 'Ventas', 'ingreso'),
                (:id, 'Alimentación', 'gasto'),
                (:id, 'Vivienda', 'gasto'),
                (:id, 'Transporte', 'gasto'),
                (:id, 'Servicios', 'gasto'),
                (:id, 'Ocio', 'gasto'),
                (:id, 'Pago de Deudas', 'gasto')";
            
            $stmt_cat = $this->db->prepare($sql_categorias);
            $stmt_cat->bindParam(':id', $id_usuario_nuevo, PDO::PARAM_INT);
            $stmt_cat->execute();

            return true;
        } catch (PDOException $e) {
            // Retorna falso si el correo ya existe
            return false; 
        }
    }

    // Extrae los datos del usuario basándose en su correo electrónico
    public function obtenerUsuarioPorEmail($email) {
        $sql = "SELECT id_usuario, nombre, password_hash FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>