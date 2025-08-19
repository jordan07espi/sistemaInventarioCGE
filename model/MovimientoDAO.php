<?php
// Archivo: model/MovimientoDAO.php
require_once __DIR__ . '/../config/Conexion.php';

class MovimientoDAO {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConnection();
    }

    public function agregar(Movimiento $movimiento) {
        // Usaremos una transacción para asegurar que ambas operaciones (insertar movimiento y actualizar stock) se completen con éxito.
        try {
            $this->conexion->beginTransaction();

            // 1. Insertar el movimiento
            $sqlMovimiento = "INSERT INTO movimientos (id_producto, id_espacio, id_usuario, tipo_movimiento, cantidad, jornada, descripcion) 
                              VALUES (:id_producto, :id_espacio, :id_usuario, :tipo, :cantidad, :jornada, :descripcion)";
            
            $stmtMovimiento = $this->conexion->prepare($sqlMovimiento);
            $stmtMovimiento->bindValue(':id_producto', $movimiento->id_producto);
            $stmtMovimiento->bindValue(':id_espacio', $movimiento->id_espacio);
            $stmtMovimiento->bindValue(':id_usuario', $movimiento->id_usuario);
            $stmtMovimiento->bindValue(':tipo', $movimiento->tipo_movimiento);
            $stmtMovimiento->bindValue(':cantidad', $movimiento->cantidad);
            $stmtMovimiento->bindValue(':jornada', $movimiento->jornada);
            $stmtMovimiento->bindValue(':descripcion', $movimiento->descripcion);
            $stmtMovimiento->execute();

            // 2. Actualizar el stock en la tabla de productos
            if ($movimiento->tipo_movimiento == 'Salida') {
                $sqlStock = "UPDATE productos SET stock_actual = stock_actual - :cantidad WHERE id_producto = :id_producto";
            } else { // 'Entrada' o 'Ajuste'
                $sqlStock = "UPDATE productos SET stock_actual = stock_actual + :cantidad WHERE id_producto = :id_producto";
            }

            $stmtStock = $this->conexion->prepare($sqlStock);
            $stmtStock->bindValue(':cantidad', $movimiento->cantidad);
            $stmtStock->bindValue(':id_producto', $movimiento->id_producto);
            $stmtStock->execute();

            // 3. Si todo fue bien, confirmamos la transacción
            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            // Si algo falla, revertimos todos los cambios
            $this->conexion->rollBack();
            error_log("Error en MovimientoDAO::agregar: " . $e->getMessage());
            return false;
        }
    }

    public function listarRecientes($fechaInicio = null, $fechaFin = null) {
        // Consulta base que une todas las tablas para obtener la información completa
        $sql = "SELECT 
                    m.tipo_movimiento,
                    m.cantidad,
                    m.jornada,
                    m.fecha_movimiento,
                    p.nombre_producto,
                    p.unidad_medida,
                    e.nombre_espacio,
                    u.nombre_completo AS nombre_usuario
                FROM 
                    movimientos m
                JOIN 
                    productos p ON m.id_producto = p.id_producto
                JOIN 
                    usuarios u ON m.id_usuario = u.id_usuario
                LEFT JOIN 
                    espacios e ON m.id_espacio = e.id_espacio";

        // Lógica para el filtro de fechas
        if ($fechaInicio && $fechaFin) {
            // Añadimos +1 día a la fecha fin para incluir todo el día
            $sql .= " WHERE m.fecha_movimiento BETWEEN :fecha_inicio AND DATE_ADD(:fecha_fin, INTERVAL 1 DAY)";
        } else {
            // Por defecto, mostramos los últimos 4 días
            $sql .= " WHERE m.fecha_movimiento >= NOW() - INTERVAL 4 DAY";
        }
        
        $sql .= " ORDER BY m.fecha_movimiento DESC"; // Ordenamos por los más recientes primero

        try {
            $stmt = $this->conexion->prepare($sql);
            if ($fechaInicio && $fechaFin) {
                $stmt->bindValue(':fecha_inicio', $fechaInicio);
                $stmt->bindValue(':fecha_fin', $fechaFin);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en MovimientoDAO::listarRecientes: " . $e->getMessage());
            return []; // Devolvemos un array vacío en caso de error
        }
    }
}
?>