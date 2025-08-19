// Archivo: view/assets/js/movimientos.js
document.addEventListener('DOMContentLoaded', function() {
    // --- Selección de Elementos ---
    const btnEntrada = document.getElementById('btnRegistrarEntrada');
    const btnSalida = document.getElementById('btnRegistrarSalida');
    const modal = document.getElementById('movimientoModal');
    const closeModalBtn = document.getElementById('closeModal');
    const movimientoForm = document.getElementById('movimientoForm');
    const modalTitle = document.getElementById('modalTitle');
    const camposSalida = document.getElementById('camposSalida');
    const btnFiltrar = document.getElementById('btnFiltrar');
    // CORRECCIÓN 1: Definir tablaBody aquí para que sea accesible globalmente en el script.
    const tablaBody = document.getElementById('tablaMovimientosBody'); 

    // --- Funciones ---

    function cargarMovimientos() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        const formData = new FormData();
        formData.append('action', 'listarRecientes');

        if (fechaInicio && fechaFin) {
            formData.append('fecha_inicio', fechaInicio);
            formData.append('fecha_fin', fechaFin);
        }

        fetch('../../controller/MovimientoController.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                // Ahora usamos la variable tablaBody definida arriba
                tablaBody.innerHTML = '';
                if (data.success && data.data.length > 0) {
                    data.data.forEach(m => {
                        let descripcion = '';
                        let color = m.tipo_movimiento === 'Entrada' ? 'text-green-600' : 'text-red-600';
                        
                        if (m.tipo_movimiento === 'Salida') {
                            descripcion = `<strong class="${color}">SALIDA</strong> de <strong>${m.cantidad} ${m.unidad_medida}</strong> de <strong>${m.nombre_producto}</strong> a <strong>${m.nombre_espacio || 'N/A'}</strong> en jornada <strong>${m.jornada || 'N/A'}</strong>.`;
                        } else {
                            descripcion = `<strong class="${color}">ENTRADA</strong> de <strong>${m.cantidad} ${m.unidad_medida}</strong> de <strong>${m.nombre_producto}</strong> al inventario general.`;
                        }
                        
                        // Añadimos una descripción si es una corrección
                        if (m.es_correccion == 1) {
                            descripcion += ` <span class="text-yellow-600 font-bold">(CORRECCIÓN)</span>`;
                        }
                        if (m.descripcion_original) {
                             descripcion += ` <span class="text-gray-500 italic"> - "${m.descripcion_original}"</span>`;
                        }

                        const fecha = new Date(m.fecha_movimiento);
                        const fechaFormateada = fecha.toLocaleDateString('es-EC', { year: 'numeric', month: '2-digit', day: '2-digit' }) + ' ' + fecha.toLocaleTimeString('es-EC', { hour: '2-digit', minute: '2-digit' });

                        // CORRECCIÓN 2: Añadir la celda de "Acciones" (<td>) con el botón.
                        const accionesHtml = m.es_correccion == 0
                            ? `<button class="btn-corregir bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 text-sm" data-id="${m.id_movimiento}">Corregir</button>`
                            : '<span class="text-gray-400 text-sm">No aplicable</span>';

                        tablaBody.innerHTML += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">${descripcion}</td>
                                <td class="py-3 px-4">${m.nombre_usuario}</td>
                                <td class="py-3 px-4">${fechaFormateada}</td>
                                <td class="py-3 px-4">${accionesHtml}</td>
                            </tr>
                        `;
                    });
                } else {
                    tablaBody.innerHTML = '<tr><td colspan="4" class="text-center py-4">No hay movimientos para mostrar.</td></tr>';
                }
            });
    }

    function cargarSelects() {
        const productoSelect = document.getElementById('id_producto');
        const espacioSelect = document.getElementById('id_espacio');

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

    // --- Asignación de Eventos ---

    btnEntrada.addEventListener('click', () => abrirModal('Entrada'));
    btnSalida.addEventListener('click', () => abrirModal('Salida'));
    closeModalBtn.addEventListener('click', cerrarModal);
    btnFiltrar.addEventListener('click', cargarMovimientos);

    // UN SOLO EVENTO PARA EL FORMULARIO
    movimientoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(movimientoForm);
        
        fetch('../../controller/MovimientoController.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                cerrarModal();
                cargarSelects();      // Recarga los selects para actualizar el stock
                cargarMovimientos();  // Recarga la tabla para mostrar el nuevo movimiento
            }
        });
    });
    
    // CORRECCIÓN 3: El evento ahora funciona porque tablaBody está definida.
    tablaBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-corregir')) {
            const idMovimiento = e.target.dataset.id;
            if (confirm('¿Estás seguro de que deseas corregir este movimiento?\nSe creará un movimiento inverso para anularlo.')) {
                const formData = new FormData();
                formData.append('action', 'corregir');
                formData.append('id_movimiento', idMovimiento);
                
                fetch('../../controller/MovimientoController.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            cargarSelects(); // Actualizar stock en selects
                            cargarMovimientos(); // Refrescar la tabla
                        }
                    });
            }
        }
    });


    // --- Carga Inicial ---
    cargarSelects();
    cargarMovimientos(); 
});