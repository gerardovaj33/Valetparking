<?php

$servidor = "localhost";
$usuario = "root";
$contrasena = "12345678";
$base_datos = "valet_parking";

$conexion = new mysqli(
    $servidor,
    $usuario,
    $contrasena,
    $base_datos
);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>