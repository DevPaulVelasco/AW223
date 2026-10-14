<?php
require_once __DIR__ . '/conexion.php';

$resultado = $conexion->query(
    'SELECT id, nombre, correo, mensaje, timeStap
     FROM mensajes
     ORDER BY timeStap DESC'
);

if (!$resultado) {
    http_response_code(500);
    exit('No se pudieron consultar los mensajes: ' . htmlspecialchars($conexion->error, ENT_QUOTES, 'UTF-8'));
}

function escaparHtml($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Mi portafolio</title>
    <link rel="stylesheet" href="../Css/style.css">
</head>
<body>
    <header class="top-banner">
        <div class="banner-content">
            <p class="banner-tag">Desarrollo web</p>
            <h1>Administración</h1>
        </div>
    </header>

    <main class="admin-contenido">
        <a class="admin-regreso" href="index.html" aria-label="Regresar al portafolio">
            ← Volver al portafolio
        </a>

        <h2>Mensajes recibidos</h2>

        <div class="tabla-contenedor">
            <table class="tabla-mensajes">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mensaje = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= escaparHtml($mensaje['nombre']) ?></td>
                            <td><?= escaparHtml($mensaje['correo']) ?></td>
                            <td><?= nl2br(escaparHtml($mensaje['mensaje'])) ?></td>
                            <td><?= escaparHtml($mensaje['timeStap']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
