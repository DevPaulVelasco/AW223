<?php
// Esta página conecta a MySQL para buscar y actualizar una consulta.
require_once __DIR__ . "/conexion.php";

// El id llega por GET al abrir la página y por POST al enviar el formulario.
$id = (int) ($_GET["id"] ?? $_POST["id"] ?? 0);

// Un id debe ser un número positivo para poder buscar un registro.
if ($id <= 0) {
    die("El identificador no es válido.");
}

// SELECT carga los datos actuales del registro seleccionado.
$consulta = mysqli_prepare(
    $conex,
    "SELECT nombre, telefono, email, mensaje FROM datos WHERE id = ?"
);

if ($consulta === false) {
    die("No se pudo buscar la consulta: " . mysqli_error($conex));
}

// "i" indica que el parámetro id es un entero.
mysqli_stmt_bind_param($consulta, "i", $id);
mysqli_stmt_execute($consulta);
// bind_result coloca las columnas encontradas en estas variables PHP.
mysqli_stmt_bind_result($consulta, $nombre, $telefono, $email, $mensajeConsulta);

// Si SELECT no encontró el id, no hay nada que editar.
if (!mysqli_stmt_fetch($consulta)) {
    die("No se encontró la consulta.");
}

mysqli_stmt_close($consulta);

// Aquí se guardan errores de validación o actualización para mostrarlos en la página.
$mensaje = "";

// Al abrir con GET se muestra el formulario; al enviarlo por POST se actualiza.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Se leen los nuevos valores enviados por el formulario.
    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mensajeConsulta = trim($_POST["message"] ?? "");

    // No se intenta actualizar si falta alguno de los campos.
    if ($nombre === "" || $telefono === "" || $email === "" || $mensajeConsulta === "") {
        $mensaje = "Completa todos los campos.";
    } else {
        // UPDATE reemplaza los valores del registro que coincide con el id.
        $actualizar = mysqli_prepare(
            $conex,
            "UPDATE datos SET nombre = ?, telefono = ?, email = ?, mensaje = ? WHERE id = ?"
        );

        if ($actualizar === false) {
            die("No se pudo preparar la actualización: " . mysqli_error($conex));
        }

        // "ssssi" indica cuatro textos y un entero para el id.
        mysqli_stmt_bind_param(
            $actualizar,
            "ssssi",
            $nombre,
            $telefono,
            $email,
            $mensajeConsulta,
            $id
        );

        // Si se guardó, regresa al listado; si falla, deja un error en esta página.
        if (mysqli_stmt_execute($actualizar)) {
            mysqli_stmt_close($actualizar);
            header("Location: consultas.php?actualizado=1");
            exit;
        }

        $mensaje = "No se pudo actualizar la consulta: " . mysqli_stmt_error($actualizar);
        mysqli_stmt_close($actualizar);
    }
}
?>
<!-- Desde aquí comienza la estructura HTML de la página de edición. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Editar consulta</title>
</head>
<body>
    <!-- El formulario muestra los valores cargados por el SELECT de arriba. -->
    <main class="formulario container">
        <form method="post">
            <h2>Editar consulta</h2>

            <!-- PHP imprime el error si la validación o el UPDATE fallaron. -->
            <?php if ($mensaje !== ""): ?>
                <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8"); ?></p>
            <?php endif; ?>

            <!-- El campo oculto envía el id sin mostrarlo como texto editable. -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="input-group">
                <!-- htmlspecialchars convierte caracteres especiales para imprimir valores de forma segura. -->
                <div class="input-container">
                    <input type="text" name="nombre" placeholder="Nombre y apellido" value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, "UTF-8"); ?>" required>
                </div>
                <div class="input-container">
                    <input type="tel" name="telefono" placeholder="Número de teléfono" value="<?php echo htmlspecialchars($telefono, ENT_QUOTES, "UTF-8"); ?>" required>
                </div>
                <div class="input-container">
                    <input type="email" name="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($email, ENT_QUOTES, "UTF-8"); ?>" required>
                </div>
                <div class="input-container">
                    <textarea name="message" placeholder="Detalles de consulta" required><?php echo htmlspecialchars($mensajeConsulta, ENT_QUOTES, "UTF-8"); ?></textarea>
                </div>
                <button type="submit" class="btn">Guardar cambios</button>
                <a href="consultas.php">Volver al listado</a>
            </div>
        </form>
    </main>
</body>
</html>
