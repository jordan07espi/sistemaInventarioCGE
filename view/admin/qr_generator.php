<?php
// Archivo: view/admin/qr_generator.php
session_start();
include '../partials/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800">Generador de Códigos QR</h1>
<p class="text-gray-600 mb-4">Crea y imprime códigos QR para registrar salidas de productos en espacios específicos.</p>
<hr class="my-4">

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">1. Seleccionar Datos</h2>
        <div class="mb-4">
            <label for="selectProducto" class="block text-gray-700 mb-1">Producto</label>
            <select id="selectProducto" class="w-full border rounded px-3 py-2"></select>
        </div>
        <div class="mb-4">
            <label for="selectEspacio" class="block text-gray-700 mb-1">Espacio</label>
            <select id="selectEspacio" class="w-full border rounded px-3 py-2"></select>
        </div>
        <button id="btnGenerarQR" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition duration-300">
            <i class="fas fa-qrcode mr-2"></i>Generar QR
        </button>
    </div>

    <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">2. Visualizar e Imprimir</h2>
        <div id="qr-container" class="flex flex-col items-center justify-center text-center border-2 border-dashed rounded-lg p-8 min-h-[250px]">
            <p id="qr-placeholder" class="text-gray-500">Seleccione un producto y un espacio para generar el código QR.</p>
            <div id="qr-result" class="hidden">
                <div id="qrcode" class="p-4 border bg-white inline-block"></div>
                <p id="qr-label" class="mt-4 font-semibold text-gray-800"></p>
                <button id="btnImprimirQR" class="mt-4 bg-gray-700 text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition duration-300">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>
<script src="../assets/js/qr_generator.js"></script>

<?php 
include '../partials/footer.php'; 
?>