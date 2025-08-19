<?php
// Archivo: model/EspacioDAO.php
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/dto/Espacio.php';

class EspacioDAO {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->getConnection();
    }

    public function listar() {
        $sql = "SELECT * FROM espacios WHERE activo = 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(Espacio $espacio) {
        $sql = "INSERT INTO espacios (nombre_espacio, piso, descripcion) VALUES (:nombre, :piso, :descripcion)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre', $espacio->nombre_espacio);
        $stmt->bindValue(':piso', $espacio->piso);
        $stmt->bindValue(':descripcion', $espacio->descripcion);
        return $stmt->execute();
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM espacios WHERE id_espacio = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar(Espacio $espacio) {
        $sql = "UPDATE espacios SET nombre_espacio = :nombre, piso = :piso, descripcion = :descripcion WHERE id_espacio = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':nombre', $espacio->nombre_espacio);
        $stmt->bindValue(':piso', $espacio->piso);
        $stmt->bindValue(':descripcion', $espacio->descripcion);
        $stmt->bindValue(':id', $espacio->id_espacio);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql = "UPDATE espacios SET activo = 0 WHERE id_espacio = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
?>