<?php
// Archivo: model/ProductoDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/dto/Producto.php';

class ProductoDAO {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConnection();
    }

    public function listar() {
        $sql = "SELECT * FROM productos WHERE activo = 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(Producto $producto) {
        $sql = "INSERT INTO productos (nombre_producto, unidad_medida, stock_actual, stock_minimo) VALUES (:nombre, :unidad, :stock, :stock_minimo)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre', $producto->nombre_producto);
        $stmt->bindValue(':unidad', $producto->unidad_medida);
        $stmt->bindValue(':stock', $producto->stock_actual);
        $stmt->bindValue(':stock_minimo', $producto->stock_minimo);
        return $stmt->execute();
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM productos WHERE id_producto = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar(Producto $producto) {
        $sql = "UPDATE productos SET nombre_producto = :nombre, unidad_medida = :unidad, stock_minimo = :stock_minimo WHERE id_producto = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre', $producto->nombre_producto);
        $stmt->bindValue(':unidad', $producto->unidad_medida);
        $stmt->bindValue(':stock_minimo', $producto->stock_minimo);
        $stmt->bindValue(':id', $producto->id_producto);
        return $stmt->execute();
    }

    public function eliminar($id) {
        // Usamos borrado lógico para no perder el historial de movimientos
        $sql = "UPDATE productos SET activo = 0 WHERE id_producto = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
?>