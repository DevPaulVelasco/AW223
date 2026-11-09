<?php
/**
 * KomiX - Historial de Tickets y Ventas
 * Archivo: historial.php (Página 14 del diseño)
 * Funcionalidad: Eliminación real de registros de ventas con modal JS de confirmación
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'ventas';
$titulo_pagina = 'KomiX - Historial de Ventas';

// Inicializar lista de ventas en sesión
if (!isset($_SESSION['komix_ventas'])) {
    $_SESSION['komix_ventas'] = [
        'TK-00981' => ['id' => '1', 'folio' => 'TK-00981', 'fecha' => '02/01/2024', 'cliente' => 'Alejandro Morales', 'vendedor' => 'Mendoza A.', 'estado' => 'verde', 'total' => '$450.00'],
        'TK-00982' => ['id' => '2', 'folio' => 'TK-00982', 'fecha' => '15/02/2024', 'cliente' => 'Beatriz Gómez', 'vendedor' => 'García R.', 'estado' => 'verde', 'total' => '$620.00'],
        'TK-00983' => ['id' => '3', 'folio' => 'TK-00983', 'fecha' => '22/03/2024', 'cliente' => 'Carlos Fuentes', 'vendedor' => 'Mendoza A.', 'estado' => 'rojo', 'total' => '$280.00'],
        'TK-00984' => ['id' => '4', 'folio' => 'TK-00984', 'fecha' => '10/05/2024', 'cliente' => 'Diana López', 'vendedor' => 'Pérez L.', 'estado' => 'verde', 'total' => '$890.00'],
        'TK-00985' => ['id' => '5', 'folio' => 'TK-00985', 'fecha' => '18/06/2024', 'cliente' => 'Eduardo Silva', 'vendedor' => 'Mendoza A.', 'estado' => 'naranja', 'total' => '$350.00'],
    ];
}

$mensaje_alerta = null;
$tipo_alerta = 'info';

// Procesar eliminación de ticket de venta
if (isset($_GET['eliminar'])) {
    $folio_eliminar = trim($_GET['eliminar']);
    if (isset($_SESSION['komix_ventas'][$folio_eliminar])) {
        $cliente = $_SESSION['komix_ventas'][$folio_eliminar]['cliente'];
        unset($_SESSION['komix_ventas'][$folio_eliminar]);
        $mensaje_alerta = "El registro de venta {$folio_eliminar} de {$cliente} ha sido eliminado correctamente.";
        $tipo_alerta = 'success';
    }
}

$tickets = $_SESSION['komix_ventas'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="content-header-row" style="margin-bottom: 20px;">
        <h1 class="page-title">Historial</h1>
        <div style="display: flex; align-items: center; gap: 14px;">
            <span style="font-size: 0.88rem; font-weight: 700; color: #333;">
                Registros: <strong><?php echo count($tickets); ?></strong>
            </span>
            <div class="busqueda-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" class="input-busqueda-pill" placeholder="Buscador por folio o cliente" style="width: 320px;">
            </div>
        </div>
    </div>

    <!-- Caja Roja de Filtros de Búsqueda Avanzada -->
    <div class="caja-busqueda-avanzada">
        <div class="caja-titulo">Filtros de busqueda avanzada</div>
        <form action="historial.php" method="GET" class="caja-filtros-row" data-range-filter>
            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-weight: 700;">Desde:</label>
                <input type="date" name="desde" class="input-fecha-white" style="width: 160px; text-align: center;">
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <label style="font-weight: 700;">Hasta:</label>
                <input type="date" name="hasta" class="input-fecha-white" style="width: 160px; text-align: center;">
            </div>

            <button type="submit" class="btn-consultar-white">Consultar</button>
        </form>
    </div>

    <!-- Tabla del Historial de Ventas -->
    <div class="komix-table-container">
        <div class="table-header-row" style="grid-template-columns: 1.5fr 1.5fr 2fr 1.5fr 1fr 60px;">
            <div>Folio:</div>
            <div>Fecha:</div>
            <div>Cliente:</div>
            <div>Vendedor:</div>
            <div style="text-align: center;">Estado:</div>
            <div style="text-align: right;"></div>
        </div>

        <?php if (empty($tickets)): ?>
            <div class="table-data-row" style="text-align: center; display: block; padding: 24px;">
                <p style="color: #666; font-size: 0.95rem;">No hay registros de ventas en el historial.</p>
            </div>
        <?php else: ?>
            <?php foreach ($tickets as $t): ?>
                <?php $fecha_venta = DateTime::createFromFormat('d/m/Y', $t['fecha']); ?>
                <div class="table-data-row" id="fila-venta-<?php echo $t['folio']; ?>" data-fecha="<?php echo $fecha_venta ? $fecha_venta->format('Y-m-d') : ''; ?>" style="grid-template-columns: 1.5fr 1.5fr 2fr 1.5fr 1fr 60px;">
                    <div style="font-weight: 700;"><?php echo htmlspecialchars($t['folio']); ?></div>
                    <div style="color: #444;"><?php echo htmlspecialchars($t['fecha']); ?></div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($t['cliente']); ?></div>
                    <div style="color: #444;"><?php echo htmlspecialchars($t['vendedor']); ?></div>
                    <div style="text-align: center;">
                        <span class="badge-estado <?php echo $t['estado']; ?>"></span>
                    </div>
                    <div class="table-actions" style="justify-content: flex-end;">
                        <!-- Botón Eliminar con Confirmación JavaScript -->
                        <button 
                            type="button" 
                            class="btn-action-icon" 
                            title="Eliminar registro de venta"
                            onclick="confirmarEliminarVenta(<?php echo htmlspecialchars(json_encode($t['folio'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars(json_encode($t['cliente'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)"
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
 * Confirmar eliminación del registro de venta mediante JS/alertas.js
 */
function confirmarEliminarVenta(folio, cliente) {
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.confirm({
            titulo: '¿Eliminar Registro de Venta?',
            mensaje: `¿Estás seguro de que deseas eliminar permanentemente el ticket con Folio <strong>${folio}</strong> de <strong>${cliente}</strong>? Esta acción no se puede deshacer.`,
            tipo: 'danger',
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            onConfirm: function () {
                window.location.href = `historial.php?eliminar=${encodeURIComponent(folio)}`;
            }
        });
    } else {
        if (confirm(`¿Estás seguro de eliminar el ticket ${folio} de ${cliente}?`)) {
            window.location.href = `historial.php?eliminar=${encodeURIComponent(folio)}`;
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>
