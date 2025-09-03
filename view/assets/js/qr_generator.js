// Archivo: view/assets/js/qr_generator.js
document.addEventListener('DOMContentLoaded', function() {
    const selectProducto = document.getElementById('selectProducto');
    const selectEspacio = document.getElementById('selectEspacio');
    const btnGenerarQR = document.getElementById('btnGenerarQR');
    const qrContainer = document.getElementById('qr-container');
    const qrPlaceholder = document.getElementById('qr-placeholder');
    const qrResult = document.getElementById('qr-result');
    const qrCodeDiv = document.getElementById('qrcode');
    const qrLabel = document.getElementById('qr-label');
    const btnImprimirQR = document.getElementById('btnImprimirQR');

    let qrcode = null; // Variable para mantener la instancia del QR

    function cargarSelects() {
        // Cargar Productos
        fetch('../../controller/ProductoController.php?action=listar')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    selectProducto.innerHTML = '<option value="">-- Seleccione Producto --</option>';
                    data.data.forEach(p => {
                        selectProducto.innerHTML += `<option value="${p.id_producto}">${sanitizeHTML(p.nombre_producto)}</option>`;
                    });
                }
            });

        // Cargar Espacios
        fetch('../../controller/EspacioController.php?action=listar')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    selectEspacio.innerHTML = '<option value="">-- Seleccione Espacio --</option>';
                    data.data.forEach(e => {
                        selectEspacio.innerHTML += `<option value="${e.id_espacio}">${sanitizeHTML(e.nombre_espacio)} - ${sanitizeHTML(e.piso)}</option>`;
                    });
                }
            });
    }

    btnGenerarQR.addEventListener('click', function() {
        const productoId = selectProducto.value;
        const espacioId = selectEspacio.value;

        if (!productoId || !espacioId) {
            alert('Por favor, seleccione un producto y un espacio.');
            return;
        }

        const productoTexto = selectProducto.options[selectProducto.selectedIndex].text;
        const espacioTexto = selectEspacio.options[selectEspacio.selectedIndex].text;
        
        // Construir la URL base del sitio de forma dinámica
        const baseUrl = window.location.origin + window.location.pathname.substring(0, window.location.pathname.indexOf('/view/'));
        const urlParaQR = `${baseUrl}/controller/QrController.php?action=registrarSalida&id_producto=${productoId}&id_espacio=${espacioId}`;

        // Limpiar QR anterior
        qrCodeDiv.innerHTML = '';
        if (qrcode) {
            qrcode.clear();
        }

        // Generar nuevo QR
        qrcode = new QRCode(qrCodeDiv, {
            text: urlParaQR,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        qrLabel.textContent = `QR para: ${productoTexto} en ${espacioTexto}`;
        qrPlaceholder.classList.add('hidden');
        qrResult.classList.remove('hidden');
    });

    btnImprimirQR.addEventListener('click', function() {
        const qrContent = document.getElementById('qr-result').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Imprimir Código QR</title>
                    <style>
                        body { font-family: sans-serif; text-align: center; margin-top: 50px; }
                        #qrcode img { display: block; margin: 0 auto; }
                        #qr-label { font-size: 16px; font-weight: bold; margin-top: 10px; }
                        @media print {
                            button { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${qrContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 250);
    });

    // Carga inicial de datos
    cargarSelects();
});