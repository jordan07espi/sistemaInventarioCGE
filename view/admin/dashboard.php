<?php
// Archivo: view/admin/dashboard.php
session_start();
include '../partials/header.php'; // Incluye el nuevo header con la navbar
?>

<!-- El contenido de la página va directamente aquí -->
<h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
<hr class="my-4">

<!-- Sección de Resumen -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Productos Totales</p>
            <p class="text-2xl font-bold">15</p> <!-- Dato de ejemplo -->
        </div>
        <i class="fas fa-box fa-3x text-blue-500"></i>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Espacios Registrados</p>
            <p class="text-2xl font-bold">25</p> <!-- Dato de ejemplo -->
        </div>
        <i class="fas fa-map-marker-alt fa-3x text-green-500"></i>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Alertas de Stock</p>
            <p class="text-2xl font-bold text-red-500">3</p> <!-- Dato de ejemplo -->
        </div>
        <i class="fas fa-exclamation-triangle fa-3x text-red-500"></i>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Movimientos Hoy</p>
            <p class="text-2xl font-bold">42</p> <!-- Dato de ejemplo -->
        </div>
        <i class="fas fa-exchange-alt fa-3x text-yellow-500"></i>
    </div>
</div>

<!-- Sección de Actividad e Inventario -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Actividad del día -->
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Actividad del Día</h2>
        <p class="text-gray-600">Próximamente: Tabla de actividad con filtros y paginación...</p>
    </div>

    <!-- Inventario Actual -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Inventario Actual</h2>
        <p class="text-gray-600">Próximamente: Lista de productos con su stock...</p>
    </div>
</div>

<?php 
include '../partials/footer.php'; 
?>