<?php
// Archivo: controller/ReporteController.php
session_start();
require_once '../model/ReporteDAO.php';

// --- Lógica para Exportación (PDF y Excel) ---
// (Esta parte se explica en el Paso 6)


// --- Lógica para generar el reporte vía AJAX ---
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no reconocida.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Error: Sesión no válida.';
    echo json_encode($response);
    exit();
}

$reporteDAO = new ReporteDAO();

switch ($action) {
    case 'generarReporte':
        $rango = $_POST['rango'] ?? 'hoy';
        
        // Calcular fechas según el rango seleccionado
        date_default_timezone_set('America/Guayaquil'); // Asegura la zona horaria correcta
        switch ($rango) {
            case 'semana':
                $fechaInicio = date('Y-m-d 00:00:00', strtotime('monday this week'));
                $fechaFin = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                break;
            case 'mes':
                $fechaInicio = date('Y-m-01 00:00:00');
                $fechaFin = date('Y-m-t 23:59:59');
                break;
            case 'hoy':
            default:
                $fechaInicio = date('Y-m-d 00:00:00');
                $fechaFin = date('Y-m-d 23:59:59');
                break;
        }

        try {
            $consumoGeneral = $reporteDAO->getConsumoGeneral($fechaInicio, $fechaFin);
            $consumoDetallado = $reporteDAO->getConsumoPorEspacio($fechaInicio, $fechaFin);
            
            $response['success'] = true;
            $response['data'] = [
                'general' => $consumoGeneral,
                'detallado' => $consumoDetallado,
                'periodo' => date('d/m/Y', strtotime($fechaInicio)) . ' - ' . date('d/m/Y', strtotime($fechaFin))
            ];
        } catch (Exception $e) {
            $response['message'] = 'Error al generar el reporte: ' . $e->getMessage();
        }
        break;
}

echo json_encode($response);

?>