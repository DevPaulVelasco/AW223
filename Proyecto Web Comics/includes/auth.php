<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['komix_usuario'])) {
    header('Location: login.php');
    exit;
}
