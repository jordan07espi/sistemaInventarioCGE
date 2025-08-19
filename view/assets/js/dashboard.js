// Archivo: view/assets/js/dashboard.js

/**
 * Esta función ahora se define directamente en el objeto 'window', 
 * asegurando que esté disponible globalmente tan pronto como el navegador
 * cargue este archivo. Esto resuelve la condición de carrera.
 */
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
            // Se añade una clase de color rojo si el stock es bajo para mayor visibilidad
            const stockClass = parseFloat(p.stock_actual) <= parseFloat(p.stock_minimo) ? 'text-red-500 font-bold' : '';
            inventarioBody.innerHTML += `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4">${p.nombre_producto}</td>
                    <td class="py-2 px-4 text-right ${stockClass}">${p.stock_actual}</td>
                    <td class="py-2 px-4">${p.unidad_medida}</td>
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
            
            if (m.tipo_movimiento === 'Salida') {
                descripcion = `<strong class="${color}">SALIDA</strong> de <strong>${m.cantidad} ${m.unidad_medida}</strong> de <strong>${m.nombre_producto}</strong>.`;
            } else {
                descripcion = `<strong class="${color}">ENTRADA</strong> de <strong>${m.cantidad} ${m.unidad_medida}</strong> de <strong>${m.nombre_producto}</strong>.`;
            }

            actividadBody.innerHTML += `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4">${descripcion}</td>
                    <td class="py-2 px-4 text-gray-700">${m.nombre_usuario}</td>
                </tr>
            `;
        });
    } else {
        actividadBody.innerHTML = '<tr><td colspan="2" class="text-center py-4">No hay movimientos registrados hoy.</td></tr>';
    }
};

// El listener DOMContentLoaded ya no es necesario aquí para definir la función,
// pero lo mantenemos por si quieres añadir lógica futura que sí dependa del DOM.
document.addEventListener('DOMContentLoaded', function() {
    // Puedes poner aquí código que se ejecute solo cuando el HTML del dashboard esté listo.
});