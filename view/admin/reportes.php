<?php
// Archivo: view/admin/reportes.php
session_start();
include '../partials/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800">Reportes de Consumo</h1>
<p class="text-gray-600 mb-4">Analiza el consumo por período para planificar compras.</p>
<hr class="my-4">

<div class="bg-white p-4 rounded-lg shadow-md mb-6 flex items-center space-x-4">
    <div class="flex-grow">
        <label for="rangoFechas" class="block text-sm font-medium text-gray-700">Rango de Fechas</label>
        <select id="rangoFechas" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="hoy">Hoy</option>
            <option value="semana">Esta Semana</option>
            <option value="mes">Este Mes</option>
        </select>
    </div>
    <button id="btnGenerarReporte" class="self-end bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
        <i class="fas fa-chart-bar mr-2"></i>Generar Reporte
    </button>
</div>

<div id="zonaResultados" class="hidden">
    <div class="flex justify-end space-x-2 mb-4">
        <a id="btnExportarPdf" href="#" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
            <i class="fas fa-file-pdf mr-2"></i>Exportar a PDF
        </a>
        <a id="btnExportarExcel" href="#" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
            <i class="fas fa-file-excel mr-2"></i>Exportar a Excel
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-2">Consumo General por Implemento</h2>
            <p id="periodoGeneral" class="text-sm text-gray-500 mb-4"></p>
            <div id="reporteGeneralBody">
                </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-2">Consumo Detallado por Espacio</h2>
            <p id="periodoDetallado" class="text-sm text-gray-500 mb-4"></p>
            <div id="reporteDetalladoBody">
                </div>
        </div>
    </div>
</div>

<script src="../assets/js/reportes.js"></script>

<?php 
include '../partials/footer.php'; 
?>