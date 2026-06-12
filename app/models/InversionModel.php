<?php
require_once 'Conexion.php';

class InversionModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Registra una nueva inversión activa
    public function registrarInversion($id_usuario, $plataforma, $monto_invertido, $tasa_retorno_mensual, $fecha_inicio) {
        $sql = "INSERT INTO inversiones (id_usuario, plataforma, monto_invertido, tasa_retorno_mensual, fecha_inicio)
                VALUES (:id_usuario, :plataforma, :monto_invertido, :tasa_retorno_mensual, :fecha_inicio)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':plataforma', $plataforma, PDO::PARAM_STR);
        $stmt->bindParam(':monto_invertido', $monto_invertido, PDO::PARAM_STR);
        $stmt->bindParam(':tasa_retorno_mensual', $tasa_retorno_mensual, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Obtiene todas las inversiones activas de un usuario
    public function obtenerInversionesUsuario($id_usuario) {
        $sql = "SELECT * FROM inversiones WHERE id_usuario = :id_usuario ORDER BY fecha_inicio DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina una inversión del catálogo del usuario
    public function eliminarInversion($id_inversion, $id_usuario) {
        $sql = "DELETE FROM inversiones WHERE id_inversion = :id_inversion AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_inversion', $id_inversion, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
