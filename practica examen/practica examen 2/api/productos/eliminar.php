<?php
// DELETE del CRUD: elimina un producto usando su id.
require_once __DIR__ . "/../conexion.php";
revisarMetodo("POST");
$datos = leerDatos();

$id = filter_var($datos["id"] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    responder(400, false, "El id del producto no es válido.");
}

$consulta = mysqli_prepare($conex, "DELETE FROM productos WHERE id = ?");
mysqli_stmt_bind_param($consulta, "i", $id);

if (!mysqli_stmt_execute($consulta)) {
    error_log("Error al eliminar producto: " . mysqli_stmt_error($consulta));
    responder(500, false, "No se pudo eliminar el producto.");
}

if (mysqli_stmt_affected_rows($consulta) === 0) {
    responder(404, false, "No se encontró el producto.");
}

responder(200, true, "Producto eliminado correctamente.");
