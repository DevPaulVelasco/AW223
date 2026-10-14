<?php
$servidor = 'localhost';
$usuario = 'root';
$contrasena = '';
$baseDatos = 'mensajes';

$conexion = new mysqli($servidor, $usuario, $contrasena, $baseDatos);

if ($conexion->connect_errno) {
    http_response_code(500);
    exit('No se pudo conectar a la base de datos: ' . htmlspecialchars($conexion->connect_error, ENT_QUOTES, 'UTF-8'));
}

$conexion->set_charset('utf8mb4');