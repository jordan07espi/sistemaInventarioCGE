<?php
// Archivo: model/DashboardDAO.php
require_once __DIR__ . '/../config/Conexion.php';

class DashboardDAO {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConnection();
    }

    /**
     * Obtiene las tarjetas de resumen del dashboard.
     */
    public function getDatosResumen() {
        try {
            $resumen = [];

            // 1. Total de productos activos
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM productos WHERE activo = 1");
            $stmt->execute();
            $resumen['total_productos'] = $stmt->fetchColumn();

            // 2. Total de espacios activos
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM espacios WHERE activo = 1");
            $stmt->execute();
            $resumen['total_espacios'] = $stmt->fetchColumn();

            // 3. Alertas de stock bajo
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM productos WHERE stock_actual <= stock_minimo AND activo = 1");
            $stmt->execute();
            $resumen['alertas_stock'] = $stmt->fetchColumn();

            // 4. Movimientos realizados hoy
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM movimientos WHERE DATE(fecha_movimiento) = CURDATE()");
            $stmt->execute();
            $resumen['movimientos_hoy'] = $stmt->fetchColumn();

            return $resumen;
        } catch (PDOException $e) {
            error_log("Error en DashboardDAO::getDatosResumen: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene la lista de productos con bajo stock para las alertas.
     */
    public function getProductosConBajoStock() {
        $sql = "SELECT nombre_producto, stock_actual, stock_minimo 
                FROM productos 
                WHERE stock_actual <= stock_minimo AND activo = 1 
                ORDER BY nombre_producto";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene el inventario actual para la tabla del dashboard.
     */
    public function getInventarioActual() {
        $sql = "SELECT nombre_producto, stock_actual, unidad_medida, stock_minimo 
                FROM productos 
                WHERE activo = 1 
                ORDER BY stock_actual ASC, nombre_producto ASC
                LIMIT 10"; // Limitamos a 10 para una vista rápida
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los últimos 5 movimientos realizados hoy.
     */
    public function getMovimientosHoy() {
        $sql = "SELECT 
                    m.tipo_movimiento, m.cantidad,
                    p.nombre_producto, p.unidad_medida,
                    u.nombre_completo AS nombre_usuario
                FROM movimientos m
                JOIN productos p ON m.id_producto = p.id_producto
                JOIN usuarios u ON m.id_usuario = u.id_usuario
                WHERE DATE(m.fecha_movimiento) = CURDATE()
                ORDER BY m.fecha_movimiento DESC
                LIMIT 5";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>