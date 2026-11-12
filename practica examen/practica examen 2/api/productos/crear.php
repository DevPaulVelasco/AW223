<?php
// CREATE del CRUD: comprueba los datos y agrega un producto.
require_once __DIR__ . "/../conexion.php";
revisarMetodo("POST");
$datos = leerDatos();

$nombreRecibido = $datos["nombre"] ?? null;
$descripcionRecibida = $datos["descripcion"] ?? "";
$precio = $datos["precio"] ?? null;

if (
    !is_string($nombreRecibido)
    || !is_string($descripcionRecibida)
    || trim($nombreRecibido) === ""
    || !is_numeric($precio)
    || (float) $precio <= 0
) {
    responder(400, false, "Escribe un nombre y un precio mayor que cero.");
}

$nombre = trim($nombreRecibido);
$descripcion = trim($descripcionRecibida);

$consulta = mysqli_prepare(
    $conex,
    "INSERT INTO productos (nombre, descripcion, precio) VALUES (?, ?, ?)"
);
mysqli_stmt_bind_param($consulta, "ssd", $nombre, $descripcion, $precio);

if (!mysqli_stmt_execute($consulta)) {
    error_log("Error al crear producto: " . mysqli_stmt_error($consulta));
    responder(500, false, "No se pudo agregar el producto.");
}

responder(201, true, "Producto agregado correctamente.");
