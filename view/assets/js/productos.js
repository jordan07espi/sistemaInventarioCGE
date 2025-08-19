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
    
    // Cargar tabla de productos
    function cargarProductos() {
        fetch('../../controller/ProductoController.php?action=listar')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('tablaProductosBody');
                tbody.innerHTML = '';
                if (data.success) {
                    data.data.forEach(p => {
                        tbody.innerHTML += `
                            <tr class="border-b">
                                <td class="py-2 px-4">${p.nombre_producto}</td>
                                <td class="py-2 px-4">${p.unidad_medida}</td>
                                <td class="py-2 px-4">${p.stock_actual}</td>
                                <td class="py-2 px-4">${p.stock_minimo}</td>
                                <td class="py-2 px-4">
                                    <button class="btn-editar bg-yellow-500 text-white px-3 py-1 rounded" data-id="${p.id_producto}">Editar</button>
                                    <button class="btn-eliminar bg-red-500 text-white px-3 py-1 rounded" data-id="${p.id_producto}">Eliminar</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
    }

    // Evento para guardar (agregar/actualizar)
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
                cargarProductos(); // Recargar la tabla
                // Aquí podrías mostrar una notificación de éxito
            } else {
                alert(data.message); // O mostrar el error
            }
        });
    });

    // Delegación de eventos para botones editar y eliminar
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

        if (e.target.classList.contains('btn-eliminar')) {
            if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
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

    // Carga inicial
    cargarProductos();
});