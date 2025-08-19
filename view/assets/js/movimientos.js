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
    const tablaBody = document.getElementById('tablaMovimientosBody');
    const paginacionContainer = document.getElementById('paginacion-container');

    // --- Funciones ---

    function cargarMovimientos(pagina = 1) {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        const formData = new FormData();
        formData.append('action', 'listarRecientes');
        formData.append('pagina', pagina);

        if (fechaInicio && fechaFin) {
            formData.append('fecha_inicio', fechaInicio);
            formData.append('fecha_fin', fechaFin);
        }

        fetch('../../controller/MovimientoController.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                tablaBody.innerHTML = '';
                if (data.success && data.data.length > 0) {
                    data.data.forEach(m => {
                        let descripcion = '';
                        let color = m.tipo_movimiento === 'Entrada' ? 'text-green-600' : 'text-red-600';

                        // Sanitizar todos los datos antes de construir el HTML
                        const cantidad = sanitizeHTML(m.cantidad);
                        const unidadMedida = sanitizeHTML(m.unidad_medida);
                        const nombreProducto = sanitizeHTML(m.nombre_producto);
                        const nombreEspacio = sanitizeHTML(m.nombre_espacio || 'N/A');
                        const jornada = sanitizeHTML(m.jornada || 'N/A');
                        const descripcionOriginal = sanitizeHTML(m.descripcion_original);
                        const nombreUsuario = sanitizeHTML(m.nombre_usuario);

                        if (m.tipo_movimiento === 'Salida') {
                            descripcion = `<strong class="${color}">SALIDA</strong> de <strong>${cantidad} ${unidadMedida}</strong> de <strong>${nombreProducto}</strong> a <strong>${nombreEspacio}</strong> en jornada <strong>${jornada}</strong>.`;
                        } else {
                            descripcion = `<strong class="${color}">ENTRADA</strong> de <strong>${cantidad} ${unidadMedida}</strong> de <strong>${nombreProducto}</strong> al inventario general.`;
                        }
                        if (m.es_correccion == 1) {
                            descripcion += ` <span class="text-yellow-600 font-bold">(CORRECCIÓN)</span>`;
                        }
                        if (descripcionOriginal) {
                             descripcion += ` <span class="text-gray-500 italic"> - "${descripcionOriginal}"</span>`;
                        }
                        const fecha = new Date(m.fecha_movimiento);
                        const fechaFormateada = fecha.toLocaleDateString('es-EC', { year: 'numeric', month: '2-digit', day: '2-digit' }) + ' ' + fecha.toLocaleTimeString('es-EC', { hour: '2-digit', minute: '2-digit' });
                        const accionesHtml = m.es_correccion == 0
                            ? `<button class="btn-corregir bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 text-sm" data-id="${m.id_movimiento}">Corregir</button>`
                            : '<span class="text-gray-400 text-sm">No aplicable</span>';
                        tablaBody.innerHTML += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">${descripcion}</td>
                                <td class="py-3 px-4">${nombreUsuario}</td>
                                <td class="py-3 px-4">${fechaFormateada}</td>
                                <td class="py-3 px-4">${accionesHtml}</td>
                            </tr>`;
                    });
                } else {
                    tablaBody.innerHTML = '<tr><td colspan="4" class="text-center py-4">No hay movimientos para mostrar.</td></tr>';
                }
                renderizarPaginacion(data.pagination);
            });
    }

    function renderizarPaginacion(pagination) {
        paginacionContainer.innerHTML = ''; // Limpiamos los botones anteriores
        const { total_paginas, pagina_actual } = pagination;

        if (total_paginas <= 1) return; // No mostrar paginación si solo hay 1 página

        let html = '';

        // Botón "Anterior"
        html += `<button class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed" 
                         data-page="${pagina_actual - 1}" ${pagina_actual === 1 ? 'disabled' : ''}>
                         &laquo;
                 </button>`;

        // Botones de números de página
        for (let i = 1; i <= total_paginas; i++) {
            if (i === pagina_actual) {
                html += `<button class="px-3 py-1 rounded-md bg-indigo-600 text-white cursor-default" data-page="${i}" disabled>${i}</button>`;
            } else {
                html += `<button class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300" data-page="${i}">${i}</button>`;
            }
        }

        // Botón "Siguiente"
        html += `<button class="px-3 py-1 rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                         data-page="${pagina_actual + 1}" ${pagina_actual === total_paginas ? 'disabled' : ''}>
                         &raquo;
                 </button>`;
        
        paginacionContainer.innerHTML = html;
    }

    // El resto de funciones (cargarSelects, abrirModal, etc.) no cambian...
    function cargarSelects() {
        const productoSelect = document.getElementById('id_producto');
        const espacioSelect = document.getElementById('id_espacio');
        fetch('../../controller/ProductoController.php?action=listar').then(res => res.json()).then(data => {
            if(data.success) {
                productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
                data.data.forEach(p => {
                    productoSelect.innerHTML += `<option value="${p.id_producto}">${p.nombre_producto} (${p.stock_actual} ${p.unidad_medida})</option>`;
                });
            }
        });
        fetch('../../controller/EspacioController.php?action=listar').then(res => res.json()).then(data => {
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
    
    // Al filtrar, siempre volvemos a la página 1
    btnFiltrar.addEventListener('click', () => cargarMovimientos(1)); 
    
    // Evento para el formulario
    movimientoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(movimientoForm);
        fetch('../../controller/MovimientoController.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                cerrarModal();
                cargarSelects();
                cargarMovimientos(1); // Al registrar, recargamos y vamos a la página 1
            }
        });
    });
    
    // Evento para los botones de corregir
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
                            cargarSelects();
                            cargarMovimientos(); // Recarga la página actual
                        }
                    });
            }
        }
    });

    // --- NUEVO: Evento para manejar los clics en la paginación ---
    paginacionContainer.addEventListener('click', function(e) {
        if (e.target.tagName === 'BUTTON' && !e.target.disabled) {
            const pagina = e.target.dataset.page;
            if (pagina) {
                cargarMovimientos(parseInt(pagina));
            }
        }
    });

    // --- Carga Inicial ---
    cargarSelects();
    cargarMovimientos(); // Carga la primera página por defecto
});