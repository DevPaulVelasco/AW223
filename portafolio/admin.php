<?php
require __DIR__ . '/conexion.php';

$consulta = $conexion->query(
    'SELECT id, nombre, email, mensaje, fecha
     FROM mensajes
     ORDER BY fecha DESC'
);

$mensajes = $consulta->fetchAll();

function escapar($valor)
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
    <style>
        .admin-contenido {
            max-width: 1200px;
            margin: 0 auto;
        }

        .admin-regreso {
            display: inline-block;
            margin: 10px 0 20px;
            color: #1c91f1;
            font-weight: bold;
            text-decoration: none;
        }

        .tabla-contenedor {
            overflow-x: auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }

        .tabla-mensajes {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla-mensajes th,
        .tabla-mensajes td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            vertical-align: top;
        }

        .tabla-mensajes th {
            background-color: #1c91f1;
            color: #ffffff;
        }

        .acciones {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .icono-accion {
            padding: 8px 10px;
            border: 0;
            border-radius: 5px;
            color: #ffffff;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
        }

        .icono-editar {
            background-color: #0f7ad8;
        }

        .icono-borrar {
            background-color: #c9302c;
        }

        .sin-mensajes {
            padding: 20px;
            text-align: center;
        }
    </style>
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

        <?php if (count($mensajes) > 0): ?>
            <div class="tabla-contenedor">
                <table class="tabla-mensajes">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mensajes as $mensaje): ?>
                            <tr>
                                <td><?= escapar($mensaje['nombre']) ?></td>
                                <td><?= escapar($mensaje['email']) ?></td>
                                <td><?= nl2br(escapar($mensaje['mensaje'])) ?></td>
                                <td><?= escapar($mensaje['fecha']) ?></td>
                                <td>
                                    <div class="acciones">
                                        <a
                                            class="icono-accion icono-editar"
                                            href="editar.php?id=<?= (int) $mensaje['id'] ?>"
                                            aria-label="Editar mensaje"
                                            title="Editar"
                                        >Editar</a>

                                        <form action="borrar_mensaje.php" method="post">
                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $mensaje['id'] ?>"
                                            >
                                            <button
                                                class="icono-accion icono-borrar"
                                                type="submit"
                                                aria-label="Borrar mensaje"
                                                title="Borrar"
                                            >Borrar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="sin-mensajes">Todavía no hay mensajes recibidos.</p>
        <?php endif; ?>
    </main>
</body>
</html>