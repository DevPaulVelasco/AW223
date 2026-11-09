<?php
/**
 * KomiX - Módulo de Artículos
 * Archivo: articulos.php (Página 6 del diseño)
 * Funcionalidad: Listado general de artículos con eliminación real JS y navegación a la interfaz independiente de creación/edición
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'articulos';
$titulo_pagina = 'KomiX - Artículos';

// Inicializar lista de artículos en sesión si no existe
if (!isset($_SESSION['komix_articulos'])) {
    $_SESSION['komix_articulos'] = [
        1 => ['id' => 1, 'titulo' => 'Marvel Comics Classic Omnibus', 'volumen' => 'Vol. 1', 'categoria' => 'comic', 'precio_compra' => '$95.00', 'precio_venta' => '$190.00', 'stock' => 25],
        2 => ['id' => 2, 'titulo' => 'Figura Batman McFarlane DC', 'volumen' => 'Serie 7"', 'categoria' => 'figuras', 'precio_compra' => '$320.00', 'precio_venta' => '$650.00', 'stock' => 14],
        3 => ['id' => 3, 'titulo' => 'Funko Pop Spider-Man 2099', 'volumen' => 'Edición Especial', 'categoria' => 'figuras', 'precio_compra' => '$180.00', 'precio_venta' => '$380.00', 'stock' => 19],
        4 => ['id' => 4, 'titulo' => 'Póster Holográfico X-Men', 'volumen' => '60x90 cm', 'categoria' => 'posters', 'precio_compra' => '$75.00', 'precio_venta' => '$160.00', 'stock' => 40],
        5 => ['id' => 5, 'titulo' => 'Taza Cerámica Capitán América', 'volumen' => '11 oz', 'categoria' => 'merch', 'precio_compra' => '$45.00', 'precio_venta' => '$120.00', 'stock' => 30],
    ];
}

$mensaje_alerta = $_GET['msg'] ?? null;
$tipo_alerta = $_GET['tipo'] ?? 'info';

// Procesar eliminación de artículo
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    if (isset($_SESSION['komix_articulos'][$id_eliminar])) {
        $titulo_eliminado = $_SESSION['komix_articulos'][$id_eliminar]['titulo'];
        unset($_SESSION['komix_articulos'][$id_eliminar]);
        $mensaje_alerta = "El artículo '{$titulo_eliminado}' ha sido eliminado exitosamente.";
        $tipo_alerta = 'success';
    }
}

$articulos = $_SESSION['komix_articulos'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="content-header-row" style="margin-bottom: 24px;">
        <h1 class="page-title">Articulos</h1>
        <div style="display: flex; align-items: center; gap: 14px;">
            <span style="font-size: 0.9rem; color: #444; font-weight: 600;">
                Total artículos: <strong><?php echo count($articulos); ?></strong>
            </span>
            <!-- Botón hacia la interfaz independiente de Crear Nuevo Artículo -->
            <a href="articulos_nuevo.php" class="btn-nuevo-rojo" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-plus"></i> + Nuevo artículo
            </a>
        </div>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="filter-bar-row">
        <div class="busqueda-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" class="input-busqueda-pill" placeholder="Busqueda (por titulo/ISBN)">
        </div>
    </div>

    <!-- Tabla de Artículos -->
    <div class="komix-table-container">
        <div class="table-header-row" style="grid-template-columns: 2fr 1.5fr 1.3fr 1.3fr 100px;">
            <div>Titulo:</div>
            <div>Volumen:</div>
            <div>Precio compra:</div>
            <div>Precio venta:</div>
            <div style="text-align: right;">Acciones:</div>
        </div>

        <?php if (empty($articulos)): ?>
            <div class="table-data-row" style="text-align: center; display: block; padding: 28px;">
                <p style="color: #666; font-size: 0.95rem;">No hay artículos registrados actualmente.</p>
            </div>
        <?php else: ?>
            <?php foreach ($articulos as $art): ?>
                <div class="table-data-row" id="fila-articulo-<?php echo $art['id']; ?>" style="grid-template-columns: 2fr 1.5fr 1.3fr 1.3fr 100px;">
                    <div style="font-weight: 700;"><?php echo htmlspecialchars($art['titulo']); ?></div>
                    <div style="color: #444;"><?php echo htmlspecialchars($art['volumen']); ?></div>
                    <div><?php echo htmlspecialchars($art['precio_compra']); ?></div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($art['precio_venta']); ?></div>
                    <div class="table-actions" style="justify-content: flex-end;">
                        <!-- Botón Editar que redirige a la interfaz independiente articulos_nuevo.php?editar=ID -->
                        <a 
                            href="articulos_nuevo.php?editar=<?php echo $art['id']; ?>" 
                            class="btn-action-icon" 
                            title="Editar artículo <?php echo htmlspecialchars($art['titulo']); ?>"
                            style="text-decoration: none; display: flex; align-items: center; justify-content: center;"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <!-- Botón Eliminar con confirmación modal JS -->
                        <button 
                            type="button" 
                            class="btn-action-icon" 
                            title="Eliminar artículo"
                            onclick="confirmarEliminarArticulo(<?php echo $art['id']; ?>, <?php echo htmlspecialchars(json_encode($art['titulo'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($mensaje_alerta): ?>
        if (typeof KomixAlert !== 'undefined') {
            KomixAlert.<?php echo $tipo_alerta; ?>('<?php echo addslashes($mensaje_alerta); ?>');
        }
    <?php endif; ?>
});

/**
 * Confirmar eliminación de artículo mediante JS/alertas.js
 */
function confirmarEliminarArticulo(id, titulo) {
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.confirm({
            titulo: '¿Eliminar Artículo?',
            mensaje: `¿Estás seguro de que deseas eliminar permanentemente el artículo <strong>"${titulo}"</strong>? Esta acción no se puede deshacer.`,
            tipo: 'danger',
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            onConfirm: function () {
                window.location.href = `articulos.php?eliminar=${id}`;
            }
        });
    } else {
        if (confirm(`¿Estás seguro de eliminar el artículo "${titulo}"?`)) {
            window.location.href = `articulos.php?eliminar=${id}`;
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>
