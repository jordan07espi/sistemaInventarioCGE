<?php
// Archivo: view/admin/movimientos.php
session_start();
include '../partials/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800">Operación Diaria</h1>
<hr class="my-4">

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex space-x-4 mb-4">
        <button id="btnRegistrarEntrada" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i>Registrar Entrada
        </button>
        <button id="btnRegistrarSalida" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-minus mr-2"></i>Registrar Salida
        </button>
    </div>
    
    <h2 class="text-xl font-bold mt-6 mb-4">Actividad Reciente</h2>

    <div class="flex items-center space-x-4 mb-4 p-4 bg-gray-50 rounded-lg">
        <div>
            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Desde:</label>
            <input type="date" id="fecha_inicio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Hasta:</label>
            <input type="date" id="fecha_fin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <button id="btnFiltrar" class="self-end bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            <i class="fas fa-filter mr-2"></i>Filtrar
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="py-2 px-4">Descripción del Movimiento</th>
                    <th class="py-2 px-4">Usuario</th>
                    <th class="py-2 px-4">Fecha y Hora</th>
                    <th class="py-2 px-4">Acciones</th> </tr>
            </thead>
            <tbody id="tablaMovimientosBody">
                </tbody>
        </table>
    </div>
    <div id="paginacion-container" class="flex justify-center items-center mt-6 space-x-1"> </div>
</div>

<div id="movimientoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 id="modalTitle" class="text-2xl font-bold"></h2>
            <button id="closeModal" class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>
        <form id="movimientoForm">
            <input type="hidden" name="action" value="registrar">
            <input type="hidden" name="tipo_movimiento" id="tipo_movimiento">

            <div class="mb-4">
                <label for="id_producto" class="block text-gray-700">Producto</label>
                <select name="id_producto" id="id_producto" class="w-full border rounded px-3 py-2 mt-1" required></select>
            </div>
            <div class="mb-4">
                <label for="cantidad" class="block text-gray-700">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" class="w-full border rounded px-3 py-2 mt-1" required step="0.01" min="0.01">
            </div>

            <div id="camposSalida" class="hidden">
                <div class="mb-4">
                    <label for="id_espacio" class="block text-gray-700">Espacio de Destino</label>
                    <select name="id_espacio" id="id_espacio" class="w-full border rounded px-3 py-2 mt-1"></select>
                </div>
                <div class="mb-4">
                    <label for="jornada" class="block text-gray-700">Jornada</label>
                    <select name="jornada" id="jornada" class="w-full border rounded px-3 py-2 mt-1">
                        <option value="">Seleccione jornada</option>
                        <option value="Matutino">Matutino</option>
                        <option value="Vespertino">Vespertino</option>
                        <option value="Nocturno">Nocturno</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700">Descripción (Opcional)</label>
                <textarea name="descripcion" id="descripcion" class="w-full border rounded px-3 py-2 mt-1" rows="2"></textarea>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Registrar Movimiento</button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/movimientos.js"></script>

<?php 
include '../partials/footer.php'; 
?>