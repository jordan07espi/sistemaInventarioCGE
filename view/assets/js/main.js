/**
 * Sanitiza una cadena de texto para prevenir ataques XSS al usar innerHTML.
 * Reemplaza los caracteres HTML peligrosos con sus equivalentes seguros.
 * @param {string | number | null} str La cadena a sanitizar.
 * @returns {string} La cadena sanitizada.
 */
function sanitizeHTML(str) {
    if (str === null || typeof str === 'undefined') {
        return '';
    }
    const temp = document.createElement('div');
    temp.textContent = str.toString();
    return temp.innerHTML;
}

document.addEventListener('DOMContentLoaded', function() {
    
    function cargarDatosGlobales() {
        fetch('../../controller/DashboardController.php?action=cargarDatos')
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    const data = response.data;
                    
                    // Lógica de Alertas (para todas las páginas)
                    const badge = document.getElementById('alerta-badge');
                    const listaAlertas = document.getElementById('lista-alertas');
                    
                    if (data.productosBajoStock.length > 0) {
                        badge.textContent = data.productosBajoStock.length;
                        badge.classList.remove('hidden');
                        
                        listaAlertas.innerHTML = '';
                        data.productosBajoStock.forEach(p => {
                            // Aplicamos sanitización a los datos antes de insertarlos
                            const nombreProducto = sanitizeHTML(p.nombre_producto);
                            const stockActual = sanitizeHTML(p.stock_actual);
                            const stockMinimo = sanitizeHTML(p.stock_minimo);

                            listaAlertas.innerHTML += `
                                <li class="p-2 border-b text-sm text-gray-700 hover:bg-gray-100">
                                    <strong>${nombreProducto}</strong> solo tiene 
                                    <span class="font-bold text-red-600">${stockActual}</span>/${stockMinimo} unidades.
                                </li>
                            `;
                        });
                    } else {
                        badge.classList.add('hidden');
                        listaAlertas.innerHTML = '<li class="p-2 text-sm text-gray-500">No hay alertas.</li>';
                    }

                    // Si estamos en la página del dashboard, actualizamos sus datos específicos
                    if (typeof window.actualizarDashboard === 'function') {
                        window.actualizarDashboard(data);
                    }
                }
            })
            .catch(error => console.error('Error al cargar datos globales:', error));
    }

    // Manejar visibilidad del dropdown de alertas
    const btnAlertas = document.getElementById('btnAlertas');
    const dropdown = document.getElementById('alertas-dropdown');
    if (btnAlertas) {
        btnAlertas.addEventListener('click', () => {
            dropdown.classList.toggle('hidden');
        });

        // Ocultar si se hace clic fuera
        document.addEventListener('click', function(event) {
            if (!btnAlertas.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }

    // Carga inicial
    cargarDatosGlobales();
});