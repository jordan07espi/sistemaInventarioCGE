<?php
// Archivo: controller/ProductoController.php
require_once '../model/ProductoDAO.php';
require_once '../model/dto/Producto.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Acción no reconocida.'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$productoDAO = new ProductoDAO();

switch ($action) {
    case 'listar':
        $response['success'] = true;
        $response['data'] = $productoDAO->listar();
        break;

    case 'agregar':
        $producto = new Producto();
        $producto->nombre_producto = $_POST['nombre_producto'];
        $producto->unidad_medida = $_POST['unidad_medida'];
        $producto->stock_actual = $_POST['stock_actual'] ?? 0;
        $producto->stock_minimo = $_POST['stock_minimo'];

        if ($productoDAO->agregar($producto)) {
            $response['success'] = true;
            $response['message'] = 'Producto agregado exitosamente.';
        } else {
            $response['message'] = 'Error al agregar el producto.';
        }
        break;
        
    case 'obtener':
        $id = $_POST['id_producto'];
        $data = $productoDAO->obtenerPorId($id);
        if ($data) {
            $response['success'] = true;
            $response['data'] = $data;
        } else {
            $response['message'] = 'Producto no encontrado.';
        }
        break;

    case 'actualizar':
        $producto = new Producto();
        $producto->id_producto = $_POST['id_producto'];
        $producto->nombre_producto = $_POST['nombre_producto'];
        $producto->unidad_medida = $_POST['unidad_medida'];
        $producto->stock_minimo = $_POST['stock_minimo'];

        if ($productoDAO->actualizar($producto)) {
            $response['success'] = true;
            $response['message'] = 'Producto actualizado exitosamente.';
        } else {
            $response['message'] = 'Error al actualizar el producto.';
        }
        break;

    case 'eliminar':
        $id = $_POST['id_producto'];
        if ($productoDAO->eliminar($id)) {
            $response['success'] = true;
            $response['message'] = 'Producto eliminado exitosamente.';
        } else {
            $response['message'] = 'Error al eliminar el producto.';
        }
        break;
}

echo json_encode($response);
?>