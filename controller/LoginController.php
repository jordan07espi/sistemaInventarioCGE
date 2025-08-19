<?php
// Archivo: controller/LoginController.php

session_start();
require_once '../model/UsuarioDAO.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Método no permitido.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = trim($_POST['cedula'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($cedula) || empty($password)) {
        $response['message'] = 'Cédula y contraseña son requeridos.';
        echo json_encode($response);
        exit();
    }
    
    $usuarioDAO = new UsuarioDAO();
    $usuario = $usuarioDAO->obtenerUsuarioPorCedula($cedula);
    
    if ($usuario && password_verify($password, $usuario->password)) {
        $_SESSION['id_usuario'] = $usuario->id_usuario;
        $_SESSION['nombre_completo'] = $usuario->nombre_completo;
        $_SESSION['cedula'] = $usuario->cedula;
        $_SESSION['rol'] = $usuario->nombre_rol;
        $response['redirect'] = 'view/admin/dashboard.php'; 
        $response['success'] = true;
        $response['message'] = 'Inicio de sesión exitoso.';
        
    } else {
        $response['message'] = 'Cédula o contraseña incorrectos.';
    }
}

echo json_encode($response);
?>