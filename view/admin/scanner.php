<?php
// Archivo: view/admin/scanner.php
session_start();
include '../partials/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800">Escáner QR</h1>
<p class="text-gray-600 mb-4">Apunte la cámara al código QR para registrar una salida de producto.</p>
<hr class="my-4">

<style>
    #preview {
        width: 100%;
        max-width: 500px;
        height: auto;
        margin: 0 auto;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background-color: #000;
    }
</style>

<div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <video id="preview" playsinline></video>
    <div id="scan-results" class="mt-4 text-center font-medium"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/8.2.3/adapter.min.js"></script>
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const videoElement = document.getElementById('preview');
    const resultsContainer = document.getElementById('scan-results');
    let lastScanTime = 0;

    const scanner = new Instascan.Scanner({ 
        video: videoElement,
        scanPeriod: 5,
        mirror: false 
    });

    scanner.addListener('scan', function (content) {
        const now = Date.now();
        if (now - lastScanTime < 4000) {
            return;
        }
        lastScanTime = now;

        resultsContainer.innerHTML = `
            <div class="p-3 bg-yellow-100 text-yellow-800 rounded-lg">
                Procesando código...
            </div>`;

        fetch(content)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultsContainer.innerHTML = `
                        <div class="p-4 bg-green-100 text-green-800 rounded-lg border border-green-200">
                            <strong>¡Éxito!</strong> ${data.message || 'Salida registrada.'}
                        </div>`;
                } else {
                    resultsContainer.innerHTML = `
                        <div class="p-4 bg-red-100 text-red-800 rounded-lg border border-red-200">
                            <strong>Error:</strong> ${data.message || 'No se pudo registrar la salida.'}
                        </div>`;
                }

                setTimeout(() => {
                    resultsContainer.innerHTML = '<div class="p-3 bg-blue-100 text-blue-800 rounded-lg">Listo para el siguiente escaneo.</div>';
                    lastScanTime = 0;
                }, 3000);
            })
            .catch(error => {
                console.error('Error en la solicitud fetch:', error);
                resultsContainer.innerHTML = `
                    <div class="p-4 bg-red-100 text-red-800 rounded-lg border border-red-200">
                        <strong>Error de conexión.</strong> No se pudo contactar al servidor.
                    </div>`;
                setTimeout(() => {
                     resultsContainer.innerHTML = '';
                     lastScanTime = 0;
                }, 3000);
            });
    });

    // --- LÓGICA MEJORADA PARA SELECCIONAR LA CÁMARA TRASERA ---
    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            // Por defecto, usa la primera cámara
            let selectedCamera = cameras[0]; 
            
            // Busca si existe una cámara "trasera" y la prefiere
            cameras.forEach(camera => {
                const cameraName = camera.name.toLowerCase();
                if (cameraName.includes('back') || cameraName.includes('rear') || cameraName.includes('trasera')) {
                    selectedCamera = camera;
                }
            });

            // Inicia el escáner con la cámara seleccionada
            scanner.start(selectedCamera);
            resultsContainer.innerHTML = `
                <div class="p-3 bg-blue-100 text-blue-800 rounded-lg">
                    Cámara activada. Apunte al código QR.
                </div>`;
        } else {
            console.error('No se encontraron cámaras.');
            resultsContainer.innerHTML = `
                <div class="p-4 bg-red-100 text-red-800 rounded-lg border border-red-200">
                    <strong>Error:</strong> No se encontraron cámaras en este dispositivo.
                </div>`;
        }
    }).catch(function (e) {
        console.error('Error al acceder a la cámara:', e);
        let errorMsg = 'No se pudo acceder a la cámara. Por favor, asegúrese de haber concedido los permisos en el navegador.';
        
        if (window.location.protocol !== 'https:') {
            errorMsg = '<strong>Error Crítico:</strong> El acceso a la cámara solo es posible a través de una conexión segura (HTTPS).';
        }

        resultsContainer.innerHTML = `
            <div class="p-4 bg-red-100 text-red-800 rounded-lg border border-red-200">
                ${errorMsg}
            </div>`;
    });
});
</script>

<?php 
include '../partials/footer.php'; 
?>