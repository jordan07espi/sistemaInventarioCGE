<?php
// Archivo: controller/EspacioController.php
require_once '../model/EspacioDAO.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no reconocida.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$espacioDAO = new EspacioDAO();

switch ($action) {
    case 'listar':
        $response['success'] = true;
        $response['data'] = $espacioDAO->listar();
        break;

    case 'agregar':
        $espacio = new Espacio();
        $espacio->nombre_espacio = $_POST['nombre_espacio'];
        $espacio->piso = $_POST['piso'];
        $espacio->descripcion = $_POST['descripcion'];

        if ($espacioDAO->agregar($espacio)) {
            $response['success'] = true;
            $response['message'] = 'Espacio agregado exitosamente.';
        } else {
            $response['message'] = 'Error al agregar el espacio.';
        }
        break;
        
    case 'obtener':
        $id = $_POST['id_espacio'];
        $data = $espacioDAO->obtenerPorId($id);
        if ($data) {
            $response['success'] = true;
            $response['data'] = $data;
        } else {
            $response['message'] = 'Espacio no encontrado.';
        }
        break;

    case 'actualizar':
        $espacio = new Espacio();
        $espacio->id_espacio = $_POST['id_espacio'];
        $espacio->nombre_espacio = $_POST['nombre_espacio'];
        $espacio->piso = $_POST['piso'];
        $espacio->descripcion = $_POST['descripcion'];

        if ($espacioDAO->actualizar($espacio)) {
            $response['success'] = true;
            $response['message'] = 'Espacio actualizado exitosamente.';
        } else {
            $response['message'] = 'Error al actualizar el espacio.';
        }
        break;

    case 'eliminar':
        $id = $_POST['id_espacio'];
        if ($espacioDAO->eliminar($id)) {
            $response['success'] = true;
            $response['message'] = 'Espacio eliminado exitosamente.';
        } else {
            $response['message'] = 'Error al eliminar el espacio.';
        }
        break;
}

echo json_encode($response);
?>