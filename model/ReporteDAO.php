<?php
// Archivo: model/ReporteDAO.php
require_once __DIR__ . '/../config/Conexion.php';

class ReporteDAO {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConnection();
    }

    /**
     * Obtiene el consumo total de cada producto en un rango de fechas.
     */
    public function getConsumoGeneral($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                    p.nombre_producto, 
                    p.unidad_medida, 
                    SUM(m.cantidad) as total_consumido
                FROM movimientos m
                JOIN productos p ON m.id_producto = p.id_producto
                WHERE 
                    m.tipo_movimiento = 'Salida' AND
                    m.fecha_movimiento BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY p.id_producto, p.nombre_producto, p.unidad_medida
                ORDER BY p.nombre_producto";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':fecha_inicio', $fechaInicio);
        $stmt->bindValue(':fecha_fin', $fechaFin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el consumo de productos desglosado por cada espacio en un rango de fechas.
     */
    public function getConsumoPorEspacio($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                    e.nombre_espacio, 
                    e.piso, 
                    p.nombre_producto, 
                    p.unidad_medida, 
                    SUM(m.cantidad) as total_consumido
                FROM movimientos m
                JOIN productos p ON m.id_producto = p.id_producto
                JOIN espacios e ON m.id_espacio = e.id_espacio
                WHERE 
                    m.tipo_movimiento = 'Salida' AND
                    m.id_espacio IS NOT NULL AND
                    m.fecha_movimiento BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY e.id_espacio, e.nombre_espacio, e.piso, p.id_producto, p.nombre_producto, p.unidad_medida
                ORDER BY e.nombre_espacio, p.nombre_producto";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':fecha_inicio', $fechaInicio);
        $stmt->bindValue(':fecha_fin', $fechaFin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>