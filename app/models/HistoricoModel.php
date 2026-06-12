<?php
require_once 'Conexion.php';

class HistoricoModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Guarda o actualiza la instantánea de balance general para un período mensual
    public function guardarInstantaneaMensual($id_usuario, $periodo, $ingresos, $gastos, $deudas, $ahorros, $neto) {
        $fecha = new DateTime($periodo);
        $periodo_formateado = $fecha->format('Y-m-01');

        $sql = "INSERT INTO historico_saldos (id_usuario, periodo, total_ingresos, total_gastos, total_deudas, total_ahorros, patrimonio_neto) 
                VALUES (:id_usuario, :periodo, :total_ingresos, :total_gastos, :total_deudas, :total_ahorros, :patrimonio_neto)
                ON DUPLICATE KEY UPDATE 
                    total_ingresos = :total_ingresos2,
                    total_gastos = :total_gastos2,
                    total_deudas = :total_deudas2,
                    total_ahorros = :total_ahorros2,
                    patrimonio_neto = :patrimonio_neto2";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':periodo', $periodo_formateado, PDO::PARAM_STR);
        $stmt->bindParam(':total_ingresos', $ingresos);
        $stmt->bindParam(':total_gastos', $gastos);
        $stmt->bindParam(':total_deudas', $deudas);
        $stmt->bindParam(':total_ahorros', $ahorros);
        $stmt->bindParam(':patrimonio_neto', $neto);
        
        $stmt->bindParam(':total_ingresos2', $ingresos);
        $stmt->bindParam(':total_gastos2', $gastos);
        $stmt->bindParam(':total_deudas2', $deudas);
        $stmt->bindParam(':total_ahorros2', $ahorros);
        $stmt->bindParam(':patrimonio_neto2', $neto);

        return $stmt->execute();
    }

    // Comprueba si ya existe un registro de balance para el período
    public function existeInstantanea($id_usuario, $periodo) {
        $fecha = new DateTime($periodo);
        $periodo_formateado = $fecha->format('Y-m-01');

        $sql = "SELECT id_historico FROM historico_saldos WHERE id_usuario = :id_usuario AND periodo = :periodo LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':periodo', $periodo_formateado, PDO::PARAM_STR);
        $stmt->execute();
        return (bool)$stmt->fetch();
    }

    // Obtiene la trayectoria de balances mensuales de un usuario para graficar
    public function obtenerTrayectoriaPatrimonial($id_usuario) {
        $sql = "SELECT periodo, total_ingresos, total_gastos, total_deudas, total_ahorros, patrimonio_neto 
                FROM historico_saldos 
                WHERE id_usuario = :id_usuario 
                ORDER BY periodo ASC 
                LIMIT 12"; // Limitamos a los últimos 12 meses

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
