// Archivo: view/assets/js/espacios.js
document.addEventListener('DOMContentLoaded', function() {
    const btnNuevo = document.getElementById('btnNuevoEspacio');
    const modal = document.getElementById('espacioModal');
    const closeModalBtn = document.getElementById('closeModal');
    const espacioForm = document.getElementById('espacioForm');
    const modalTitle = document.getElementById('modalTitle');
    const actionInput = document.getElementById('action');
    const idInput = document.getElementById('id_espacio');

    function abrirModal(titulo, action, espacio = null) {
        modalTitle.textContent = titulo;
        actionInput.value = action;
        espacioForm.reset();
        idInput.value = '';

        if (espacio) {
            idInput.value = espacio.id_espacio;
            document.getElementById('nombre_espacio').value = espacio.nombre_espacio;
            document.getElementById('piso').value = espacio.piso;
            document.getElementById('descripcion').value = espacio.descripcion;
        }
        
        modal.classList.remove('hidden');
    }

    function cerrarModal() {
        modal.classList.add('hidden');
    }

    btnNuevo.addEventListener('click', () => abrirModal('Nuevo Espacio', 'agregar'));
    closeModalBtn.addEventListener('click', cerrarModal);
    
    function cargarEspacios() {
        fetch('../../controller/EspacioController.php?action=listar')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('tablaEspaciosBody');
                tbody.innerHTML = '';
                if (data.success) {
                    data.data.forEach(e => {
                        tbody.innerHTML += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4">${e.nombre_espacio}</td>
                                <td class="py-2 px-4">${e.piso}</td>
                                <td class="py-2 px-4">${e.descripcion}</td>
                                <td class="py-2 px-4">
                                    <button class="btn-editar bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600" data-id="${e.id_espacio}">Editar</button>
                                    <button class="btn-eliminar bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600" data-id="${e.id_espacio}">Eliminar</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
    }

    espacioForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(espacioForm);
        
        fetch('../../controller/EspacioController.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cerrarModal();
                cargarEspacios();
            } else {
                alert(data.message);
            }
        });
    });

    document.getElementById('tablaEspaciosBody').addEventListener('click', function(e) {
        const id = e.target.dataset.id;
        if (e.target.classList.contains('btn-editar')) {
            const formData = new FormData();
            formData.append('action', 'obtener');
            formData.append('id_espacio', id);
            
            fetch('../../controller/EspacioController.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        abrirModal('Editar Espacio', 'actualizar', data.data);
                    }
                });
        }

        if (e.target.classList.contains('btn-eliminar')) {
            if (confirm('¿Estás seguro de que deseas eliminar este espacio?')) {
                const formData = new FormData();
                formData.append('action', 'eliminar');
                formData.append('id_espacio', id);

                fetch('../../controller/EspacioController.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            cargarEspacios();
                        } else {
                            alert(data.message);
                        }
                    });
            }
        }
    });

    cargarEspacios();
});