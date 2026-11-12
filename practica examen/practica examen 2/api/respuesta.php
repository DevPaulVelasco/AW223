<?php
// Funciones pequeñas compartidas por los archivos que forman la API.
header("Content-Type: application/json; charset=utf-8");

// Envía una respuesta JSON y termina el archivo para evitar contenido extra.
function responder($codigo, $correcto, $mensaje, $datos = null)
{
    http_response_code($codigo);
    echo json_encode(
        ["ok" => $correcto, "mensaje" => $mensaje, "datos" => $datos],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// Lee los datos JSON que envía el navegador.
function leerDatos()
{
    $contenido = file_get_contents("php://input");
    $datos = json_decode($contenido, true);

    if (!is_array($datos)) {
        responder(400, false, "Los datos enviados no tienen un formato válido.");
    }

    return $datos;
}

// Revisa que el archivo se haya llamado con el método HTTP esperado.
function revisarMetodo($metodoEsperado)
{
    if ($_SERVER["REQUEST_METHOD"] !== $metodoEsperado) {
        header("Allow: " . $metodoEsperado);
        responder(405, false, "Método no permitido.");
    }
}
