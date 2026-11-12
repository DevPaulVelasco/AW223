<?php
// UPDATE del CRUD: modifica un producto que ya existe.
require_once __DIR__ . "/../conexion.php";
revisarMetodo("POST");
$datos = leerDatos();

$id = filter_var($datos["id"] ?? null, FILTER_VALIDATE_INT);
$nombreRecibido = $datos["nombre"] ?? null;
$descripcionRecibida = $datos["descripcion"] ?? "";
$precio = $datos["precio"] ?? null;

if (
    !$id
    || !is_string($nombreRecibido)
    || !is_string($descripcionRecibida)
    || trim($nombreRecibido) === ""
    || !is_numeric($precio)
    || (float) $precio <= 0
) {
    responder(400, false, "Revisa el id, el nombre y el precio del producto.");
}

$nombre = trim($nombreRecibido);
$descripcion = trim($descripcionRecibida);

$consulta = mysqli_prepare(
    $conex,
    "UPDATE productos SET nombre = ?, descripcion = ?, precio = ? WHERE id = ?"
);
mysqli_stmt_bind_param($consulta, "ssdi", $nombre, $descripcion, $precio, $id);

if (!mysqli_stmt_execute($consulta)) {
    error_log("Error al actualizar producto: " . mysqli_stmt_error($consulta));
    responder(500, false, "No se pudo actualizar el producto.");
}

if (mysqli_stmt_affected_rows($consulta) === 0) {
    // MySQL también reporta cero cambios si se guardan exactamente los mismos datos.
    $comprobar = mysqli_prepare($conex, "SELECT id FROM productos WHERE id = ?");
    mysqli_stmt_bind_param($comprobar, "i", $id);
    mysqli_stmt_execute($comprobar);
    $resultado = mysqli_stmt_get_result($comprobar);

    if (!mysqli_fetch_assoc($resultado)) {
        responder(404, false, "No se encontró el producto.");
    }
}

responder(200, true, "Producto actualizado correctamente.");
