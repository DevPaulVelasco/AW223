<?php
require 'conexion.php';
require 'consulta_mensajes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = 0;
    if (isset($_POST['id'])) {
        $id = (int) $_POST['id'];
    }

    $accion = '';
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
    }

    $estado = 'error';

    if ($accion === 'borrar') {
        $consulta = $conexion->prepare('DELETE FROM mensajes WHERE id = ?');
        $consulta->bind_param('i', $id);
        if ($consulta->execute()) {
            $estado = 'borrado';
        }
        $consulta->close();
    } elseif ($accion === 'guardar') {
        $nombre = '';
        $correo = '';
        $mensaje = '';

        if (isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
        }
        if (isset($_POST['correo'])) {
            $correo = trim($_POST['correo']);
        }
        if (isset($_POST['mensaje'])) {
            $mensaje = trim($_POST['mensaje']);
        }

        if ($nombre === '' || $correo === '' || $mensaje === '') {
            header('Location: admin.php?editar=' . $id . '&estado=vacios');
            exit;
        }

        $consulta = $conexion->prepare(
            'UPDATE mensajes SET nombre = ?, correo = ?, mensaje = ? WHERE id = ?'
        );
        $consulta->bind_param('sssi', $nombre, $correo, $mensaje, $id);
        if ($consulta->execute()) {
            $estado = 'actualizado';
        }
        $consulta->close();
    }

    header('Location: admin.php?estado=' . $estado);
    exit;
}

$resultado = consultaMensajes();
$editarId = 0;
if (isset($_GET['editar'])) {
    $editarId = (int) $_GET['editar'];
}

$mensajeEditar = false;

if ($editarId > 0) {
    $consultaEditar = $conexion->prepare('SELECT id, nombre, correo, mensaje, timeStap FROM mensajes WHERE id = ?');
    $consultaEditar->bind_param('i', $editarId);
    $consultaEditar->execute();
    $resultadoEditar = $consultaEditar->get_result();
    $mensajeEditar = $resultadoEditar->fetch_assoc();
    $consultaEditar->close();

    if (!$mensajeEditar) {
        header('Location: admin.php');
        exit;
    }
}

$hayMensajes = false;
if (mysqli_num_rows($resultado) > 0) {
    $hayMensajes = true;
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
    <?php
    $estadoAviso = '';
    if (isset($_GET['estado'])) {
        $estadoAviso = $_GET['estado'];
    }

    if ($estadoAviso === 'actualizado') {
        echo '<script>alert("Mensaje actualizado correctamente.");</script>';
    } elseif ($estadoAviso === 'borrado') {
        echo '<script>alert("Mensaje borrado correctamente.");</script>';
    } elseif ($estadoAviso === 'error') {
        echo '<script>alert("Ocurrió un error al guardar los cambios.");</script>';
    } elseif ($estadoAviso === 'vacios') {
        echo '<script>alert("No se permiten campos vacíos.");</script>';
    }
    ?>
    <header class="top-banner">
        <div class="banner-content">
            <p class="banner-tag">Desarrollo web</p>
            <h1>Administración</h1>
        </div>
    </header>

    <main class="admin-contenido">
        <?php if ($mensajeEditar) { ?>
            <div class="admin-top-actions">
                <a class="admin-btn-home" href="index.php">Home</a>
                <a class="admin-regreso" href="admin.php" aria-label="Regresar">←</a>
            </div>

            <div class="admin-editar-panel">
                <form class="admin-editar-form" method="post">
                    <input type="hidden" name="accion" value="guardar">
                    <input type="hidden" name="id" value="<?php echo (int) $mensajeEditar['id']; ?>">

                    <div>
                        <label for="nombre">Nombre</label>
                        <input id="nombre" name="nombre" type="text" value="<?php echo htmlspecialchars($mensajeEditar['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div>
                        <label for="correo">Correo</label>
                        <input id="correo" name="correo" type="email" value="<?php echo htmlspecialchars($mensajeEditar['correo'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <div>
                        <label for="mensaje">Mensaje</label>
                        <textarea id="mensaje" name="mensaje" required><?php echo htmlspecialchars($mensajeEditar['mensaje'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="admin-acciones-editar">
                        <button class="admin-boton-guardar" type="submit">Guardar</button>
                    </div>
                </form>
            </div>
        <?php } else { ?>
            <div class="admin-intro">
                <div>
                    <a class="admin-regreso" href="index.php" aria-label="Regresar">←</a>
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
                        <?php if ($hayMensajes === false) { ?>
                            <tr>
                                <td class="admin-vacio" colspan="5">Todavía no hay mensajes.</td>
                            </tr>
                        <?php } ?>
                        <?php while ($mensaje = mysqli_fetch_assoc($resultado)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mensaje['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($mensaje['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($mensaje['mensaje'], ENT_QUOTES, 'UTF-8')); ?></td>
                                <td><?php echo htmlspecialchars($mensaje['timeStap'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="admin-acciones">
                                        <form method="post" onsubmit="return confirm('¿Estás seguro de que deseas borrar este mensaje?');">
                                            <input type="hidden" name="accion" value="borrar">
                                            <input type="hidden" name="id" value="<?php echo (int) $mensaje['id']; ?>">
                                            <button class="admin-boton-borrar" type="submit">Borrar</button>
                                        </form>
                                        <a class="admin-btn-home" href="admin.php?editar=<?php echo (int) $mensaje['id']; ?>">Editar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </main>
</body>
</html>
