<?php
require_once 'Conexion.php';

class ReglaModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    // Registra una regla de categorización inteligente
    public function registrarRegla($id_usuario, $patron, $id_categoria, $nombre_fantasia) {
        $sql = "INSERT INTO reglas_categorizacion (id_usuario, patron, id_categoria, nombre_fantasia)
                VALUES (:id_usuario, :patron, :id_categoria, :nombre_fantasia)
                ON DUPLICATE KEY UPDATE 
                    id_categoria = :id_categoria2,
                    nombre_fantasia = :nombre_fantasia2";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':patron', $patron, PDO::PARAM_STR);
        $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria2', $id_categoria, PDO::PARAM_INT);
        $stmt->bindParam(':nombre_fantasia', $nombre_fantasia, PDO::PARAM_STR);
        $stmt->bindParam(':nombre_fantasia2', $nombre_fantasia, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Obtiene todas las reglas de un usuario con los nombres de categorías asociados
    public function obtenerReglasUsuario($id_usuario) {
        $sql = "SELECT r.*, c.nombre_categoria 
                FROM reglas_categorizacion r
                INNER JOIN categorias c ON r.id_categoria = c.id_categoria
                WHERE r.id_usuario = :id_usuario
                ORDER BY r.id_regla DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Elimina una regla
    public function eliminarRegla($id_regla, $id_usuario) {
        $sql = "DELETE FROM reglas_categorizacion WHERE id_regla = :id_regla AND id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_regla', $id_regla, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Heurística de Clasificación: Evalúa si una descripción coincide con alguna regla activa
    public function evaluarDescripcion($id_usuario, $descripcion) {
        $reglas = $this->obtenerReglasUsuario($id_usuario);
        $desc_upper = mb_strtoupper($descripcion, 'UTF-8');

        foreach ($reglas as $regla) {
            $patron_upper = mb_strtoupper($regla['patron'], 'UTF-8');
            
            // Busca coincidencia de texto exacta o parcial (case-insensitive)
            if (strpos($desc_upper, $patron_upper) !== false) {
                return [
                    'id_categoria'    => (int)$regla['id_categoria'],
                    'nombre_fantasia' => $regla['nombre_fantasia'] ?: $regla['patron']
                ];
            }
        }

        return null;
    }
}
?>
