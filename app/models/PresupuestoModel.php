<?php
require_once 'Conexion.php';

class PresupuestoModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Establece o actualiza un presupuesto mensual para una categoría
    public function establecerPresupuesto($id_usuario, $id_categoria, $monto_limite, $periodo) {
        // Aseguramos que el periodo sea el primer día del mes
        $fecha = new DateTime($periodo);
        $periodo_formateado = $fecha->format('Y-m-01');

        $sql = "INSERT INTO presupuestos (id_usuario, id_categoria, monto_limite, periodo) 
                VALUES (:id_usuario, :id_categoria, :monto_limite, :periodo)
                ON DUPLICATE KEY UPDATE monto_limite = :monto_limite2";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':monto_limite', $monto_limite);
        $stmt->bindParam(':monto_limite2', $monto_limite);
        $stmt->bindParam(':periodo', $periodo_formateado, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    // Obtiene el listado de presupuestos y consumos reales por categoría para un período mensual
    public function obtenerPresupuestosYConsumos($id_usuario, $periodo) {
        $fecha = new DateTime($periodo);
        $periodo_inicio = $fecha->format('Y-m-01');
        $periodo_fin = $fecha->format('Y-m-t'); // 't' devuelve el último día del mes

        $sql = "SELECT c.id_categoria, c.nombre_categoria, 
                       COALESCE(p.monto_limite, 0) as monto_limite, 
                       COALESCE(SUM(CASE WHEN t.id_usuario = :id_usuario2 THEN t.monto ELSE 0 END), 0) as monto_gastado
                FROM categorias c
                LEFT JOIN presupuestos p ON c.id_categoria = p.id_categoria AND p.periodo = :periodo_inicio
                LEFT JOIN transacciones t ON c.id_categoria = t.id_categoria 
                       AND t.fecha_transaccion >= :periodo_inicio2 
                       AND t.fecha_transaccion <= :periodo_fin
                WHERE c.id_usuario = :id_usuario AND c.tipo_flujo = 'gasto'
                GROUP BY c.id_categoria, c.nombre_categoria, p.monto_limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario2', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':periodo_inicio', $periodo_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':periodo_inicio2', $periodo_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':periodo_fin', $periodo_fin, PDO::PARAM_STR);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
