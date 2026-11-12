<?php
// El carrito se guarda en la sesión, no en la tabla de productos.
session_start();
require_once __DIR__ . "/conexion.php";

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

// GET devuelve los detalles de los productos que están en el carrito.
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $articulos = [];
    $consulta = mysqli_prepare(
        $conex,
        "SELECT id, nombre, precio FROM productos WHERE id = ?"
    );

    foreach ($_SESSION["carrito"] as $id => $cantidad) {
        mysqli_stmt_bind_param($consulta, "i", $id);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        $producto = mysqli_fetch_assoc($resultado);

        // Si un producto fue eliminado del menú, se descarta de la sesión.
        if ($producto) {
            $producto["cantidad"] = $cantidad;
            $articulos[] = $producto;
        } else {
            unset($_SESSION["carrito"][$id]);
        }
    }

    responder(200, true, "Carrito cargado.", $articulos);
}

revisarMetodo("POST");
$datos = leerDatos();
$accion = $datos["accion"] ?? "";

if ($accion === "vaciar") {
    $_SESSION["carrito"] = [];
    responder(200, true, "Carrito vacío.");
}

$id = filter_var($datos["id"] ?? null, FILTER_VALIDATE_INT);
if (!$id || !in_array($accion, ["agregar", "quitar"], true)) {
    responder(400, false, "La acción o el producto no son válidos.");
}

if ($accion === "agregar") {
    // Comprueba que el producto siga existiendo antes de agregarlo.
    $consulta = mysqli_prepare($conex, "SELECT id FROM productos WHERE id = ?");
    mysqli_stmt_bind_param($consulta, "i", $id);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);

    if (!mysqli_fetch_assoc($resultado)) {
        responder(404, false, "El producto ya no existe en el menú.");
    }

    if (!isset($_SESSION["carrito"][$id])) {
        $_SESSION["carrito"][$id] = 0;
    }
    $_SESSION["carrito"][$id]++;
} else {
    // Quitar resta una unidad; cuando llega a cero, elimina el producto del carrito.
    if (isset($_SESSION["carrito"][$id])) {
        $_SESSION["carrito"][$id]--;
        if ($_SESSION["carrito"][$id] <= 0) {
            unset($_SESSION["carrito"][$id]);
        }
    }
}

responder(200, true, "Carrito actualizado.");
