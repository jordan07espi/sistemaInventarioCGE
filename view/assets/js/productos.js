// Archivo: view/assets/js/productos.js
document.addEventListener('DOMContentLoaded', function() {
    const btnNuevo = document.getElementById('btnNuevoProducto');
    const modal = document.getElementById('productoModal');
    const closeModalBtn = document.getElementById('closeModal');
    const productoForm = document.getElementById('productoForm');
    const modalTitle = document.getElementById('modalTitle');
    const actionInput = document.getElementById('action');
    const idInput = document.getElementById('id_producto');
    const stockDiv = document.getElementById('stock_div');

    function abrirModal(titulo, action, producto = null) {
        modalTitle.textContent = titulo;
        actionInput.value = action;
        productoForm.reset();
        idInput.value = '';
        stockDiv.classList.add('hidden');

        if (producto) {
            idInput.value = producto.id_producto;
            document.getElementById('nombre_producto').value = producto.nombre_producto;
            document.getElementById('unidad_medida').value = producto.unidad_medida;
            document.getElementById('stock_minimo').value = producto.stock_minimo;
        } else {
            stockDiv.classList.remove('hidden');
        }
        
        modal.classList.remove('hidden');
    }

    function cerrarModal() {
        modal.classList.add('hidden');
    }

    btnNuevo.addEventListener('click', () => abrirModal('Nuevo Producto', 'agregar'));
    closeModalBtn.addEventListener('click', cerrarModal);
    
    function cargarProductos() {
        fetch('../../controller/ProductoController.php?action=listar')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('tablaProductosBody');
                tbody.innerHTML = '';
                if (data.success) {
                    data.data.forEach(p => {
                        // Sanitizar datos antes de insertarlos
                        const nombreProducto = sanitizeHTML(p.nombre_producto);
                        const unidadMedida = sanitizeHTML(p.unidad_medida);
                        const stockActual = sanitizeHTML(p.stock_actual);
                        const stockMinimo = sanitizeHTML(p.stock_minimo);

                        tbody.innerHTML += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4">${nombreProducto}</td>
                                <td class="py-2 px-4">${unidadMedida}</td>
                                <td class="py-2 px-4">${stockActual}</td>
                                <td class="py-2 px-4">${stockMinimo}</td>
                                <td class="py-2 px-4">
                                    <button class="btn-editar bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600" data-id="${p.id_producto}">Editar</button>
                                    <button class="btn-eliminar bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" data-id="${p.id_producto}">Eliminar</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
    }

    productoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(productoForm);
        
        fetch('../../controller/ProductoController.php',{
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cerrarModal();
                cargarProductos();
            } else {
                alert(data.message);
            }
        });
    });

    document.getElementById('tablaProductosBody').addEventListener('click', function(e) {
        const id = e.target.dataset.id;
        if (e.target.classList.contains('btn-editar')) {
            const formData = new FormData();
            formData.append('action', 'obtener');
            formData.append('id_producto', id);
            
            fetch('../../controller/ProductoController.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        abrirModal('Editar Producto', 'actualizar', data.data);
                    }
                });
        }

        // ===== SECCIÓN DE ELIMINAR (AHORA ACTIVA) =====
        if (e.target.classList.contains('btn-eliminar')) {
            // Usamos un popup de confirmación nativo del navegador
            if (confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
                const formData = new FormData();
                formData.append('action', 'eliminar');
                formData.append('id_producto', id);

                fetch('../../controller/ProductoController.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            cargarProductos(); 
                        } else {
                            alert(data.message); 
                        }
                    });
            }
        }
    });

    cargarProductos();
});