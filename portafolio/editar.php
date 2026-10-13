<?php
function escapar($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (!$id || $id < 1 || $nombre === '' || $email === '' || $mensaje === '') {
        http_response_code(400);
        exit('Completa correctamente todos los campos.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        exit('Escribe un correo electrónico válido.');
    }

    require __DIR__ . '/conexion.php';

    $consulta = $conexion->prepare(
        'UPDATE mensajes
         SET nombre = :nombre, email = :email, mensaje = :mensaje
         WHERE id = :id'
    );

    $consulta->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':mensaje' => $mensaje,
        ':id' => $id,
    ]);

    header('Location: admin.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('El identificador del mensaje no es válido.');
}

require __DIR__ . '/conexion.php';

$consulta = $conexion->prepare(
    'SELECT id, nombre, email, mensaje
     FROM mensajes
     WHERE id = :id'
);
$consulta->execute([':id' => $id]);
$mensajeActual = $consulta->fetch();

if (!$mensajeActual) {
    http_response_code(404);
    exit('No se encontró el mensaje solicitado.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar mensaje | Mi portafolio</title>
    <link rel="stylesheet" href="../Css/style.css">
    <style>
        .editar-contenido {
            max-width: 800px;
            margin: 0 auto;
        }

        .editar-regresos {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin: 10px 0 24px;
        }

        .editar-regresos a {
            color: #1c91f1;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header class="top-banner">
        <div class="banner-content">
            <p class="banner-tag">Desarrollo web</p>
            <h1>Editar mensaje</h1>
        </div>
    </header>

    <main class="editar-contenido">
        <div class="editar-regresos">
            <a href="index.html">← Volver al portafolio</a>
            <a href="admin.php">← Volver a administración</a>
        </div>

        <h2>Actualizar mensaje</h2>

        <form class="contact-form" action="editar.php" method="post">
            <input
                type="hidden"
                name="id"
                value="<?= (int) $mensajeActual['id'] ?>"
            >

            <div class="campo">
                <label for="nombre">Nombre</label>
                <input
                    id="nombre"
                    name="nombre"
                    type="text"
                    value="<?= escapar($mensajeActual['nombre']) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label for="email">Correo electrónico</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="<?= escapar($mensajeActual['email']) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label for="mensaje">Mensaje</label>
                <textarea
                    id="mensaje"
                    name="mensaje"
                    rows="6"
                    required
                ><?= escapar($mensajeActual['mensaje']) ?></textarea>
            </div>

            <button type="submit">Guardar cambios</button>
        </form>
    </main>
</body>
</html>