<?php
require_once 'Conexion.php';

class GastoRecurrenteModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Busca gastos fijos cuyo día de cobro sea menor o igual al día de hoy,
    // y que NO hayan sido procesados durante el mes y año actual.
    public function obtenerGastosPendientes($id_usuario) {
        $sql = "SELECT * FROM gastos_recurrentes 
                WHERE id_usuario = :id_usuario 
                AND dia_cobro <= DAY(CURRENT_DATE) 
                AND (ultimo_procesamiento IS NULL OR DATE_FORMAT(ultimo_procesamiento, '%Y-%m') < DATE_FORMAT(CURRENT_DATE, '%Y-%m'))";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualiza la fecha de procesamiento para evitar cobros duplicados en el mismo mes
    public function marcarComoProcesado($id_recurrente) {
        $sql = "UPDATE gastos_recurrentes SET ultimo_procesamiento = CURRENT_DATE WHERE id_recurrente = :id_recurrente";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_recurrente', $id_recurrente, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Registra una nueva plantilla de gasto fijo mensual
    public function registrarGastoRecurrente($id_usuario, $id_categoria, $monto, $descripcion, $dia_cobro) {
        $sql = "INSERT INTO gastos_recurrentes (id_usuario, id_categoria, monto, descripcion, dia_cobro) 
                VALUES (:id_usuario, :id_categoria, :monto, :descripcion, :dia_cobro)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':dia_cobro', $dia_cobro, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Obtiene todas las plantillas activas del usuario, uniendo con la tabla categorías
    public function obtenerPlantillasUsuario($id_usuario) {
        $sql = "SELECT gr.*, c.nombre_categoria 
                FROM gastos_recurrentes gr 
                INNER JOIN categorias c ON gr.id_categoria = c.id_categoria 
                WHERE gr.id_usuario = :id_usuario 
                ORDER BY gr.dia_cobro ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>