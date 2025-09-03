<?php
// Archivo: controller/QrController.php
session_start();
require_once '../model/MovimientoDAO.php';
require_once '../model/dto/Movimiento.php';

// Cambiamos el tipo de contenido a JSON para la respuesta
header('Content-Type: application/json');

// --- Asegurarse de que el usuario ha iniciado sesión ---
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Acceso denegado. Por favor, inicie sesión.']);
    exit();
}

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Acción no reconocida.'];

if ($action === 'registrarSalida') {
    $id_producto = $_GET['id_producto'] ?? null;
    $id_espacio = $_GET['id_espacio'] ?? null;
    $id_usuario = $_SESSION['id_usuario'];

    if (!$id_producto || !$id_espacio) {
        http_response_code(400); // Bad Request
        $response['message'] = 'Error: Faltan datos del producto o del espacio en el QR.';
        echo json_encode($response);
        exit();
    }

    // --- Lógica para determinar la jornada ---
    date_default_timezone_set('America/Guayaquil');
    $hora_actual = (int)date('G');
    $jornada = '';

    if ($hora_actual >= 0 && $hora_actual < 13) {
        $jornada = 'Matutino';
    } elseif ($hora_actual >= 13 && $hora_actual < 18) {
        $jornada = 'Vespertino';
    } else {
        $jornada = 'Nocturno';
    }

    // --- Crear y registrar el movimiento ---
    $movimiento = new Movimiento();
    $movimiento->id_producto = $id_producto;
    $movimiento->id_espacio = $id_espacio;
    $movimiento->id_usuario = $id_usuario;
    $movimiento->tipo_movimiento = 'Salida';
    $movimiento->cantidad = 1; // La cantidad es siempre 1 al escanear
    $movimiento->jornada = $jornada;
    $movimiento->descripcion = 'Salida registrada por escáner QR.';
    
    $movimientoDAO = new MovimientoDAO();
    $resultado = $movimientoDAO->agregar($movimiento);
    
    if ($resultado) {
        $response['success'] = true;
        $response['message'] = 'Movimiento registrado exitosamente.';
    } else {
        http_response_code(500);
        $response['message'] = 'Error al registrar el movimiento en la base de datos.';
    }
    
    echo json_encode($response);
    exit();
}

// Si la acción no es 'registrarSalida', devolvemos el error inicial
echo json_encode($response);
?>