// Archivo: view/assets/js/dashboard.js

window.actualizarDashboard = function(data) {
    // Actualizar tarjetas de resumen
    document.getElementById('total-productos').textContent = data.resumen.total_productos;
    document.getElementById('total-espacios').textContent = data.resumen.total_espacios;
    document.getElementById('alertas-stock').textContent = data.resumen.alertas_stock;
    document.getElementById('movimientos-hoy').textContent = data.resumen.movimientos_hoy;

    // Actualizar tabla de inventario actual
    const inventarioBody = document.getElementById('inventario-body');
    inventarioBody.innerHTML = '';
    
    if (data.inventarioActual && data.inventarioActual.length > 0) {
        data.inventarioActual.forEach(p => {
            const stockClass = parseFloat(p.stock_actual) <= parseFloat(p.stock_minimo) ? 'text-red-500 font-bold' : '';
            
            // Sanitizar datos antes de insertarlos en el HTML
            const nombreProducto = sanitizeHTML(p.nombre_producto);
            const stockActual = sanitizeHTML(p.stock_actual);
            const unidadMedida = sanitizeHTML(p.unidad_medida);

            inventarioBody.innerHTML += `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4">${nombreProducto}</td>
                    <td class="py-2 px-4 text-right ${stockClass}">${stockActual}</td>
                    <td class="py-2 px-4">${unidadMedida}</td>
                </tr>
            `;
        });
    } else {
        inventarioBody.innerHTML = '<tr><td colspan="3" class="text-center py-4">No hay productos para mostrar.</td></tr>';
    }

    // Actualizar tabla de actividad reciente
    const actividadBody = document.getElementById('actividad-reciente-body');
    actividadBody.innerHTML = '';
    if (data.movimientosHoy && data.movimientosHoy.length > 0) {
        data.movimientosHoy.forEach(m => {
            let descripcion = '';
            let color = m.tipo_movimiento === 'Entrada' ? 'text-green-600' : 'text-red-600';
            
            // Sanitizar datos dinámicos
            const cantidad = sanitizeHTML(m.cantidad);
            const unidadMedida = sanitizeHTML(m.unidad_medida);
            const nombreProducto = sanitizeHTML(m.nombre_producto);
            const nombreUsuario = sanitizeHTML(m.nombre_usuario);

            if (m.tipo_movimiento === 'Salida') {
                descripcion = `<strong class="${color}">SALIDA</strong> de <strong>${cantidad} ${unidadMedida}</strong> de <strong>${nombreProducto}</strong>.`;
            } else {
                descripcion = `<strong class="${color}">ENTRADA</strong> de <strong>${cantidad} ${unidadMedida}</strong> de <strong>${nombreProducto}</strong>.`;
            }

            actividadBody.innerHTML += `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4">${descripcion}</td>
                    <td class="py-2 px-4 text-gray-700">${nombreUsuario}</td>
                </tr>
            `;
        });
    } else {
        actividadBody.innerHTML = '<tr><td colspan="2" class="text-center py-4">No hay movimientos registrados hoy.</td></tr>';
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Código futuro que dependa del DOM puede ir aquí.
});