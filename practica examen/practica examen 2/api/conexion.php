<?php
// Conexión compartida con MySQL. Cambia estos datos si tu XAMPP usa otros.
require_once __DIR__ . "/respuesta.php";

try {
    $conex = mysqli_connect("localhost", "root", "", "menu_restaurante");

    // Permite guardar y leer correctamente acentos y caracteres especiales.
    mysqli_set_charset($conex, "utf8mb4");
} catch (mysqli_sql_exception $error) {
    error_log("Error al conectar con MySQL: " . $error->getMessage());
    responder(500, false, "No se pudo conectar a la base de datos. Importa base_datos.sql en phpMyAdmin.");
}
