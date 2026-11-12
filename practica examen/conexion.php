<?php
// Abre la conexión con MySQL usando los datos de XAMPP.
// Si cambian el usuario, contraseña o nombre de base, se actualizan aquí.
$conex = mysqli_connect("localhost", "root", "", "formulario_prueba");

// Si MySQL no responde o la base no existe, se detiene la página y se informa el motivo.
if (!$conex) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Permite guardar y leer correctamente acentos y otros caracteres en español.
if (!mysqli_set_charset($conex, "utf8mb4")) {
    die("No se pudo configurar la conexión: " . mysqli_error($conex));
}