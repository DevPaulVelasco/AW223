<?php
/**
 * KomiX - Módulo de Comics
 * Archivo: comics.php (Página 5 del diseño)
 * Funcionalidad: Listado general de cómics con eliminación real JS y navegación a la interfaz independiente de creación/edición
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'comics';
$titulo_pagina = 'KomiX - Comics';

// Inicializar lista de cómics en sesión si no existe
if (!isset($_SESSION['komix_comics'])) {
    $_SESSION['komix_comics'] = [
        1 => ['id' => 1, 'titulo' => 'Marvel Comics Classic Omnibus', 'editorial' => 'Marvel Comics', 'volumen' => 'Vol. 1 #01', 'ano' => '1963', 'precio_compra' => '$120.00', 'precio_venta' => '$250.00'],
        2 => ['id' => 2, 'titulo' => 'The Amazing Spider-Man', 'editorial' => 'Marvel Comics', 'volumen' => 'Vol. 1 #300', 'ano' => '1988', 'precio_compra' => '$350.00', 'precio_venta' => '$650.00'],
        3 => ['id' => 3, 'titulo' => 'Batman: The Dark Knight Returns', 'editorial' => 'DC Comics', 'volumen' => 'Tomo Único', 'ano' => '1986', 'precio_compra' => '$280.00', 'precio_venta' => '$490.00'],
        4 => ['id' => 4, 'titulo' => 'Superman: Action Comics', 'editorial' => 'DC Comics', 'volumen' => 'Vol. 2 #1000', 'ano' => '2018', 'precio_compra' => '$150.00', 'precio_venta' => '$320.00'],
        5 => ['id' => 5, 'titulo' => 'X-Men: Days of Future Past', 'editorial' => 'Marvel Comics', 'volumen' => 'Vol. 1 #141', 'ano' => '1981', 'precio_compra' => '$400.00', 'precio_venta' => '$800.00'],
        6 => ['id' => 6, 'titulo' => 'Watchmen Deluxe Edition', 'editorial' => 'DC Comics', 'volumen' => 'Edición Deluxe', 'ano' => '1987', 'precio_compra' => '$450.00', 'precio_venta' => '$850.00'],
        7 => ['id' => 7, 'titulo' => 'Spawn: Origins Collection', 'editorial' => 'Image Comics', 'volumen' => 'Vol. 1 #01', 'ano' => '1992', 'precio_compra' => '$200.00', 'precio_venta' => '$390.00'],
    ];
}

$mensaje_alerta = $_GET['msg'] ?? null;
$tipo_alerta = $_GET['tipo'] ?? 'info';

// Procesar eliminación de cómic
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    if (isset($_SESSION['komix_comics'][$id_eliminar])) {
        $titulo_eliminado = $_SESSION['komix_comics'][$id_eliminar]['titulo'];
        unset($_SESSION['komix_comics'][$id_eliminar]);
        $mensaje_alerta = "El cómic '{$titulo_eliminado}' ha sido eliminado exitosamente.";
        $tipo_alerta = 'success';
    }
}

$comics = $_SESSION['komix_comics'];
$editoriales = array_unique(array_column($comics, 'editorial'));
$anos = array_unique(array_column($comics, 'ano'));

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="content-header-row" style="margin-bottom: 24px;">
        <h1 class="page-title">Comics</h1>
        <div style="display: flex; align-items: center; gap: 14px;">
            <span style="font-size: 0.9rem; color: #444; font-weight: 600;">
                Total cómics: <strong><?php echo count($comics); ?></strong>
            </span>
            <!-- Botón hacia la interfaz independiente de Crear Nuevo Cómic -->
            <a href="comics_nuevo.php" class="btn-nuevo-rojo" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-plus"></i> + Nuevo comic
            </a>
        </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="filter-bar-row">
        <div class="busqueda-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" class="input-busqueda-pill" placeholder="Busqueda (por titulo/ISBN)">
        </div>

        <span class="filter-label">Filtros:</span>

        <select class="btn-filtro" data-filter="editorial" aria-label="Filtrar por editorial">
            <option value="">Todas las editoriales</option>
            <?php foreach ($editoriales as $editorial): ?>
                <option value="<?php echo htmlspecialchars($editorial); ?>"><?php echo htmlspecialchars($editorial); ?></option>
            <?php endforeach; ?>
        </select>
        <select class="btn-filtro" data-filter="ano" aria-label="Filtrar por año">
            <option value="">Todos los años</option>
            <?php foreach ($anos as $ano): ?>
                <option value="<?php echo htmlspecialchars($ano); ?>"><?php echo htmlspecialchars($ano); ?></option>
            <?php endforeach; ?>
        </select>
        <a href="catalogo.php" class="btn-filtro" style="margin-left: auto; text-decoration: none; font-size: 0.8rem;">
            Ver modo Catálogo visual →
        </a>
    </div>

    <!-- Tabla de Cómics -->
    <div class="komix-table-container">
        <div class="table-header-row" style="grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr 1.2fr 100px;">
            <div>Titulo:</div>
            <div>Editorial:</div>
            <div>Volumen:</div>
            <div>Precio compra:</div>
            <div>Precio venta:</div>
            <div style="text-align: right;">Acciones:</div>
        </div>

        <?php if (empty($comics)): ?>
            <div class="table-data-row" style="text-align: center; display: block; padding: 28px;">
                <p style="color: #666; font-size: 0.95rem;">No hay cómics registrados actualmente.</p>
            </div>
        <?php else: ?>
            <?php foreach ($comics as $c): ?>
                <div class="table-data-row" id="fila-comic-<?php echo $c['id']; ?>" data-editorial="<?php echo htmlspecialchars($c['editorial']); ?>" data-ano="<?php echo htmlspecialchars($c['ano']); ?>" style="grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr 1.2fr 100px;">
                    <div style="font-weight: 700;"><?php echo htmlspecialchars($c['titulo']); ?></div>
                    <div style="color: #444;"><?php echo htmlspecialchars($c['editorial']); ?></div>
                    <div style="color: #444;"><?php echo htmlspecialchars($c['volumen']); ?></div>
                    <div><?php echo htmlspecialchars($c['precio_compra']); ?></div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($c['precio_venta']); ?></div>
                    <div class="table-actions" style="justify-content: flex-end;">
                        <!-- Botón Editar que redirige a la interfaz independiente comics_nuevo.php?editar=ID -->
                        <a 
                            href="comics_nuevo.php?editar=<?php echo $c['id']; ?>" 
                            class="btn-action-icon" 
                            title="Editar cómic <?php echo htmlspecialchars($c['titulo']); ?>"
                            style="text-decoration: none; display: flex; align-items: center; justify-content: center;"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <!-- Botón Eliminar con confirmación modal JS -->
                        <button 
                            type="button" 
                            class="btn-action-icon" 
                            title="Eliminar cómic"
                            onclick="confirmarEliminarComic(<?php echo $c['id']; ?>, <?php echo htmlspecialchars(json_encode($c['titulo'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)"
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
 * Confirmar eliminación de cómic mediante JS/alertas.js
 */
function confirmarEliminarComic(id, titulo) {
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.confirm({
            titulo: '¿Eliminar Cómic?',
            mensaje: `¿Estás seguro de que deseas eliminar permanentemente el cómic <strong>"${titulo}"</strong>? Esta acción no se puede deshacer.`,
            tipo: 'danger',
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            onConfirm: function () {
                window.location.href = `comics.php?eliminar=${id}`;
            }
        });
    } else {
        if (confirm(`¿Estás seguro de eliminar el cómic "${titulo}"?`)) {
            window.location.href = `comics.php?eliminar=${id}`;
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>
