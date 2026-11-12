<?php
// Este mensaje se muestra después dentro del HTML de index.php.
$mensaje = "";

// Solo se procesa la creación cuando el formulario se envía por POST con el botón send.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send"])) {
    // $_POST contiene los valores que llegaron desde los campos del formulario.
    // trim quita espacios al principio y al final; ?? "" usa texto vacío si falta un campo.
    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mensajeConsulta = trim($_POST["message"] ?? "");

    // Evita guardar campos vacíos, incluso si alguien omite la validación del navegador.
    if ($nombre === "" || $telefono === "" || $email === "" || $mensajeConsulta === "") {
        $mensaje = "Completa todos los campos.";
    } else {
        // Incluye el archivo que crea la conexión; se ejecuta solo si hay datos válidos.
        require_once __DIR__ . "/conexion.php";

        // Los signos ? son espacios para los valores y evitan unir datos directamente al SQL.
        $consulta = mysqli_prepare(
            $conex,
            "INSERT INTO datos (nombre, telefono, email, mensaje) VALUES (?, ?, ?, ?)"
        );

        // Si MySQL no pudo preparar el INSERT, se conserva un mensaje de error claro.
        if ($consulta === false) {
            $mensaje = "No se pudo preparar el registro: " . mysqli_error($conex);
        } else {
            // "ssss" indica que los cuatro valores son texto; se asocian en el mismo orden.
            mysqli_stmt_bind_param($consulta, "ssss", $nombre, $telefono, $email, $mensajeConsulta);

            // Ejecuta el INSERT y prepara el mensaje que index.php mostrará al usuario.
            if (mysqli_stmt_execute($consulta)) {
                $mensaje = "Tu consulta se envió correctamente.";
            } else {
                $mensaje = "No se pudo guardar la consulta: " . mysqli_stmt_error($consulta);
            }

            // Libera los recursos que utilizó la consulta preparada.
            mysqli_stmt_close($consulta);
        }
    }
}
