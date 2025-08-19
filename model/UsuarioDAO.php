<?php
// Archivo: model/UsuarioDAO.php

require_once __DIR__ . '/../config/Conexion.php';

class UsuarioDAO {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConnection();
    }

    public function obtenerUsuarioPorCedula($cedula) {
        // CAMBIO: Se elimina 'email' de la consulta
        $sql = "SELECT u.id_usuario, u.nombre_completo, u.cedula, u.password, u.id_rol, u.activo, r.nombre_rol 
                FROM usuarios u 
                JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.cedula = :cedula AND u.activo = 1";
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::obtenerUsuarioPorCedula: " . $e->getMessage());
            return null;
        }
    }
}
?>