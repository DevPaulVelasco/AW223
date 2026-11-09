<?php
/**
 * KomiX - Módulo de Pedidos
 * Archivo: pedidos.php (Página 9 del diseño)
 * Funcionalidad: Eliminación real de pedidos con confirmación JS y edición dirigida a pedidos_nuevo.php
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'pedidos';
$titulo_pagina = 'KomiX - Pedidos';

// Inicializar lista de pedidos en sesión si no existe
if (!isset($_SESSION['komix_pedidos'])) {
    $_SESSION['komix_pedidos'] = [
        'PED-001' => [
            'id' => 'PED-001',
            'nombre' => 'Lote Cómics Marvel Classic',
            'proveedor' => 'Panini México',
            'fecha' => '2024-05-10',
            'estado' => 'naranja',
            'costo' => '$4,500.00'
        ],
        'PED-002' => [
            'id' => 'PED-002',
            'nombre' => 'Tomo X-Men & Batman',
            'proveedor' => 'Kamite Cómics',
            'fecha' => '2024-05-12',
            'estado' => 'verde',
            'costo' => '$2,350.00'
        ],
        'PED-003' => [
            'id' => 'PED-003',
            'nombre' => 'Edición Especial Spawn & Hulk',
            'proveedor' => 'Diamond Distributors',
            'fecha' => '2024-05-14',
            'estado' => 'rojo',
            'costo' => '$8,900.00'
        ],
        'PED-004' => [
            'id' => 'PED-004',
            'nombre' => 'Manga & Figuras Coleccionables',
            'proveedor' => 'Anime & Cómics S.A.',
            'fecha' => '2024-05-15',
            'estado' => 'verde',
            'costo' => '$1,200.00'
        ],
        'PED-005' => [
            'id' => 'PED-005',
            'nombre' => 'Restock Spider-Man #300',
            'proveedor' => 'Panini México',
            'fecha' => '2024-05-18',
            'estado' => 'naranja',
            'costo' => '$6,100.00'
        ],
        'PED-006' => [
            'id' => 'PED-006',
            'nombre' => 'Graphic Novels Collections',
            'proveedor' => 'Kamite Cómics',
            'fecha' => '2024-05-20',
            'estado' => 'rojo',
            'costo' => '$3,400.00'
        ],
        'PED-007' => [
            'id' => 'PED-007',
            'nombre' => 'Justice League Deluxe Imports',
            'proveedor' => 'Diamond Distributors',
            'fecha' => '2024-05-22',
            'estado' => 'naranja',
            'costo' => '$5,800.00'
        ]
    ];
}

$mensaje_alerta = $_GET['msg'] ?? null;
$tipo_alerta = $_GET['tipo'] ?? 'info';

// Procesar eliminación de pedido
if (isset($_GET['eliminar'])) {
    $id_eliminar = trim($_GET['eliminar']);
    if (isset($_SESSION['komix_pedidos'][$id_eliminar])) {
        $proveedor_eliminado = $_SESSION['komix_pedidos'][$id_eliminar]['proveedor'];
        unset($_SESSION['komix_pedidos'][$id_eliminar]);
        $mensaje_alerta = "El pedido {$id_eliminar} de '{$proveedor_eliminado}' ha sido eliminado exitosamente.";
        $tipo_alerta = 'success';
    }
}

$pedidos = $_SESSION['komix_pedidos'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="content-header-row">
        <h1 class="page-title">Pedidos</h1>
        <div style="display: flex; align-items: center; gap: 14px;">
            <span style="font-size: 0.9rem; color: #444; font-weight: 600;">
                Total pedidos: <strong><?php echo count($pedidos); ?></strong>
            </span>
            <a href="pedidos_nuevo.php" class="btn-nuevo-rojo">
                + Crear pedido
            </a>
        </div>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="filter-bar-row">
        <div class="busqueda-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" class="input-busqueda-pill" placeholder="Busqueda (por ID o Proveedor)">
        </div>
    </div>

    <!-- Tabla de Pedidos -->
    <div class="komix-table-container">
        <div class="table-header-row" style="grid-template-columns: 1.2fr 2fr 1.5fr 100px 1.5fr 120px;">
            <div>ID pedido:</div>
            <div>Proveedor:</div>
            <div>Fecha emision:</div>
            <div style="text-align: center;">Estado:</div>
            <div>Costo total:</div>
            <div style="text-align: right;">Acciones:</div>
        </div>

        <?php if (empty($pedidos)): ?>
            <div class="table-data-row" style="text-align: center; display: block; padding: 24px;">
                <p style="color: #666; font-size: 0.95rem;">No hay pedidos registrados actualmente en el sistema.</p>
            </div>
        <?php else: ?>
            <?php foreach ($pedidos as $p): ?>
                <div class="table-data-row" id="fila-pedido-<?php echo $p['id']; ?>" style="grid-template-columns: 1.2fr 2fr 1.5fr 100px 1.5fr 120px;">
                    <div style="font-weight: 700;"><?php echo htmlspecialchars($p['id']); ?></div>
                    <div><?php echo htmlspecialchars($p['proveedor']); ?></div>
                    <div style="color: #555;"><?php echo htmlspecialchars($p['fecha']); ?></div>
                    <div style="text-align: center;">
                        <span class="badge-estado <?php echo $p['estado']; ?>"></span>
                    </div>
                    <div style="font-weight: 700;"><?php echo htmlspecialchars($p['costo']); ?></div>
                    <div class="table-actions" style="justify-content: flex-end;">
                        <!-- Ver Detalles -->
                        <a href="pedidos_detalle.php?id=<?php echo urlencode($p['id']); ?>" class="btn-action-icon" title="Ver detalles de pedido">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        <!-- Botón Eliminar con confirmación modal JS -->
                        <button 
                            type="button" 
                            class="btn-action-icon" 
                            title="Eliminar pedido <?php echo $p['id']; ?>"
                            onclick="confirmarEliminarPedido(<?php echo htmlspecialchars(json_encode($p['id'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars(json_encode($p['proveedor'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </button>

                        <!-- Botón Editar que redirige a la sección donde se pueden editar los datos -->
                        <a 
                            href="pedidos_nuevo.php?editar=<?php echo urlencode($p['id']); ?>" 
                            class="btn-action-icon" 
                            title="Editar datos de pedido <?php echo $p['id']; ?>"
                            style="text-decoration: none; display: flex; align-items: center; justify-content: center;"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </a>
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
 * Confirmar eliminación del pedido mediante JS/alertas.js
 */
function confirmarEliminarPedido(id, proveedor) {
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.confirm({
            titulo: '¿Eliminar Pedido?',
            mensaje: `¿Estás seguro de que deseas eliminar permanentemente el pedido con ID <strong>${id}</strong> (${proveedor})? Esta acción no se puede deshacer.`,
            tipo: 'danger',
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            onConfirm: function () {
                window.location.href = `pedidos.php?eliminar=${encodeURIComponent(id)}`;
            }
        });
    } else {
        if (confirm(`¿Estás seguro de eliminar el pedido ${id} de ${proveedor}?`)) {
            window.location.href = `pedidos.php?eliminar=${encodeURIComponent(id)}`;
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>
