<?php
// READ del CRUD: obtiene todos los productos y los devuelve como JSON.
require_once __DIR__ . "/../conexion.php";
revisarMetodo("GET");

$consulta = mysqli_query(
    $conex,
    "SELECT id, nombre, descripcion, precio FROM productos ORDER BY id DESC"
);

if (!$consulta) {
    error_log("Error al listar productos: " . mysqli_error($conex));
    responder(500, false, "No se pudieron cargar los productos.");
}

$productos = [];
while ($producto = mysqli_fetch_assoc($consulta)) {
    $productos[] = $producto;
}

responder(200, true, "Productos encontrados.", $productos);
