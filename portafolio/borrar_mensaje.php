<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('El identificador del mensaje no es válido.');
}

require __DIR__ . '/conexion.php';

$consulta = $conexion->prepare('DELETE FROM mensajes WHERE id = :id');
$consulta->execute([':id' => $id]);

header('Location: admin.php');
exit;