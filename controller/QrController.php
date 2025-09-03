<?php
// Archivo: controller/QrController.php
session_start();
require_once '../model/MovimientoDAO.php';
require_once '../model/dto/Movimiento.php';

// --- Asegurarse de que el usuario ha iniciado sesión ---
if (!isset($_SESSION['id_usuario'])) {
    die("Acceso denegado. Por favor, inicie sesión desde la aplicación móvil o web para escanear.");
}

$action = $_GET['action'] ?? '';

if ($action === 'registrarSalida') {
    $id_producto = $_GET['id_producto'] ?? null;
    $id_espacio = $_GET['id_espacio'] ?? null;
    $id_usuario = $_SESSION['id_usuario'];

    if (!$id_producto || !$id_espacio) {
        http_response_code(400); // Bad Request
        echo "Error: Faltan datos del producto o del espacio en el QR.";
        exit();
    }

    // --- Lógica para determinar la jornada ---
    date_default_timezone_set('America/Guayaquil');
    $hora_actual = (int)date('G'); // Formato 24h sin ceros iniciales
    $jornada = '';

    if ($hora_actual >= 0 && $hora_actual < 13) { // 00:00 a 12:59
        $jornada = 'Matutino';
    } elseif ($hora_actual >= 13 && $hora_actual < 18) { // 13:00 a 17:59
        $jornada = 'Vespertino';
    } else { // 18:00 a 23:59
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

    // --- Respuesta visual para el usuario ---
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="es"><head>';
    echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<script src="https://cdn.tailwindcss.com"></script>';
    echo '<title>Resultado de Escaneo</title></head>';
    echo '<body class="bg-gray-100 flex items-center justify-center h-screen">';
    
    if ($resultado['success']) {
        echo '<div class="text-center p-8 bg-white rounded-lg shadow-lg">
                <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h1 class="text-2xl font-bold text-gray-800 mt-4">¡Éxito!</h1>
                <p class="text-gray-600 mt-2">Salida registrada correctamente.</p>
              </div>';
    } else {
        echo '<div class="text-center p-8 bg-white rounded-lg shadow-lg">
                <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Error</h1>
                <p class="text-gray-600 mt-2">' . htmlspecialchars($resultado['message']) . '</p>
              </div>';
    }

    echo '</body></html>';
} else {
    http_response_code(404);
    echo "Acción no reconocida.";
}
?>