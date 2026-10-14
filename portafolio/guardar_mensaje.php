<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$nombre = trim($_POST['txtnombre'] ?? '');
$email = trim($_POST['txtemail'] ?? '');
$mensaje = trim($_POST['txtmensaje'] ?? '');

if ($nombre === '' || $email === '' || $mensaje === '') {
    http_response_code(400);
    exit('Completa todos los campos.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Escribe un correo electrónico válido.');
}

require_once __DIR__ . '/conexion.php';

$stmt = $conexion->prepare(
    'INSERT INTO mensajes (nombre, correo, mensaje) VALUES (?, ?, ?)'
);
$stmt->bind_param('sss', $nombre, $email, $mensaje);
$stmt->execute();
$stmt->close();
$conexion->close();

header('Location: index.html?enviado=1#contacto');
exit;
