<?php
// Archivo: controller/ReporteController.php
session_start();
require_once '../model/ReporteDAO.php';
require_once '../lib/fpdf/fpdf.php';

// --- LÓGICA COMPLETA Y MEJORADA PARA EXPORTACIÓN ---
$exportAction = $_GET['action'] ?? '';
if ($exportAction === 'exportarExcel' || $exportAction === 'exportarPdf') {
    
    if (!isset($_SESSION['id_usuario'])) {
        die('Acceso denegado. Por favor, inicie sesión.');
    }

    // Obtenemos el nombre del usuario de la sesión para el reporte
    $nombreUsuarioReporte = $_SESSION['nombre_completo'] ?? 'Usuario Desconocido';
    $rango = $_GET['rango'] ?? 'hoy';
    date_default_timezone_set('America/Guayaquil');
    
    switch ($rango) {
        case 'semana':
            $fechaInicio = date('Y-m-d 00:00:00', strtotime('monday this week'));
            $fechaFin = date('Y-m-d 23:59:59', strtotime('sunday this week'));
            break;
        case 'mes':
            $fechaInicio = date('Y-m-01 00:00:00');
            $fechaFin = date('Y-m-t 23:59:59');
            break;
        default: // 'hoy'
            $fechaInicio = date('Y-m-d 00:00:00');
            $fechaFin = date('Y-m-d 23:59:59');
            break;
    }

    $reporteDAO = new ReporteDAO();
    $consumoGeneral = $reporteDAO->getConsumoGeneral($fechaInicio, $fechaFin);
    $consumoDetallado = $reporteDAO->getConsumoPorEspacio($fechaInicio, $fechaFin);
    $periodoStr = date('d/m/Y', strtotime($fechaInicio)) . ' al ' . date('d/m/Y', strtotime($fechaFin));
    $periodoFile = date('Ymd', strtotime($fechaInicio)) . '_' . date('Ymd', strtotime($fechaFin));

    // --- LÓGICA PARA EXCEL (CSV) ---
    if ($exportAction === 'exportarExcel') {
        // (La lógica de Excel no cambia)
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Reporte_Consumo_' . $periodoFile . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['CGE - Reporte de Consumo - ' . $periodoStr]);
        fputcsv($output, []); 
        fputcsv($output, ['Consumo General por Implemento']);
        fputcsv($output, ['Producto', 'Unidad', 'Total Consumido']);
        foreach ($consumoGeneral as $fila) { fputcsv($output, $fila); }
        fputcsv($output, []);
        fputcsv($output, ['Consumo Detallado por Espacio y Jornada']);
        fputcsv($output, ['Espacio', 'Piso', 'Producto', 'Unidad', 'Jornada', 'Total Consumido']);
        foreach ($consumoDetallado as $fila) { fputcsv($output, $fila); }
        fclose($output);
        exit();
    }
    
    // --- LÓGICA PARA PDF (FPDF) ---
    if ($exportAction === 'exportarPdf') {

        class PDF extends FPDF {
            private $periodo;
            private $nombreUsuario; // Propiedad para guardar el nombre del usuario

            function setPeriodo($periodo) {
                $this->periodo = $periodo;
            }
            
            // Nueva función para pasar el nombre del usuario a la clase
            function setNombreUsuario($nombre) {
                $this->nombreUsuario = $nombre;
            }

            function Header() {
                // 1. Logo de la empresa
                $this->Image('../view/assets/img/logo.png', 10, 8, 25);
                
                // 2. Título principal
                $this->SetFont('Arial','B',14);
                $this->Cell(0, 7, iconv('UTF-8', 'windows-1252', 'CGE - Reporte de Consumo'), 0, 1, 'C');
                
                // 3. Período del reporte
                $this->SetFont('Arial','',10);
                $this->Cell(0, 7, iconv('UTF-8', 'windows-1252', 'Período: ') . $this->periodo, 0, 1, 'C');
                
                // 4. Usuario que generó el reporte
                $this->SetFont('Arial','I',9);
                $this->Cell(0, 7, iconv('UTF-8', 'windows-1252', 'Reporte generado por: ') . iconv('UTF-8', 'windows-1252', $this->nombreUsuario), 0, 1, 'C');

                // 5. Salto de línea para separar la cabecera del contenido
                $this->Ln(10);
            }

            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial','',8);
                
                $texto1 = iconv('UTF-8', 'windows-1252', 'Sistema de Inventario Desarrollado por ');
                $texto_bold = 'CelestiumSoft';
                $texto2 = iconv('UTF-8', 'windows-1252', ' | CGE. Todos los derechos reservados.');

                $ancho_texto1 = $this->GetStringWidth($texto1);
                $this->SetFont('Arial','B',8);
                $ancho_bold = $this->GetStringWidth($texto_bold);
                $this->SetFont('Arial','',8);
                $ancho_texto2 = $this->GetStringWidth($texto2);
                $ancho_total = $ancho_texto1 + $ancho_bold + $ancho_texto2;

                $posicion_inicial = ($this->GetPageWidth() - $ancho_total) / 2;
                $this->SetX($posicion_inicial);

                $this->Cell($ancho_texto1, 10, $texto1, 0, 0, 'L');
                $this->SetFont('Arial','B',8);
                $this->Cell($ancho_bold, 10, $texto_bold, 0, 0, 'L');
                $this->SetFont('Arial','',8);
                $this->Cell($ancho_texto2, 10, $texto2, 0, 0, 'L');
            }
        }

        $pdf = new PDF();
        $pdf->setPeriodo($periodoStr);
        $pdf->setNombreUsuario($nombreUsuarioReporte); // Pasamos el nombre del usuario
        $pdf->AddPage();
        
        // El resto del código para generar las tablas se mantiene igual...
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Consumo General por Implemento'), 0, 1);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(90, 7, 'Producto', 1, 0, 'C');
        $pdf->Cell(40, 7, 'Unidad', 1, 0, 'C');
        $pdf->Cell(40, 7, 'Total Consumido', 1, 1, 'C');
        $pdf->SetFont('Arial','',10);
        foreach ($consumoGeneral as $fila) {
            $pdf->Cell(90, 7, iconv('UTF-8', 'windows-1252', $fila['nombre_producto']), 1);
            $pdf->Cell(40, 7, iconv('UTF-8', 'windows-1252', $fila['unidad_medida']), 1);
            $pdf->Cell(40, 7, $fila['total_consumido'], 1, 1, 'R');
        }

        $pdf->Ln(10);

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Consumo Detallado por Espacio y Jornada'), 0, 1);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(60, 7, 'Espacio', 1, 0, 'C');
        $pdf->Cell(60, 7, 'Producto', 1, 0, 'C');
        $pdf->Cell(30, 7, 'Jornada', 1, 0, 'C');
        $pdf->Cell(30, 7, 'Consumo', 1, 1, 'C');
        $pdf->SetFont('Arial','',9);
        foreach ($consumoDetallado as $fila) {
             $pdf->Cell(60, 7, iconv('UTF-8', 'windows-1252', $fila['nombre_espacio'] . ' - ' . $fila['piso']), 1);
             $pdf->Cell(60, 7, iconv('UTF-8', 'windows-1252', $fila['nombre_producto']), 1);
             $pdf->Cell(30, 7, iconv('UTF-8', 'windows-1252', $fila['jornada'] ?: 'N/A'), 1);
             $pdf->Cell(30, 7, $fila['total_consumido'], 1, 1, 'R');
        }

        $pdf->Output('D', 'Reporte_Consumo_' . $periodoFile . '.pdf');
        exit();
    }
}
// --- FIN DE LA LÓGICA DE EXPORTACIÓN ---


// --- LÓGICA PARA GENERAR EL REPORTE VÍA AJAX ---
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no reconocida.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (!isset($_SESSION['id_usuario'])) {
    $response['message'] = 'Error: Sesión no válida.';
    echo json_encode($response);
    exit();
}

$reporteDAO = new ReporteDAO();

if ($action === 'generarReporte') {
    $rango = $_POST['rango'] ?? 'hoy';
    date_default_timezone_set('America/Guayaquil');
    switch ($rango) {
        case 'semana':
            $fechaInicio = date('Y-m-d 00:00:00', strtotime('monday this week'));
            $fechaFin = date('Y-m-d 23:59:59', strtotime('sunday this week'));
            break;
        case 'mes':
            $fechaInicio = date('Y-m-01 00:00:00');
            $fechaFin = date('Y-m-t 23:59:59');
            break;
        default: // 'hoy'
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
}

echo json_encode($response);

?>