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

require 'conexion.php';

$stmt = $conexion->prepare(
    'INSERT INTO mensajes (nombre, correo, mensaje) VALUES (?, ?, ?)'
);
$stmt->bind_param('sss', $nombre, $email, $mensaje);

$estado = $stmt->execute() ? 'exito' : 'error';
$stmt->close();
$conexion->close();

header('Location: index.php?estado=' . $estado . '#contacto');
exit;
