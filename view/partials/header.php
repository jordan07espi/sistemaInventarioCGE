<?php
// Archivo: view/partials/header.php

// 1. INICIO DE SESIÓN SEGURO
// Asegura que la sesión siempre se inicie.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. CONTROL DE CACHÉ DEL NAVEGADOR
// Estas cabeceras le dicen al navegador que no guarde la página en caché.
// Esto soluciona el problema de poder ver páginas privadas con el botón "Atrás" después de cerrar sesión.
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

// 3. VERIFICACIÓN DE SESIÓN Y TIEMPO DE INACTIVIDAD
$tiempo_limite_inactividad = 30 * 60; // 30 minutos

// Primero, verificamos si el usuario está logueado.
if (!isset($_SESSION['id_usuario'])) {
    // Si no hay sesión, lo redirigimos al login.
    header('Location: ../../login.php?error=no_session');
    exit();
}

// Si está logueado, verificamos su actividad.
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $tiempo_limite_inactividad)) {
    // Si ha pasado demasiado tiempo inactivo, destruimos la sesión.
    session_unset();
    session_destroy();
    header('Location: ../../login.php?error=inactive'); // Redirigir con un mensaje
    exit();
}

// Si sigue activo, actualizamos la marca de tiempo de su última actividad.
$_SESSION['last_activity'] = time();

// El resto de tu código para obtener variables de sesión...
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

    <!-- Header / Navbar -->
    <header class="bg-gray-800 text-white shadow-lg">
        <div class="container mx-auto flex items-center justify-between p-4">
            <!-- Logo/Marca -->
            <div class="flex items-center">
                
                <h1 class="text-2xl font-bold">JoseSoft</h1>
            </div>

            <!-- Menú de Navegación -->
            <nav class="hidden md:flex space-x-4">
                <?php if ($rolUsuario === 'Administrador' || $rolUsuario === 'Supervisor') : ?>
                    <a href="dashboard.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Dashboard</a>
                    <a href="movimientos.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Movimientos</a>
                    <a href="productos.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Productos</a>
                    <a href="espacios.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Espacios</a>
                <?php endif; ?>

                <?php if ($rolUsuario === 'Administrador' || $rolUsuario === 'Supervisor' || $rolUsuario === 'CEO') : ?>
                    <a href="reportes.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Reportes</a>
                <?php endif; ?>

                <?php if ($rolUsuario === 'Administrador') : ?>
                    <a href="usuarios.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Usuarios</a>
                <?php endif; ?>
            </nav>

            <div class="flex items-center space-x-4">
                <span class="hidden sm:inline">Hola, <?php echo htmlspecialchars($nombreUsuario); ?></span>

                <div class="relative" id="alertas-container">
                    <button id="btnAlertas" class="text-white hover:text-gray-300">
                        <i class="fas fa-bell fa-lg"></i>
                        <span id="alerta-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">
                            0
                        </span>
                    </button>
                    <div id="alertas-dropdown" class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg z-20 hidden">
                        <div class="p-2 border-b">
                            <h4 class="font-bold text-gray-800">Alertas de Stock Bajo</h4>
                        </div>
                        <ul id="lista-alertas" class="max-h-64 overflow-y-auto">
                            </ul>
                    </div>
                </div>

                <a href="../../controller/logout.php" class="bg-red-600 px-3 py-2 rounded hover:bg-red-700 transition duration-300" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Contenedor principal para el contenido de la página -->
    <main class="container mx-auto p-6">