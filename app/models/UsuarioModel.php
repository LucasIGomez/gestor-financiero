<?php
require_once 'Conexion.php';

class UsuarioModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Registra un nuevo usuario, encripta contraseña y asigna categorías mediante Transacciones
    public function registrarUsuario($nombre, $email, $password_hash) {
        try {
            // INICIO DE LA TRANSACCIÓN: Pausa el guardado definitivo en la BD
            $this->db->beginTransaction();

            // 1. Insertar el usuario
            $sql = "INSERT INTO usuarios (nombre, email, password_hash, fecha_registro) 
                    VALUES (:nombre, :email, :password_hash, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->execute();

            // 2. Capturar el ID asignado por MySQL
            $id_usuario_nuevo = $this->db->lastInsertId();

            // 3. Inyectar categorías base concatenando el ID entero (evita el error de parámetros múltiples en PDO)
            $sql_categorias = "INSERT INTO categorias (id_usuario, nombre_categoria, tipo_flujo) VALUES 
                ($id_usuario_nuevo, 'Sueldo', 'ingreso'),
                ($id_usuario_nuevo, 'Ventas', 'ingreso'),
                ($id_usuario_nuevo, 'Alimentación', 'gasto'),
                ($id_usuario_nuevo, 'Vivienda', 'gasto'),
                ($id_usuario_nuevo, 'Transporte', 'gasto'),
                ($id_usuario_nuevo, 'Servicios', 'gasto'),
                ($id_usuario_nuevo, 'Ocio', 'gasto'),
                ($id_usuario_nuevo, 'Pago de Deudas', 'gasto')";
            
            $stmt_cat = $this->db->prepare($sql_categorias);
            $stmt_cat->execute();

            // CONFIRMAR TRANSACCIÓN: Si todo salió bien, guarda los cambios definitivamente
            $this->db->commit();

            return true;

        } catch (PDOException $e) {
            // Revertir transacción en caso de fallo para evitar datos huérfanos
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            // Retorna falso para que el Controlador muestre el mensaje de error en la interfaz
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