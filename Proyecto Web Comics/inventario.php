<?php
/**
 * KomiX - Módulo de Inventario
 * Archivo: inventario.php (Página 7 del diseño)
 */
require_once 'includes/auth.php';
$pagina_actual = 'inventario';
$titulo_pagina = 'KomiX - Inventario';

$mensaje_alerta = $_GET['msg'] ?? null;
$tipo_alerta = $_GET['tipo'] ?? 'info';
$inventario = [
    ['nombre' => 'The Amazing Spider-Man #300', 'tipo' => 'Comic', 'stock_actual' => 2, 'stock_minimo' => 10, 'estado' => 'rojo'],
    ['nombre' => 'Figura Colección Batman DC', 'tipo' => 'Articulo', 'stock_actual' => 5, 'stock_minimo' => 8, 'estado' => 'naranja'],
    ['nombre' => 'Watchmen Edición Especial', 'tipo' => 'Comic', 'stock_actual' => 25, 'stock_minimo' => 5, 'estado' => 'verde'],
    ['nombre' => 'Funko Pop Iron Man Endgame', 'tipo' => 'Articulo', 'stock_actual' => 1, 'stock_minimo' => 6, 'estado' => 'rojo'],
    ['nombre' => 'Superman All Star Tomo 1', 'tipo' => 'Comic', 'stock_actual' => 18, 'stock_minimo' => 5, 'estado' => 'verde'],
];

foreach ($_SESSION['komix_entradas_inventario'] ?? [] as $entrada) {
    foreach ($entrada as $producto) {
        $inventario[] = $producto;
    }
}

$total_sku = count($inventario);
$productos_stock_bajo = count(array_filter($inventario, function ($producto) {
    return $producto['stock_actual'] < $producto['stock_minimo'];
}));

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <h1 class="page-title" style="margin-bottom: 24px;">Inventario</h1>

    <!-- Tarjetas de Métricas (KPIs) -->
    <div class="kpi-cards-grid">
        <div class="kpi-card kpi-card-rojo">
            <span class="kpi-title">Total de SKU</span>
            <span class="kpi-value"><?php echo $total_sku; ?></span>
        </div>
        <div class="kpi-card kpi-card-naranja">
            <span class="kpi-title">Valor del inventario</span>
            <span class="kpi-value">$125,000</span>
        </div>
        <div class="kpi-card kpi-card-verde">
            <span class="kpi-title">Productos con stock criticos</span>
            <span class="kpi-value"><?php echo $productos_stock_bajo; ?> items</span>
        </div>
    </div>

    <!-- Barra de Búsqueda y Switch de Stock Bajo -->
    <div class="filter-bar-row" style="justify-content: space-between;">
        <div class="busqueda-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="search" class="input-busqueda-pill" placeholder="Busqueda" aria-label="Buscar en inventario">
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-weight: 800; font-size: 0.95rem; letter-spacing: 0.05em; color: #111;">STOCK BAJO</span>
            <label class="switch-toggle">
                <input type="checkbox" data-filter="stock-bajo" aria-label="Mostrar solo productos con stock bajo">
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>

    <!-- Tabla de Inventario -->
    <div class="komix-table-container">
        <div class="table-header-row" style="grid-template-columns: 2fr 1.5fr 1fr 1fr 100px;">
            <div>Nombre:</div>
            <div>Tipo(comic/articulo)</div>
            <div>Stock actual</div>
            <div>Stock minimo:</div>
            <div style="text-align: center;">Estado</div>
        </div>

        <?php foreach ($inventario as $inv): ?>
            <div class="table-data-row" data-stock-bajo="<?php echo $inv['stock_actual'] < $inv['stock_minimo'] ? 'true' : 'false'; ?>" style="grid-template-columns: 2fr 1.5fr 1fr 1fr 100px;">
                <div style="font-weight: 700;"><?php echo htmlspecialchars($inv['nombre']); ?></div>
                <div style="color: #444;"><?php echo htmlspecialchars($inv['tipo']); ?></div>
                <div style="font-weight: 600;"><?php echo $inv['stock_actual']; ?></div>
                <div style="color: #666;"><?php echo $inv['stock_minimo']; ?></div>
                <div style="text-align: center;">
                    <span class="badge-estado <?php echo $inv['estado']; ?>"></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($mensaje_alerta): ?>
        if (typeof KomixAlert !== 'undefined') KomixAlert.<?php echo $tipo_alerta; ?>('<?php echo addslashes($mensaje_alerta); ?>');
    <?php endif; ?>
});
</script>

<?php include 'includes/footer.php'; ?>
