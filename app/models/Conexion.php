<?php

class Conexion {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;

    public function __construct() {
        // Railway provee variables sin underscore (MYSQLHOST, MYSQLUSER, etc.)
        // Fallback a valores locales de Laragon para desarrollo
        $this->host     = getenv('MYSQLHOST')     ?: (getenv('MYSQL_HOST')     ?: '127.0.0.1');
        $this->db_name  = getenv('MYSQLDATABASE') ?: (getenv('MYSQL_DATABASE') ?: 'gestor_financiero');
        $this->username = getenv('MYSQLUSER')     ?: (getenv('MYSQL_USER')     ?: 'root');
        $this->password = getenv('MYSQLPASSWORD') ?: (getenv('MYSQL_PASSWORD') ?: '');
        $this->port     = getenv('MYSQLPORT')     ?: (getenv('MYSQL_PORT')     ?: '3306');
    }

    public function conectar() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
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