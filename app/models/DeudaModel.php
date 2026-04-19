<?php
require_once 'Conexion.php';

class DeudaModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Extrae las deudas ordenadas para el Método Avalancha
    public function obtenerDeudasAvalancha($id_usuario) {
        // Sentencia SQL preparada para prevenir inyección SQL
        $sql = "SELECT id_deuda, nombre_deuda, saldo_total, tasa_intereses, cuota_mensual 
                FROM deudas 
                WHERE id_usuario = :id_usuario 
                ORDER BY tasa_intereses DESC";
        
        $stmt = $this->db->prepare($sql);
        
        // Vinculación segura del parámetro
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // Retorna un array asociativo con los resultados
        return $stmt->fetchAll();
    }
}
?>