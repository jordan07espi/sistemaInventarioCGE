<?php
// Archivo: controller/MovimientoController.php
session_start();
require_once '../model/MovimientoDAO.php';
require_once '../model/dto/Movimiento.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no reconocida.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;

if (!$id_usuario_actual) {
    $response['message'] = 'Error: Sesión no válida.';
    echo json_encode($response);
    exit();
}

$movimientoDAO = new MovimientoDAO();

switch ($action) {
    case 'registrar':
        if (empty($_POST['id_producto']) || empty($_POST['cantidad'])) {
            $response['message'] = 'Producto y cantidad son obligatorios.';
            break;
        }

        $movimiento = new Movimiento();
        $movimiento->id_producto = $_POST['id_producto'];
        $movimiento->id_usuario = $id_usuario_actual;
        $movimiento->tipo_movimiento = $_POST['tipo_movimiento'];
        $movimiento->cantidad = $_POST['cantidad'];
        $movimiento->descripcion = $_POST['descripcion'] ?? '';
        $movimiento->id_espacio = ($_POST['tipo_movimiento'] == 'Salida') ? ($_POST['id_espacio'] ?: null) : null;
        $movimiento->jornada = ($_POST['tipo_movimiento'] == 'Salida') ? ($_POST['jornada'] ?: null) : null;

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
        $pagina = filter_input(INPUT_POST, 'pagina', FILTER_VALIDATE_INT) ?: 1; 
        $registrosPorPagina = 20; // Definido aquí

        $resultado = $movimientoDAO->listarRecientes($fechaInicio, $fechaFin, $pagina, $registrosPorPagina);
        
        $response['success'] = true;
        $response['data'] = $resultado['data'];
        $response['pagination'] = [
            'total_registros' => (int) $resultado['total'],
            'pagina_actual' => $pagina,
            'registros_por_pagina' => $registrosPorPagina,
            'total_paginas' => ceil($resultado['total'] / $registrosPorPagina)
        ];
        break;

    // --- NUEVO: Caso para corregir un movimiento ---
    case 'corregir':
        $id_movimiento_original = $_POST['id_movimiento'] ?? 0;
        if (!$id_movimiento_original) {
            $response['message'] = 'ID de movimiento no proporcionado.';
            break;
        }
        
        $resultado = $movimientoDAO->corregir($id_movimiento_original, $id_usuario_actual);
        
        if ($resultado['success']) {
            $response['success'] = true;
            $response['message'] = 'Movimiento corregido exitosamente. Se ha creado un movimiento de ajuste.';
        } else {
            $response['message'] = $resultado['message'];
        }
        break;
}

echo json_encode($response);
?>