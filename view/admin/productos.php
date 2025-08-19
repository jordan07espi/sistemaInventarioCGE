<?php
// Archivo: view/admin/productos.php
session_start();
include '../partials/header.php'; // Incluye el nuevo header con la navbar
?>

<!-- El contenido de la página va directamente aquí -->
<h1 class="text-3xl font-bold text-gray-800">Gestión de Productos</h1>
<hr class="my-4">

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Listado de Productos</h2>
        <button id="btnNuevoProducto" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nuevo Producto
        </button>
    </div>

    <!-- Tabla de Productos -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="py-2 px-4">Nombre</th>
                    <th class="py-2 px-4">Unidad</th>
                    <th class="py-2 px-4">Stock Actual</th>
                    <th class="py-2 px-4">Stock Mínimo</th>
                    <th class="py-2 px-4">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaProductosBody">
                <!-- Las filas se cargarán aquí con AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Agregar/Editar Producto -->
<div id="productoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 id="modalTitle" class="text-2xl font-bold"></h2>
            <button id="closeModal" class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>
        <form id="productoForm">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="id_producto" id="id_producto">
            
            <div class="mb-4">
                <label for="nombre_producto" class="block text-gray-700">Nombre del Producto</label>
                <input type="text" name="nombre_producto" id="nombre_producto" class="w-full border rounded px-3 py-2 mt-1" required>
            </div>
            <div class="mb-4">
                <label for="unidad_medida" class="block text-gray-700">Unidad de Medida</label>
                <input type="text" name="unidad_medida" id="unidad_medida" class="w-full border rounded px-3 py-2 mt-1" placeholder="Ej: rollos, litros, paquetes" required>
            </div>
            <div class="mb-4" id="stock_div">
                <label for="stock_actual" class="block text-gray-700">Stock Inicial</label>
                <input type="number" name="stock_actual" id="stock_actual" class="w-full border rounded px-3 py-2 mt-1" value="0" step="0.01">
            </div>
            <div class="mb-4">
                <label for="stock_minimo" class="block text-gray-700">Stock Mínimo</label>
                <input type="number" name="stock_minimo" id="stock_minimo" class="w-full border rounded px-3 py-2 mt-1" required step="0.01">
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
            </div>
        </form>
    </div>
</div>


<script src="../assets/js/productos.js"></script>

<?php 
include '../partials/footer.php'; 
?>