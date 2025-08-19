// Archivo: view/assets/js/reportes.js
document.addEventListener('DOMContentLoaded', function() {
    const btnGenerar = document.getElementById('btnGenerarReporte');
    const selectRango = document.getElementById('rangoFechas');
    const zonaResultados = document.getElementById('zonaResultados');
    
    const reporteGeneralBody = document.getElementById('reporteGeneralBody');
    const reporteDetalladoBody = document.getElementById('reporteDetalladoBody');
    const periodoGeneral = document.getElementById('periodoGeneral');
    const periodoDetallado = document.getElementById('periodoDetallado');

    const btnPdf = document.getElementById('btnExportarPdf');
    const btnExcel = document.getElementById('btnExportarExcel');

    btnGenerar.addEventListener('click', function() {
        const rango = selectRango.value;
        const formData = new FormData();
        formData.append('action', 'generarReporte');
        formData.append('rango', rango);

        // Opcional: Mostrar un loader
        reporteGeneralBody.innerHTML = '<p class="text-gray-500">Generando reporte...</p>';
        reporteDetalladoBody.innerHTML = '<p class="text-gray-500">Generando reporte...</p>';
        zonaResultados.classList.remove('hidden');

        fetch('../../controller/ReporteController.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderReporteGeneral(data.data.general);
                renderReporteDetallado(data.data.detallado);
                periodoGeneral.textContent = `Periodo: ${data.data.periodo}`;
                periodoDetallado.textContent = `Periodo: ${data.data.periodo}`;

                // Actualizar links de exportación
                const baseUrl = `../../controller/ReporteController.php?rango=${rango}`;
                btnPdf.href = `${baseUrl}&action=exportarPdf`;
                btnExcel.href = `${baseUrl}&action=exportarExcel`;
            } else {
                reporteGeneralBody.innerHTML = `<p class="text-red-500">${data.message}</p>`;
                reporteDetalladoBody.innerHTML = `<p class="text-red-500">${data.message}</p>`;
            }
        });
    });

    function renderReporteGeneral(datos) {
        reporteGeneralBody.innerHTML = '';
        if (datos.length === 0) {
            reporteGeneralBody.innerHTML = '<p class="text-gray-500">No hay datos de consumo para este período.</p>';
            return;
        }
        
        let html = '<ul class="space-y-2">';
        datos.forEach(item => {
            html += `
                <li class="flex justify-between items-center border-b pb-1">
                    <span>${item.nombre_producto} (${item.unidad_medida})</span>
                    <span class="font-bold text-lg">${parseFloat(item.total_consumido)}</span>
                </li>
            `;
        });
        html += '</ul>';
        reporteGeneralBody.innerHTML = html;
    }

    function renderReporteDetallado(datos) {
        reporteDetalladoBody.innerHTML = '';
        if (datos.length === 0) {
            reporteDetalladoBody.innerHTML = '<p class="text-gray-500">No hay datos de consumo para este período.</p>';
            return;
        }

        let html = '';
        let espacioActual = '';
        datos.forEach(item => {
            const nombreCompletoEspacio = `${item.nombre_espacio} - ${item.piso}`;
            if (nombreCompletoEspacio !== espacioActual) {
                if (espacioActual !== '') {
                    html += '</ul></div>'; // Cierra el div y ul anterior
                }
                espacioActual = nombreCompletoEspacio;
                html += `
                    <div class="mb-4">
                        <h4 class="font-bold text-indigo-700">${espacioActual}</h4>
                        <ul class="space-y-1 mt-1 pl-2 border-l-2 border-gray-200">
                `;
            }

            // Si la jornada existe, la mostramos. Si es nula o vacía, mostramos 'No especificada'.
            const jornadaTexto = item.jornada ? item.jornada : 'No especificada';

            html += `
                <li class="flex justify-between items-center text-sm">
                    <span>${item.nombre_producto} (${item.unidad_medida}) - <em class="text-gray-500">${jornadaTexto}</em></span>
                    <span class="font-semibold">${parseFloat(item.total_consumido)}</span>
                </li>
            `;
        });
        html += '</ul></div>'; // Cierra el último div
        reporteDetalladoBody.innerHTML = html;
    }
});