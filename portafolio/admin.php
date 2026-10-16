<?php
require 'conexion.php';
require 'consulta_mensajes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);

    if (($_POST['accion'] ?? '') === 'borrar') {
        $consulta = $conexion->prepare('DELETE FROM mensajes WHERE id = ?');
        $consulta->bind_param('i', $id);
        $consulta->execute();
        $consulta->close();
    } elseif (($_POST['accion'] ?? '') === 'guardar') {
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        $consulta = $conexion->prepare(
            'UPDATE mensajes SET nombre = ?, correo = ?, mensaje = ? WHERE id = ?'
        );
        $consulta->bind_param('sssi', $nombre, $correo, $mensaje, $id);
        $consulta->execute();
        $consulta->close();
    }

    header('Location: admin.php');
    exit;
}

$resultado = consultaMensajes();
$editarId = (int) ($_GET['editar'] ?? 0);
$hayMensajes = mysqli_num_rows($resultado) > 0;
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
        <div class="admin-intro">
            <div>
                <a class="admin-regreso" href="index.php">← Volver al portafolio</a>
                <h2>Mensajes recibidos</h2>
                <p>Consulta y administra los mensajes enviados desde el formulario de contacto.</p>
            </div>
        </div>

        <div class="tabla-contenedor">
            <table class="tabla-mensajes">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$hayMensajes): ?>
                        <tr>
                            <td class="admin-vacio" colspan="5">Todavía no hay mensajes.</td>
                        </tr>
                    <?php endif; ?>
                    <?php while ($mensaje = mysqli_fetch_assoc($resultado)): ?>
                        <?php if ($editarId === (int) $mensaje['id']): ?>
                            <tr>
                                <td><input form="editar-mensaje-<?= (int) $mensaje['id'] ?>" name="nombre" value="<?= htmlspecialchars($mensaje['nombre'], ENT_QUOTES, 'UTF-8') ?>" required></td>
                                <td><input form="editar-mensaje-<?= (int) $mensaje['id'] ?>" name="correo" type="email" value="<?= htmlspecialchars($mensaje['correo'], ENT_QUOTES, 'UTF-8') ?>" required></td>
                                <td><textarea form="editar-mensaje-<?= (int) $mensaje['id'] ?>" name="mensaje" required><?= htmlspecialchars($mensaje['mensaje'], ENT_QUOTES, 'UTF-8') ?></textarea></td>
                                <td><?= htmlspecialchars($mensaje['timeStap'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <form class="admin-acciones" id="editar-mensaje-<?= (int) $mensaje['id'] ?>" method="post">
                                        <input type="hidden" name="accion" value="guardar">
                                        <input type="hidden" name="id" value="<?= (int) $mensaje['id'] ?>">
                                        <button class="admin-boton-guardar" type="submit">Guardar</button>
                                    </form>
                                    <a class="admin-cancelar" href="admin.php">Cancelar</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><?= htmlspecialchars($mensaje['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($mensaje['correo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= nl2br(htmlspecialchars($mensaje['mensaje'], ENT_QUOTES, 'UTF-8')) ?></td>
                                <td><?= htmlspecialchars($mensaje['timeStap'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div class="admin-acciones">
                                    <form method="post">
                                        <input type="hidden" name="accion" value="borrar">
                                        <input type="hidden" name="id" value="<?= (int) $mensaje['id'] ?>">
                                        <button class="admin-boton-borrar" type="submit">Borrar</button>
                                    </form>
                                    <form method="get">
                                        <input type="hidden" name="editar" value="<?= (int) $mensaje['id'] ?>">
                                        <button type="submit">Editar</button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
