<?php
function consultaMensajes() {
    global $conexion;
    return mysqli_query(
        $conexion,
        'SELECT id, nombre, correo, mensaje, timeStap FROM mensajes ORDER BY timeStap DESC'
    );
}