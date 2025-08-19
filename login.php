<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    header('Location: view/admin/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Login - Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-sm p-8 bg-white rounded-lg shadow-md">
        <div class="flex justify-center mb-6">
            <img src="view/assets/img/logo.png" alt="Logo" class="h-16" onerror="this.style.display='none'">
        </div>
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Control de Inventario</h2>
        
        <form id="loginForm" method="POST">
            <div class="mb-4">
                <!-- CAMBIO: Label y input para Cédula -->
                <label for="cedula" class="block text-gray-600 mb-2">Cédula</label>
                <input type="text" id="cedula" name="cedula" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-600 mb-2">Contraseña</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div id="errorMessage" class="text-red-500 text-sm text-center mb-4"></div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                Ingresar
            </button>
        </form>
    </div>

    <script src="view/assets/js/login.js"></script>
</body>
</html>