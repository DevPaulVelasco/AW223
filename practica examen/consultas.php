<?php
// Esta página se encarga de leer y mostrar los registros guardados.
// __DIR__ indica la carpeta de este archivo; require_once evita incluir la conexión dos veces.
require_once __DIR__ . "/conexion.php";

// SELECT obtiene las columnas necesarias y muestra primero los registros más nuevos.
$registros = mysqli_query(
    $conex,
    "SELECT id, nombre, telefono, email, mensaje FROM datos ORDER BY id DESC"
);

// Si la consulta falla, se muestra el error en vez de presentar una lista vacía como si todo funcionara.
if ($registros === false) {
    die("No se pudieron leer las consultas: " . mysqli_error($conex));
}

// Estos mensajes aparecen después de regresar de editar.php o eliminar.php.
$mensaje = "";
if (isset($_GET["actualizado"])) {
    $mensaje = "La consulta se actualizó correctamente.";
} elseif (isset($_GET["eliminado"])) {
    $mensaje = "La consulta se eliminó correctamente.";
}
?>
<!-- Estructura HTML normal: el navegador muestra el contenido que sigue. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración básica para caracteres y pantallas pequeñas. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Se reutilizan los estilos generales del sitio. -->
    <link rel="stylesheet" href="style.css">
    <title>Consultas registradas</title>
</head>
<body>
    <main class="tabla-registros container">
        <h2>Consultas registradas</h2>

        <!-- PHP muestra el mensaje si se editó o eliminó un registro. -->
        <?php if ($mensaje !== ""): ?>
            <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8"); ?></p>
        <?php endif; ?>

        <!-- mysqli_num_rows cuenta los registros devueltos por SELECT. -->
        <?php if (mysqli_num_rows($registros) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Mensaje</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- El ciclo while crea una fila HTML por cada registro. -->
                    <?php while ($fila = mysqli_fetch_assoc($registros)): ?>
                        <tr>
                            <!-- htmlspecialchars muestra los datos como texto y evita interpretar HTML ingresado. -->
                            <td><?php echo htmlspecialchars($fila["nombre"], ENT_QUOTES, "UTF-8"); ?></td>
                            <td><?php echo htmlspecialchars($fila["telefono"], ENT_QUOTES, "UTF-8"); ?></td>
                            <td><?php echo htmlspecialchars($fila["email"], ENT_QUOTES, "UTF-8"); ?></td>
                            <td><?php echo htmlspecialchars($fila["mensaje"], ENT_QUOTES, "UTF-8"); ?></td>
                            <td class="acciones">
                                <!-- El id indica a editar.php qué registro debe cargar. -->
                                <a href="editar.php?id=<?php echo (int) $fila["id"]; ?>">Editar</a>

                                <!-- El id viaja por POST; el navegador pide confirmación antes de borrar. -->
                                <form action="eliminar.php" method="post" onsubmit="return confirm('¿Seguro que quieres eliminar esta consulta?');">
                                    <input type="hidden" name="id" value="<?php echo (int) $fila["id"]; ?>">
                                    <button type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <!-- Este mensaje se muestra cuando la tabla todavía no tiene registros. -->
            <p>Aún no hay consultas registradas.</p>
        <?php endif; ?>

        <!-- Enlace para volver al formulario principal. -->
        <p><a href="index.php">Volver al inicio</a></p>
    </main>
</body>
</html>
