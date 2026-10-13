<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if ($nombre === '' || $email === '' || $mensaje === '') {
    http_response_code(400);
    exit('Completa todos los campos.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Escribe un correo electrónico válido.');
}

require __DIR__ . '/conexion.php';

$sql = 'INSERT INTO mensajes (nombre, email, mensaje)
        VALUES (:nombre, :email, :mensaje)';

$consulta = $conexion->prepare($sql);
$consulta->execute([
    ':nombre' => $nombre,
    ':email' => $email,
    ':mensaje' => $mensaje,
]);

header('Location: index.html?enviado=1');
exit;