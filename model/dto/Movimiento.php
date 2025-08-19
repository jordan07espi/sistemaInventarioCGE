<?php
// Archivo: model/dto/Movimiento.php
class Movimiento {
    public $id_movimiento;
    public $id_producto;
    public $id_espacio;
    public $id_usuario;
    public $tipo_movimiento;
    public $cantidad;
    public $jornada; 
    public $fecha_movimiento;
    public $descripcion;
    public $es_correccion = 0;
}
?>