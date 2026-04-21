<?php
require_once 'Conexion.php';

class UsuarioModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Registra un nuevo usuario con la contraseña ya encriptada y la fecha actual
    public function registrarUsuario($nombre, $email, $password_hash) {
        try {
            // Inyectamos la función nativa NOW() de MySQL para generar la marca de tiempo
            $sql = "INSERT INTO usuarios (nombre, email, password_hash, fecha_registro) 
                    VALUES (:nombre, :email, :password_hash, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            // Restauramos el retorno falso si el correo ya existe
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