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
    <div class="overflow-x-auto">
        <!-- Aquí irá la tabla de movimientos del día, que construiremos más adelante -->
        <p>Próximamente: listado de movimientos...</p>
    </div>
</div>

<!-- Modal para Registrar Movimiento -->
<div id="movimientoModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 id="modalTitle" class="text-2xl font-bold"></h2>
            <button id="closeModal" class="text-gray-500 hover:text-gray-800 text-3xl leading-none">&times;</button>
        </div>
        <form id="movimientoForm">
            <input type="hidden" name="action" value="registrar">
            <input type="hidden" name="tipo_movimiento" id="tipo_movimiento">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="id_producto" class="block text-gray-700">Producto</label>
                    <select name="id_producto" id="id_producto" class="w-full border rounded px-3 py-2 mt-1" required></select>
                </div>
                <div>
                    <label for="cantidad" class="block text-gray-700">Cantidad</label>
                    <input type="number" name="cantidad" id="cantidad" class="w-full border rounded px-3 py-2 mt-1" required step="0.01" min="0.01">
                </div>
            </div>

            <!-- Campos que solo aparecen en la SALIDA -->
            <div id="camposSalida" class="hidden mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="id_espacio" class="block text-gray-700">Espacio de Destino</label>
                        <select name="id_espacio" id="id_espacio" class="w-full border rounded px-3 py-2 mt-1"></select>
                    </div>
                    <div>
                        <label for="jornada" class="block text-gray-700">Jornada</label>
                        <select name="jornada" id="jornada" class="w-full border rounded px-3 py-2 mt-1">
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                            <option value="Nocturno">Nocturno</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label for="descripcion" class="block text-gray-700">Descripción (Opcional)</label>
                <textarea name="descripcion" id="descripcion" class="w-full border rounded px-3 py-2 mt-1" rows="2"></textarea>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar Movimiento</button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/movimientos.js"></script>

<?php 
include '../partials/footer.php'; 
?>