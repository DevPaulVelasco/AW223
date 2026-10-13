<?php

$host = 'localhost';
$baseDeDatos = 'portafolio_db';
$usuario = 'root';
$contrasena = '';

$dsn = "mysql:host=$host;dbname=$baseDeDatos;charset=utf8mb4";

$opciones = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$conexion = new PDO($dsn, $usuario, $contrasena, $opciones);