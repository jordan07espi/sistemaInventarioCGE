<?php
// Archivo: view/partials/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../login.php');
    exit();
}

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
    <link rel="stylesheet" href="../assets/css/styles.css">
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
                <a href="dashboard.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Dashboard</a>
                <a href="productos.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Productos</a>
                <a href="espacios.php" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Espacios</a>
                <a href="#" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Reportes</a>
                <?php if ($rolUsuario === 'Administrador') : ?>
                    <a href="#" class="px-3 py-2 rounded hover:bg-gray-700 transition duration-300">Usuarios</a>
                <?php endif; ?>
            </nav>

            <!-- Info de Usuario y Logout -->
            <div class="flex items-center">
                <span class="mr-4">Hola, <?php echo htmlspecialchars($nombreUsuario); ?></span>
                <a href="../../controller/logout.php" class="bg-red-600 px-3 py-2 rounded hover:bg-red-700 transition duration-300" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Contenedor principal para el contenido de la página -->
    <main class="container mx-auto p-6">