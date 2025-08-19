<?php
// Archivo: controller/MovimientoController.php
session_start();
require_once '../model/MovimientoDAO.php';
require_once '../model/dto/Movimiento.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no reconocida.'];
// Leemos la acción tanto de POST (para filtros) como de GET (para la carga inicial)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$movimientoDAO = new MovimientoDAO();

switch ($action) {
    case 'registrar':
        if (empty($_POST['id_producto']) || empty($_POST['cantidad'])) {
            $response['message'] = 'Producto y cantidad son obligatorios.';
            break;
        }

        $movimiento = new Movimiento();
        $movimiento->id_producto = $_POST['id_producto'];
        $movimiento->id_usuario = $_SESSION['id_usuario'];
        $movimiento->tipo_movimiento = $_POST['tipo_movimiento'];
        $movimiento->cantidad = $_POST['cantidad'];
        $movimiento->descripcion = $_POST['descripcion'] ?? '';
        $movimiento->id_espacio = ($_POST['tipo_movimiento'] == 'Salida') ? $_POST['id_espacio'] : null;
        $movimiento->jornada = ($_POST['tipo_movimiento'] == 'Salida') ? $_POST['jornada'] : null;

        if ($movimientoDAO->agregar($movimiento)) {
            $response['success'] = true;
            $response['message'] = 'Movimiento registrado exitosamente.';
        } else {
            $response['message'] = 'Error al registrar el movimiento. Verifique el stock o contacte al administrador.';
        }
        break;

    case 'listarRecientes':
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;
        
        $response['success'] = true;
        $response['data'] = $movimientoDAO->listarRecientes($fechaInicio, $fechaFin);
        break;
}

echo json_encode($response);
?>