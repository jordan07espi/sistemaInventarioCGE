<?php
// Archivo: model/MovimientoDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/dto/Movimiento.php';

class MovimientoDAO {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        // ----- ESTA LÍNEA ESTÁ CORREGIDA -----
        // El error anterior ($this.model/...) causaba que la página entera fallara.
        $this->conexion = $db->getConnection();
    }

    public function agregar(Movimiento $movimiento) {
        try {
            $this->conexion->beginTransaction();

            $sqlMovimiento = "INSERT INTO movimientos (id_producto, id_espacio, id_usuario, tipo_movimiento, cantidad, jornada, descripcion, es_correccion) 
                              VALUES (:id_producto, :id_espacio, :id_usuario, :tipo, :cantidad, :jornada, :descripcion, :es_correccion)";
            
            $stmtMovimiento = $this->conexion->prepare($sqlMovimiento);
            $stmtMovimiento->bindValue(':id_producto', $movimiento->id_producto);
            $stmtMovimiento->bindValue(':id_espacio', $movimiento->id_espacio);
            $stmtMovimiento->bindValue(':id_usuario', $movimiento->id_usuario);
            $stmtMovimiento->bindValue(':tipo', $movimiento->tipo_movimiento);
            $stmtMovimiento->bindValue(':cantidad', $movimiento->cantidad);
            $stmtMovimiento->bindValue(':jornada', $movimiento->jornada);
            $stmtMovimiento->bindValue(':descripcion', $movimiento->descripcion);
            $stmtMovimiento->bindValue(':es_correccion', $movimiento->es_correccion ?? 0, PDO::PARAM_INT);
            $stmtMovimiento->execute();

            if ($movimiento->tipo_movimiento == 'Salida') {
                $sqlStock = "UPDATE productos SET stock_actual = stock_actual - :cantidad WHERE id_producto = :id_producto";
            } else { // 'Entrada' o 'Ajuste'
                $sqlStock = "UPDATE productos SET stock_actual = stock_actual + :cantidad WHERE id_producto = :id_producto";
            }

            $stmtStock = $this->conexion->prepare($sqlStock);
            $stmtStock->bindValue(':cantidad', $movimiento->cantidad);
            $stmtStock->bindValue(':id_producto', $movimiento->id_producto);
            $stmtStock->execute();

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            error_log("Error en MovimientoDAO::agregar: " . $e->getMessage());
            return false;
        }
    }

    public function listarRecientes($fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT 
                    m.id_movimiento, m.tipo_movimiento, m.cantidad, m.jornada, m.fecha_movimiento,
                    m.es_correccion, m.descripcion AS descripcion_original, p.nombre_producto, p.unidad_medida,
                    e.nombre_espacio, u.nombre_completo AS nombre_usuario
                FROM movimientos m
                JOIN productos p ON m.id_producto = p.id_producto
                JOIN usuarios u ON m.id_usuario = u.id_usuario
                LEFT JOIN espacios e ON m.id_espacio = e.id_espacio";

        $params = [];
        if ($fechaInicio && $fechaFin) {
            $sql .= " WHERE m.fecha_movimiento BETWEEN :fecha_inicio AND DATE_ADD(:fecha_fin, INTERVAL 1 DAY)";
            $params[':fecha_inicio'] = $fechaInicio;
            $params[':fecha_fin'] = $fechaFin;
        }
        
        $sql .= " ORDER BY m.fecha_movimiento DESC";

        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en MovimientoDAO::listarRecientes: " . $e->getMessage());
            return [];
        }
    }

    public function corregir($id_movimiento_original, $id_usuario_actual) {
        try {
            $this->conexion->beginTransaction();

            $stmt = $this->conexion->prepare("SELECT * FROM movimientos WHERE id_movimiento = :id");
            $stmt->bindValue(':id', $id_movimiento_original);
            $stmt->execute();
            $movimientoOriginal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$movimientoOriginal) {
                return ['success' => false, 'message' => 'El movimiento original no existe.'];
            }
            if ($movimientoOriginal['es_correccion']) {
                return ['success' => false, 'message' => 'No se puede corregir un movimiento que ya es una corrección.'];
            }

            $movimientoAjuste = new Movimiento();
            $movimientoAjuste->id_producto = $movimientoOriginal['id_producto'];
            $movimientoAjuste->id_usuario = $id_usuario_actual;
            $movimientoAjuste->cantidad = $movimientoOriginal['cantidad'];
            $movimientoAjuste->descripcion = "Corrección del movimiento #" . $id_movimiento_original;
            $movimientoAjuste->es_correccion = 1;
            $movimientoAjuste->tipo_movimiento = ($movimientoOriginal['tipo_movimiento'] == 'Salida') ? 'Entrada' : 'Salida';

            $this->agregar($movimientoAjuste);
            
            $this->conexion->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->conexion->rollBack();
            error_log("Error en MovimientoDAO::corregir: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al intentar corregir el movimiento.'];
        }
    }
}
?>