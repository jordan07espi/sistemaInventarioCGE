<?php
// Archivo: model/MovimientoDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/dto/Movimiento.php';

class MovimientoDAO {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConnection();
    }

    /**
     * MÉTODO PRIVADO: Contiene la lógica de inserción y actualización de stock.
     * No maneja transacciones; asume que está siendo llamado dentro de una.
     * @param Movimiento $movimiento
     * @return void
     * @throws Exception Si algo falla, lanza una excepción para que el método que lo llama haga rollback.
     */
    private function _ejecutarAgregado(Movimiento $movimiento) {
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
    }


    /**
     * MÉTODO PÚBLICO: Inicia una transacción, llama a la lógica privada y luego confirma o revierte.
     */
    public function agregar(Movimiento $movimiento) {
        try {
            $this->conexion->beginTransaction();
            
            // Llama al método privado que hace el trabajo sucio
            $this->_ejecutarAgregado($movimiento);

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollBack();
            error_log("Error en MovimientoDAO::agregar: " . $e->getMessage());
            return false;
        }
    }

    public function listarRecientes($fechaInicio = null, $fechaFin = null, $pagina = 1, $registrosPorPagina = 25) {
        $offset = ($pagina - 1) * $registrosPorPagina;
        
        // --- Consulta base sin filtros ---
        $sql = "SELECT 
                    m.id_movimiento, m.tipo_movimiento, m.cantidad, m.jornada, m.fecha_movimiento,
                    m.es_correccion, m.descripcion AS descripcion_original, p.nombre_producto, p.unidad_medida,
                    e.nombre_espacio, u.nombre_completo AS nombre_usuario
                FROM movimientos m
                JOIN productos p ON m.id_producto = p.id_producto
                JOIN usuarios u ON m.id_usuario = u.id_usuario
                LEFT JOIN espacios e ON m.id_espacio = e.id_espacio";

        // --- Lógica mejorada para construir la cláusula WHERE dinámicamente ---
        $params = [];
        $whereConditions = []; // Un array para guardar las condiciones del filtro

        if ($fechaInicio) {
            // Agrega la condición para la fecha de inicio
            $whereConditions[] = "m.fecha_movimiento >= :fecha_inicio";
            $params[':fecha_inicio'] = $fechaInicio;
        }
        if ($fechaFin) {
            // Agrega la condición para la fecha de fin (incluyendo todo el día)
            $whereConditions[] = "m.fecha_movimiento <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        $whereClause = "";
        if (!empty($whereConditions)) {
            // Si hay condiciones, las une con "AND"
            $whereClause = " WHERE " . implode(' AND ', $whereConditions);
        }
        
        $sql .= $whereClause . " ORDER BY m.fecha_movimiento DESC LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->conexion->prepare($sql);
            
            // Asocia los parámetros de fecha que se hayan añadido
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            
            // Asocia los parámetros de paginación
            $stmt->bindValue(':limit', (int) $registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- Segunda consulta para obtener el total de registros con los mismos filtros ---
            $sqlTotal = "SELECT COUNT(*) FROM movimientos m" . $whereClause;
            $stmtTotal = $this->conexion->prepare($sqlTotal);
            
            // Ejecuta la consulta de conteo con los mismos parámetros de fecha
            $stmtTotal->execute($params);
            
            $totalRegistros = $stmtTotal->fetchColumn();

            return ['data' => $data, 'total' => $totalRegistros];

        } catch (PDOException $e) {
            error_log("Error en MovimientoDAO::listarRecientes: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }

    /**
     * MÉTODO CORREGIDO: Ahora usa el método privado sin conflictos de transacciones.
     */
    public function corregir($id_movimiento_original, $id_usuario_actual) {
        try {
            // 1. Inicia la transacción. Esta será la ÚNICA transacción para toda la operación.
            $this->conexion->beginTransaction();

            $stmt = $this->conexion->prepare("SELECT * FROM movimientos WHERE id_movimiento = :id");
            $stmt->bindValue(':id', $id_movimiento_original);
            $stmt->execute();
            $movimientoOriginal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$movimientoOriginal) {
                $this->conexion->rollBack();
                return ['success' => false, 'message' => 'El movimiento original no existe.'];
            }
            if ($movimientoOriginal['es_correccion']) {
                $this->conexion->rollBack();
                return ['success' => false, 'message' => 'No se puede corregir un movimiento que ya es una corrección.'];
            }

            $movimientoAjuste = new Movimiento();
            $movimientoAjuste->id_producto = $movimientoOriginal['id_producto'];
            $movimientoAjuste->id_usuario = $id_usuario_actual;
            $movimientoAjuste->cantidad = $movimientoOriginal['cantidad'];
            $movimientoAjuste->descripcion = "Corrección del movimiento #" . $id_movimiento_original;
            $movimientoAjuste->es_correccion = 1;
            $movimientoAjuste->tipo_movimiento = ($movimientoOriginal['tipo_movimiento'] == 'Salida') ? 'Entrada' : 'Salida';
            $movimientoAjuste->id_espacio = null;
            $movimientoAjuste->jornada = null;
            
            // 2. Llama al método privado que NO maneja transacciones.
            // Si _ejecutarAgregado falla, lanzará una excepción que será capturada por este bloque catch.
            $this->_ejecutarAgregado($movimientoAjuste);
            
            // 3. Si todo fue bien, confirma la transacción aquí.
            $this->conexion->commit();
            return ['success' => true];

        } catch (Exception $e) {
            // 4. Si algo falló (ya sea aquí o dentro de _ejecutarAgregado), revierte TODO.
            $this->conexion->rollBack();
            error_log("Error en MovimientoDAO::corregir: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error de base de datos al intentar corregir el movimiento.'];
        }
    }
}
?>