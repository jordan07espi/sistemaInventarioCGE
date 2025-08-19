<?php
// Archivo: view/admin/espacios.php
session_start();
include '../partials/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800">Gestión de Espacios</h1>
<hr class="my-4">

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Listado de Espacios</h2>
        <button id="btnNuevoEspacio" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nuevo Espacio
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="py-2 px-4">Nombre del Espacio</th>
                    <th class="py-2 px-4">Piso</th>
                    <th class="py-2 px-4">Descripción</th>
                    <th class="py-2 px-4">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaEspaciosBody">
                <!-- Filas se cargarán con AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Agregar/Editar Espacio -->
<div id="espacioModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 id="modalTitle" class="text-2xl font-bold"></h2>
            <button id="closeModal" class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>
        <form id="espacioForm">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="id_espacio" id="id_espacio">
            
            <div class="mb-4">
                <label for="nombre_espacio" class="block text-gray-700">Nombre del Espacio</label>
                <input type="text" name="nombre_espacio" id="nombre_espacio" class="w-full border rounded px-3 py-2 mt-1" required>
            </div>
            <div class="mb-4">
                <label for="piso" class="block text-gray-700">Piso / Ubicación</label>
                <input type="text" name="piso" id="piso" class="w-full border rounded px-3 py-2 mt-1" placeholder="Ej: Piso 1, Planta Baja">
            </div>
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="w-full border rounded px-3 py-2 mt-1" rows="3"></textarea>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/espacios.js"></script>

<?php 
include '../partials/footer.php'; 
?>