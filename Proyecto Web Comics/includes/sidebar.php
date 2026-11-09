<?php
/**
 * KomiX - Menú Lateral Izquierdo Reutilizable
 * Archivo: includes/sidebar.php
 */
if (!isset($pagina_actual)) {
    $pagina_actual = '';
}
?>
<!-- Sidebar Lateral Izquierda Negra -->
<aside class="app-sidebar" id="app-sidebar" aria-label="Navegación principal">
    <ul class="sidebar-menu">
        <li class="sidebar-item <?php echo ($pagina_actual === 'comics') ? 'active' : ''; ?>">
            <a href="comics.php" <?php echo ($pagina_actual === 'comics') ? 'aria-current="page"' : ''; ?>>
                <i class="fa-solid fa-mask nav-icon"></i>
                <span>Comics</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo ($pagina_actual === 'articulos') ? 'active' : ''; ?>">
            <a href="articulos.php" <?php echo ($pagina_actual === 'articulos') ? 'aria-current="page"' : ''; ?>>
                <i class="fa-regular fa-newspaper nav-icon"></i>
                <span>Articulos</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo ($pagina_actual === 'inventario') ? 'active' : ''; ?>">
            <a href="inventario.php" <?php echo ($pagina_actual === 'inventario') ? 'aria-current="page"' : ''; ?>>
                <i class="fa-solid fa-boxes-stacked nav-icon"></i>
                <span>Inventario</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo ($pagina_actual === 'proveedores') ? 'active' : ''; ?>">
            <a href="proveedores.php" <?php echo ($pagina_actual === 'proveedores') ? 'aria-current="page"' : ''; ?>>
                <i class="fa-solid fa-truck-field nav-icon"></i>
                <span>Proveedores</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo ($pagina_actual === 'pedidos') ? 'active' : ''; ?>">
            <a href="pedidos.php" <?php echo ($pagina_actual === 'pedidos') ? 'aria-current="page"' : ''; ?>>
                <i class="fa-solid fa-cart-shopping nav-icon"></i>
                <span>Pedidos</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo ($pagina_actual === 'ventas') ? 'active' : ''; ?>">
            <a href="ventas.php" <?php echo ($pagina_actual === 'ventas') ? 'aria-current="page"' : ''; ?>>
                <i class="fa-solid fa-cash-register nav-icon"></i>
                <span>Ventas</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo ($pagina_actual === 'reportes') ? 'active' : ''; ?>">
            <a href="reportes.php" <?php echo ($pagina_actual === 'reportes') ? 'aria-current="page"' : ''; ?>>
                <i class="fa-solid fa-chart-column nav-icon"></i>
                <span>Reportes</span>
            </a>
        </li>
        <li class="sidebar-item <?php echo ($pagina_actual === 'usuario') ? 'active' : ''; ?>">
            <a href="usuarios.php" <?php echo ($pagina_actual === 'usuario') ? 'aria-current="page"' : ''; ?>>
                <i class="fa-solid fa-user-gear nav-icon"></i>
                <span>Usuario</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="logout.php" class="sidebar-logout-btn" title="Salir del sistema">
            <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 1.25rem;"></i>
        </a>
    </div>
</aside>
<button type="button" class="sidebar-backdrop" aria-label="Cerrar menú" hidden></button>
