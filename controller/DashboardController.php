<?php
// Archivo: controller/DashboardController.php
session_start();
require_once '../model/DashboardDAO.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no reconocida.'];
$action = $_GET['action'] ?? '';

if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Error: Sesión no válida.';
    echo json_encode($response);
    exit();
}

$dashboardDAO = new DashboardDAO();

if ($action === 'cargarDatos') {
    try {
        $datosResumen = $dashboardDAO->getDatosResumen();
        $productosBajoStock = $dashboardDAO->getProductosConBajoStock();
        $inventarioActual = $dashboardDAO->getInventarioActual();
        $movimientosHoy = $dashboardDAO->getMovimientosHoy(); // <-- AÑADIR ESTA LÍNEA

        if ($datosResumen !== null) {
            $response['success'] = true;
            $response['data'] = [
                'resumen' => $datosResumen,
                'productosBajoStock' => $productosBajoStock,
                'inventarioActual' => $inventarioActual,
                'movimientosHoy' => $movimientosHoy // <-- AÑADIR ESTA LÍNEA
            ];
        } else {
            $response['message'] = 'No se pudieron cargar los datos del dashboard.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error del servidor al cargar los datos.';
        error_log($e->getMessage());
    }
}

echo json_encode($response);
?>