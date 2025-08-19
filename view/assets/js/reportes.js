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
            // Sanitizar
            const nombreProducto = sanitizeHTML(item.nombre_producto);
            const unidadMedida = sanitizeHTML(item.unidad_medida);
            const totalConsumido = sanitizeHTML(item.total_consumido);

            html += `
                <li class="flex justify-between items-center border-b pb-1">
                    <span>${nombreProducto} (${unidadMedida})</span>
                    <span class="font-bold text-lg">${parseFloat(totalConsumido)}</span>
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
            // Sanitizar
            const nombreEspacio = sanitizeHTML(item.nombre_espacio);
            const piso = sanitizeHTML(item.piso);
            const nombreProducto = sanitizeHTML(item.nombre_producto);
            const unidadMedida = sanitizeHTML(item.unidad_medida);
            const jornada = sanitizeHTML(item.jornada ? item.jornada : 'No especificada');
            const totalConsumido = sanitizeHTML(item.total_consumido);

            const nombreCompletoEspacio = `${nombreEspacio} - ${piso}`;
            if (nombreCompletoEspacio !== espacioActual) {
                if (espacioActual !== '') {
                    html += '</ul></div>';
                }
                espacioActual = nombreCompletoEspacio;
                html += `
                    <div class="mb-4">
                        <h4 class="font-bold text-indigo-700">${espacioActual}</h4>
                        <ul class="space-y-1 mt-1 pl-2 border-l-2 border-gray-200">
                `;
            }

            html += `
                <li class="flex justify-between items-center text-sm">
                    <span>${nombreProducto} (${unidadMedida}) - <em class="text-gray-500">${jornada}</em></span>
                    <span class="font-semibold">${parseFloat(totalConsumido)}</span>
                </li>
            `;
        });
        html += '</ul></div>';
        reporteDetalladoBody.innerHTML = html;
    }
});