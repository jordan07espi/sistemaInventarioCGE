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
}
?>