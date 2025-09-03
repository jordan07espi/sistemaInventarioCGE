<?php
// Archivo: view/partials/header.php

// 1. INICIO DE SESIÓN SEGURO
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. CONTROL DE CACHÉ DEL NAVEGADOR
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// 3. VERIFICACIÓN DE SESIÓN Y TIEMPO DE INACTIVIDAD
$tiempo_limite_inactividad = 30 * 60; // 30 minutos

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../login.php?error=no_session');
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $tiempo_limite_inactividad)) {
    session_unset();
    session_destroy();
    header('Location: ../../login.php?error=inactive');
    exit();
}

$_SESSION['last_activity'] = time();

$nombreUsuario = $_SESSION['nombre_completo'] ?? 'Usuario';
$rolUsuario = $_SESSION['rol'] ?? 'Invitado';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JoseSoft - Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <header class="bg-gray-800 text-white shadow-lg">
        <div class="container mx-auto flex items-center justify-between p-4">
            <div class="flex items-center">
                <h1 class="text-2xl font-bold">JoseSoft</h1>
            </div>

            <nav class="hidden md:flex space-x-4">
                <?php if ($rolUsuario === 'Administrador' || $rolUsuario === 'Supervisor') : ?>
                    <a href="dashboard.php" class="px-3 py-2 rounded hover:bg-gray-700">Dashboard</a>
                    <a href="movimientos.php" class="px-3 py-2 rounded hover:bg-gray-700">Movimientos</a>
                    <a href="productos.php" class="px-3 py-2 rounded hover:bg-gray-700">Productos</a>
                    <a href="espacios.php" class="px-3 py-2 rounded hover:bg-gray-700">Espacios</a>
                    <a href="qr_generator.php" class="px-3 py-2 rounded hover:bg-gray-700">Generar QR</a>
                <?php endif; ?>
                <a href="scanner.php" class="px-3 py-2 rounded hover:bg-gray-700">Escáner</a>
                <?php if ($rolUsuario !== 'Invitado') : ?>
                    <a href="reportes.php" class="px-3 py-2 rounded hover:bg-gray-700">Reportes</a>
                <?php endif; ?>
                <?php if ($rolUsuario === 'Administrador') : ?>
                    <a href="usuarios.php" class="px-3 py-2 rounded hover:bg-gray-700">Usuarios</a>
                <?php endif; ?>
            </nav>

            <div class="flex items-center space-x-4">
                <span class="hidden sm:inline">Hola, <?php echo htmlspecialchars($nombreUsuario); ?></span>
                <div class="relative" id="alertas-container">
                    <button id="btnAlertas" class="text-white hover:text-gray-300">
                        <i class="fas fa-bell fa-lg"></i>
                        <span id="alerta-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </button>
                    <div id="alertas-dropdown" class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg z-20 hidden">
                        <div class="p-2 border-b"><h4 class="font-bold text-gray-800">Alertas de Stock Bajo</h4></div>
                        <ul id="lista-alertas" class="max-h-64 overflow-y-auto"></ul>
                    </div>
                </div>

                <a href="../../controller/logout.php" class="bg-red-600 px-3 py-2 rounded hover:bg-red-700" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
                
                <button id="btnMenuMovil" class="md:hidden text-white hover:text-gray-300">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
        </div>

        <div id="menuMovil" class="hidden md:hidden bg-gray-700">
            <?php if ($rolUsuario === 'Administrador' || $rolUsuario === 'Supervisor') : ?>
            <a href="dashboard.php" class="block px-4 py-2 text-white hover:bg-gray-600">Dashboard</a>
            <a href="movimientos.php" class="block px-4 py-2 text-white hover:bg-gray-600">Movimientos</a>
            <a href="productos.php" class="block px-4 py-2 text-white hover:bg-gray-600">Productos</a>
            <a href="espacios.php" class="block px-4 py-2 text-white hover:bg-gray-600">Espacios</a>
            <a href="qr_generator.php" class="block px-4 py-2 text-white hover:bg-gray-600">Generar QR</a>
            <?php endif; ?>
            <a href="scanner.php" class="block px-4 py-2 text-white hover:bg-gray-600">Escáner</a>
            <?php if ($rolUsuario !== 'Invitado') : ?>
            <a href="reportes.php" class="block px-4 py-2 text-white hover:bg-gray-600">Reportes</a>
            <?php endif; ?>
            <?php if ($rolUsuario === 'Administrador') : ?>
            <a href="usuarios.php" class="block px-4 py-2 text-white hover:bg-gray-600">Usuarios</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container mx-auto p-6 flex-grow">