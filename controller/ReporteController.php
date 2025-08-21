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
            private $nombreUsuario;
            var $widths;
            var $aligns;

            function setPeriodo($periodo) { $this->periodo = $periodo; }
            function setNombreUsuario($nombre) { $this->nombreUsuario = $nombre; }

            function Header() {
                $this->Image('../view/assets/img/logo.png', 10, 8, 25);
                $this->SetFont('Arial','B',14);
                $this->Cell(0, 7, iconv('UTF-8', 'windows-1252', 'CGE - Reporte de Consumo'), 0, 1, 'C');
                $this->SetFont('Arial','',10);
                $this->Cell(0, 7, iconv('UTF-8', 'windows-1252', 'Período: ') . $this->periodo, 0, 1, 'C');
                $this->SetFont('Arial','I',9);
                $this->Cell(0, 7, iconv('UTF-8', 'windows-1252', 'Reporte generado por: ') . iconv('UTF-8', 'windows-1252', $this->nombreUsuario), 0, 1, 'C');
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

            // --- NUEVAS FUNCIONES PARA TABLAS CON MULTICELDAS ---
            function SetWidths($w) { $this->widths = $w; }
            function SetAligns($a) { $this->aligns = $a; }

            function Row($data) {
                $nb = 0;
                for($i=0; $i<count($data); $i++)
                    $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
                $h = 5 * $nb;
                $this->CheckPageBreak($h);
                for($i=0; $i<count($data); $i++) {
                    $w = $this->widths[$i];
                    $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                    $x = $this->GetX();
                    $y = $this->GetY();
                    $this->Rect($x, $y, $w, $h);
                    $this->MultiCell($w, 5, $data[$i], 0, $a);
                    $this->SetXY($x + $w, $y);
                }
                $this->Ln($h);
            }

            function CheckPageBreak($h) {
                if($this->GetY() + $h > $this->PageBreakTrigger)
                    $this->AddPage($this->CurOrientation);
            }

            function NbLines($w, $txt) {
                $cw = &$this->CurrentFont['cw'];
                if($w==0) $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                $s = str_replace("\r", '', $txt);
                $nb = strlen($s);
                if($nb>0 && $s[$nb-1]=="\n") $nb--;
                $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
                while($i<$nb) {
                    $c = $s[$i];
                    if($c=="\n") {
                        $i++; $sep = -1; $j = $i; $l = 0; $nl++;
                        continue;
                    }
                    if($c==' ') $sep = $i;
                    $l += $cw[$c];
                    if($l>$wmax) {
                        if($sep==-1) {
                            if($i==$j) $i++;
                        } else
                            $i = $sep+1;
                        $sep = -1; $j = $i; $l = 0; $nl++;
                    } else
                        $i++;
                }
                return $nl;
            }
        } // Fin de la clase PDF

        $pdf = new PDF();
        $pdf->setPeriodo($periodoStr);
        $pdf->setNombreUsuario($nombreUsuarioReporte);
        $pdf->AddPage();
        
        // --- Tabla de Consumo General (MODIFICADA) ---
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Consumo General por Implemento'), 0, 1);
        $pdf->SetFont('Arial','B',10);
        // Ancho total: 90 + 50 + 40 = 180
        $pdf->SetWidths([90, 50, 40]);
        $pdf->SetAligns(['C', 'C', 'C']);
        $pdf->Row(['Producto', 'Unidad', 'Total Consumido']);
        
        $pdf->SetFont('Arial','',10);
        $pdf->SetAligns(['L', 'L', 'R']);
        foreach ($consumoGeneral as $fila) {
            $pdf->Row([
                iconv('UTF-8', 'windows-1252', $fila['nombre_producto']),
                iconv('UTF-8', 'windows-1252', $fila['unidad_medida']),
                $fila['total_consumido']
            ]);
        }

        $pdf->Ln(10);

        // --- Tabla de Consumo Detallado (MODIFICADA) ---
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Consumo Detallado por Espacio y Jornada'), 0, 1);
        $pdf->SetFont('Arial','B',10);
        // Ancho total: 60 + 60 + 30 + 30 = 180
        $pdf->SetWidths([60, 60, 30, 30]);
        $pdf->SetAligns(['C', 'C', 'C', 'C']);
        $pdf->Row(['Espacio', 'Producto', 'Jornada', 'Consumo']);
        
        $pdf->SetFont('Arial','',9);
        $pdf->SetAligns(['L', 'L', 'C', 'R']);
        foreach ($consumoDetallado as $fila) {
             $pdf->Row([
                iconv('UTF-8', 'windows-1252', $fila['nombre_espacio'] . ' - ' . $fila['piso']),
                iconv('UTF-8', 'windows-1252', $fila['nombre_producto']),
                iconv('UTF-8', 'windows-1252', $fila['jornada'] ?: 'N/A'),
                $fila['total_consumido']
             ]);
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