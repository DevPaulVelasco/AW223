<?php
// Esta página recibe una solicitud para eliminar un registro.
require_once __DIR__ . "/conexion.php";

// Solo acepta POST para que abrir directamente el enlace no borre datos.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Método de solicitud no válido.");
}

// El id oculto llega desde el formulario de eliminación de consultas.php.
$id = (int) ($_POST["id"] ?? 0);

// Detiene la operación si no hay un id válido.
if ($id <= 0) {
    die("El identificador no es válido.");
}

// Prepara el DELETE para borrar únicamente el registro con este id.
$consulta = mysqli_prepare($conex, "DELETE FROM datos WHERE id = ?");

if ($consulta === false) {
    die("No se pudo preparar la eliminación: " . mysqli_error($conex));
}

// "i" indica que id es un número entero.
mysqli_stmt_bind_param($consulta, "i", $id);

// Ejecuta la eliminación y muestra el error si MySQL no pudo completarla.
if (!mysqli_stmt_execute($consulta)) {
    die("No se pudo eliminar la consulta: " . mysqli_stmt_error($consulta));
}

// Libera la consulta y vuelve al listado con un indicador de éxito.
mysqli_stmt_close($consulta);
header("Location: consultas.php?eliminado=1");
exit;
?>
