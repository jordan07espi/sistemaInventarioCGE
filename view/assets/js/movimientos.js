// Archivo: view/assets/js/movimientos.js
document.addEventListener('DOMContentLoaded', function() {
    const btnEntrada = document.getElementById('btnRegistrarEntrada');
    const btnSalida = document.getElementById('btnRegistrarSalida');
    const modal = document.getElementById('movimientoModal');
    const closeModalBtn = document.getElementById('closeModal');
    const movimientoForm = document.getElementById('movimientoForm');
    const modalTitle = document.getElementById('modalTitle');
    
    const camposSalida = document.getElementById('camposSalida');

    // Cargar productos y espacios en los selects
    function cargarSelects() {
        const productoSelect = document.getElementById('id_producto');
        const espacioSelect = document.getElementById('id_espacio');

        // Cargar productos
        fetch('../../controller/ProductoController.php?action=listar')
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
                    data.data.forEach(p => {
                        productoSelect.innerHTML += `<option value="${p.id_producto}">${p.nombre_producto} (${p.stock_actual} ${p.unidad_medida})</option>`;
                    });
                }
            });

        // Cargar espacios
        fetch('../../controller/EspacioController.php?action=listar')
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    espacioSelect.innerHTML = '<option value="">Seleccione un espacio</option>';
                    data.data.forEach(e => {
                        espacioSelect.innerHTML += `<option value="${e.id_espacio}">${e.nombre_espacio} - ${e.piso}</option>`;
                    });
                }
            });
    }

    function abrirModal(tipo) {
        movimientoForm.reset();
        document.getElementById('tipo_movimiento').value = tipo;

        if (tipo === 'Entrada') {
            modalTitle.textContent = 'Registrar Entrada de Producto';
            camposSalida.classList.add('hidden');
        } else {
            modalTitle.textContent = 'Registrar Salida de Producto';
            camposSalida.classList.remove('hidden');
        }
        modal.classList.remove('hidden');
    }

    function cerrarModal() {
        modal.classList.add('hidden');
    }

    btnEntrada.addEventListener('click', () => abrirModal('Entrada'));
    btnSalida.addEventListener('click', () => abrirModal('Salida'));
    closeModalBtn.addEventListener('click', cerrarModal);

    movimientoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(movimientoForm);
        
        fetch('../../controller/MovimientoController.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message); // Mostramos un simple alert por ahora
            if (data.success) {
                cerrarModal();
                cargarSelects(); // Recargamos los selects para actualizar el stock visible
                // Aquí en el futuro recargaremos la tabla de actividad del día
            }
        });
    });

    // Carga inicial de datos para los modales
    cargarSelects();
});