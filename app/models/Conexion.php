<?php

class Conexion {
    private $host = '127.0.0.1';
    private $db_name = 'gestor_financiero';
    private $username = 'root';
    private $password = ''; // Laragon no asigna contraseña a root por defecto
    private $conn;

    public function conectar() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            // Opciones de seguridad y manejo de errores
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $opciones);
            
        } catch(PDOException $e) {
            // En un entorno de producción, esto debería ir a un archivo .log
            echo "Error de conexión crítica: " . $e->getMessage();
            die();
        }

        return $this->conn;
    }
}
?>