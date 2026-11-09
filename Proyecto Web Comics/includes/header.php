<?php
/**
 * KomiX - Encabezado Superior Reutilizable
 * Archivo: includes/header.php
 */
if (!isset($pagina_actual)) {
    $pagina_actual = '';
}
if (!isset($titulo_pagina)) {
    $titulo_pagina = 'KomiX - Sistema de Gestión';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina); ?></title>
    <!-- Hoja de estilos en carpeta separada css/estilos.css -->
    <link rel="stylesheet" href="css/estilos.css?v=10">
    <!-- FontAwesome o Lucide para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <!-- Header Superior Negro -->
        <header class="app-header">
            <div class="header-left">
                <button type="button" class="menu-toggle" aria-label="Abrir menú de navegación" aria-controls="app-sidebar" aria-expanded="false">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>
                <a href="reportes.php" aria-label="Ir al dashboard">
                    <img src="./images/komix_logo.png" alt="KomiX" class="header-logo" onerror="this.onerror=null; this.src='./images/komix_logo.png';">
                </a>
            </div>

            <div class="header-right">
                <button type="button" class="header-icon-btn" title="Notificaciones" aria-label="Notificaciones" data-action="notificaciones">
                    <i class="fa-solid fa-bell"></i>
                </button>
                <button type="button" class="header-icon-btn" title="Perfil de usuario" aria-label="Perfil" data-action="perfil" data-nombre="<?php echo htmlspecialchars($_SESSION['komix_usuario']['nombre'] ?? 'Usuario de demostración'); ?>" data-rol="<?php echo htmlspecialchars($_SESSION['komix_usuario']['rol'] ?? 'Vendedor'); ?>">
                    <i class="fa-solid fa-circle-user" style="font-size: 1.25rem;"></i>
                </button>
                <a href="logout.php" class="btn-cerrar-sesion" aria-label="Cerrar sesión">
                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </header>

        <div class="app-body">
