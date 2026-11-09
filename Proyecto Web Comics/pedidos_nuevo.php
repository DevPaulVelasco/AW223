<?php
/**
 * KomiX - Formulario de Nuevo Pedido / Edición de Pedido
 * Archivo: pedidos_nuevo.php (Página 11 del diseño)
 * Funcionalidad: Sección para editar pedidos seleccionados y crear nuevos pedidos con confirmación JS
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

$mensaje_alerta = null;
$tipo_alerta = 'info';

// Procesar eliminación de pedido si se llama desde esta vista
if (isset($_GET['eliminar'])) {
    $id_eliminar = trim($_GET['eliminar']);
    if (isset($_SESSION['komix_pedidos'][$id_eliminar])) {
        $proveedor_eliminado = $_SESSION['komix_pedidos'][$id_eliminar]['proveedor'];
        unset($_SESSION['komix_pedidos'][$id_eliminar]);
        $mensaje_alerta = "El pedido {$id_eliminar} de '{$proveedor_eliminado}' ha sido eliminado exitosamente.";
        $tipo_alerta = 'success';
    }
}

// Procesar guardado de edición o creación de pedido
$pedido_en_edicion = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'crear';
    $nombre = trim($_POST['nombre'] ?? '');
    $proveedor = trim($_POST['proveedor'] ?? '');
    $fecha = trim($_POST['fecha_emision'] ?? date('Y-m-d'));
    $estado = trim($_POST['estado'] ?? 'naranja');
    $costo_raw = trim($_POST['costo'] ?? '0');
    $costo_formateado = '$' . number_format((float)str_replace(['$', ','], '', $costo_raw), 2);

    if ($accion === 'editar') {
        $id_editar = trim($_POST['id_pedido'] ?? '');
        if (isset($_SESSION['komix_pedidos'][$id_editar]) && !empty($proveedor)) {
            $_SESSION['komix_pedidos'][$id_editar]['nombre'] = $nombre;
            $_SESSION['komix_pedidos'][$id_editar]['proveedor'] = $proveedor;
            $_SESSION['komix_pedidos'][$id_editar]['fecha'] = $fecha;
            $_SESSION['komix_pedidos'][$id_editar]['estado'] = $estado;
            $_SESSION['komix_pedidos'][$id_editar]['costo'] = $costo_formateado;
            header("Location: pedidos.php?msg=" . urlencode("Los datos del pedido {$id_editar} han sido actualizados con éxito.") . "&tipo=success");
            exit;
        }
    } else {
        // Crear nuevo pedido
        if (!empty($proveedor)) {
            $num = count($_SESSION['komix_pedidos']) + 1;
            $nuevo_id = 'PED-' . str_pad((string)$num, 3, '0', STR_PAD_LEFT);
            while (isset($_SESSION['komix_pedidos'][$nuevo_id])) {
                $num++;
                $nuevo_id = 'PED-' . str_pad((string)$num, 3, '0', STR_PAD_LEFT);
            }

            $_SESSION['komix_pedidos'][$nuevo_id] = [
                'id' => $nuevo_id,
                'nombre' => $nombre ?: 'Pedido General',
                'proveedor' => $proveedor,
                'fecha' => $fecha,
                'estado' => $estado,
                'costo' => $costo_formateado
            ];
            header("Location: pedidos.php?msg=" . urlencode("Pedido {$nuevo_id} registrado exitosamente.") . "&tipo=success");
            exit;
        }
    }
}

// Comprobar si se solicitó editar un pedido por GET
if (isset($_GET['editar'])) {
    $id_editar = trim($_GET['editar']);
    if (isset($_SESSION['komix_pedidos'][$id_editar])) {
        $pedido_en_edicion = $_SESSION['komix_pedidos'][$id_editar];
    }
}

$pedidos = $_SESSION['komix_pedidos'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="layout-con-panel-derecho">
    <!-- Columna Principal: Tabla de Pedidos -->
    <main class="main-column">
        <div class="content-header-row" style="margin-bottom: 24px;">
            <h1 class="page-title">Pedidos</h1>
            <a href="pedidos.php" class="btn-ticket-action" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; padding: 8px 16px;">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>
        </div>

        <div class="komix-table-container">
            <div class="table-header-row" style="grid-template-columns: 1.2fr 1.8fr 1.3fr 90px 1.3fr 110px;">
                <div>ID:</div>
                <div>Proveedor:</div>
                <div>Fecha:</div>
                <div style="text-align: center;">Estado:</div>
                <div>Costo:</div>
                <div style="text-align: right;">Acciones:</div>
            </div>

            <?php if (empty($pedidos)): ?>
                <div class="table-data-row" style="text-align: center; display: block; padding: 24px;">
                    <p style="color: #666; font-size: 0.95rem;">No hay registros de pedidos.</p>
                </div>
            <?php else: ?>
                <?php foreach ($pedidos as $p): ?>
                    <?php $es_editando = ($pedido_en_edicion && $pedido_en_edicion['id'] === $p['id']); ?>
                    <div 
                        class="table-data-row <?php echo $es_editando ? 'editando-fila' : ''; ?>" 
                        id="fila-pedido-<?php echo $p['id']; ?>"
                        style="grid-template-columns: 1.2fr 1.8fr 1.3fr 90px 1.3fr 110px;"
                    >
                        <div style="font-weight: 700;">
                            <?php echo htmlspecialchars($p['id']); ?>
                            <?php if ($es_editando): ?>
                                <span class="badge-editando-indicador">Editando</span>
                            <?php endif; ?>
                        </div>
                        <div><?php echo htmlspecialchars($p['proveedor']); ?></div>
                        <div style="color: #555;"><?php echo htmlspecialchars($p['fecha']); ?></div>
                        <div style="text-align: center;">
                            <span class="badge-estado <?php echo $p['estado']; ?>"></span>
                        </div>
                        <div style="font-weight: 700;"><?php echo htmlspecialchars($p['costo']); ?></div>
                        <div class="table-actions" style="justify-content: flex-end;">
                            <a 
                                href="pedidos_nuevo.php?editar=<?php echo urlencode($p['id']); ?>" 
                                class="btn-action-icon" 
                                title="Editar pedido <?php echo $p['id']; ?>"
                            >
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button 
                                type="button" 
                                class="btn-action-icon" 
                                title="Eliminar pedido"
                                onclick="confirmarEliminarPedido(<?php echo htmlspecialchars(json_encode($p['id'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars(json_encode($p['proveedor'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Panel Lateral Derecho Rojo: Crear o Editar Pedido -->
    <aside class="side-panel-rojo" id="panel-pedido">
        <?php if ($pedido_en_edicion): ?>
            <!-- SECCIÓN: EDITAR PEDIDO SELECCIONADO -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <h2 class="side-panel-title" style="margin-bottom: 0;">Editar Pedido</h2>
                <span class="badge-editando-indicador" style="background: rgba(0,0,0,0.3); border: 1px solid #fff;">
                    <?php echo htmlspecialchars($pedido_en_edicion['id']); ?>
                </span>
            </div>

            <p style="font-size: 0.8rem; color: #ffe6e6; margin-bottom: 16px;">
                Modifique los datos del pedido <strong><?php echo htmlspecialchars($pedido_en_edicion['id']); ?></strong> y guarde los cambios.
            </p>

            <form action="pedidos_nuevo.php" method="POST" class="side-panel-form">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_pedido" value="<?php echo htmlspecialchars($pedido_en_edicion['id']); ?>">

                <div class="form-group-panel">
                    <label>Nombre / Descripción:</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        class="form-input-white" 
                        value="<?php echo htmlspecialchars($pedido_en_edicion['nombre']); ?>" 
                        required
                    >
                </div>

                <div class="form-group-panel">
                    <label>Proveedor:</label>
                    <input 
                        type="text" 
                        name="proveedor" 
                        class="form-input-white" 
                        value="<?php echo htmlspecialchars($pedido_en_edicion['proveedor']); ?>" 
                        required
                    >
                </div>

                <div class="form-group-panel">
                    <label>Fecha de emisión:</label>
                    <input 
                        type="date" 
                        name="fecha_emision" 
                        class="form-input-white" 
                        value="<?php echo htmlspecialchars($pedido_en_edicion['fecha']); ?>" 
                        required
                    >
                </div>

                <div class="form-group-panel">
                    <label>Estado del Pedido:</label>
                    <select name="estado" class="form-input-white" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23111\' stroke-width=\'2\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 14px center; background-size: 14px;">
                        <option value="naranja" <?php echo $pedido_en_edicion['estado'] === 'naranja' ? 'selected' : ''; ?>>En Proceso (Naranja)</option>
                        <option value="verde" <?php echo $pedido_en_edicion['estado'] === 'verde' ? 'selected' : ''; ?>>Completado / Recibido (Verde)</option>
                        <option value="rojo" <?php echo $pedido_en_edicion['estado'] === 'rojo' ? 'selected' : ''; ?>>Cancelado / Con Problemas (Rojo)</option>
                    </select>
                </div>

                <div class="form-group-panel">
                    <label>Costo Total:</label>
                    <input 
                        type="text" 
                        name="costo" 
                        class="form-input-white" 
                        value="<?php echo htmlspecialchars(str_replace('$', '', $pedido_en_edicion['costo'])); ?>" 
                        required
                    >
                </div>

                <button type="submit" class="btn-guardar-panel" style="background-color: #ffffff; color: #7b0808; font-weight: 900;">
                    Actualizar Pedido
                </button>

                <a href="pedidos_nuevo.php" class="btn-cancelar-edicion" style="text-align: center; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                    Cancelar Edición
                </a>
            </form>
        <?php else: ?>
            <!-- SECCIÓN: CREAR NUEVO PEDIDO -->
            <h2 class="side-panel-title">+ Nuevo Pedidos</h2>

            <form action="pedidos_nuevo.php" method="POST" class="side-panel-form">
                <input type="hidden" name="accion" value="crear">

                <div class="form-group-panel">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" class="form-input-white" required placeholder="Ej: Lote Cómics Panini">
                </div>

                <div class="form-group-panel">
                    <label>Proveedor:</label>
                    <input type="text" name="proveedor" class="form-input-white" required placeholder="Nombre del proveedor">
                </div>

                <div class="form-group-panel">
                    <label>Fecha de emision:</label>
                    <input type="date" name="fecha_emision" class="form-input-white" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group-panel">
                    <label>Estado del Pedido:</label>
                    <select name="estado" class="form-input-white" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23111\' stroke-width=\'2\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 14px center; background-size: 14px;">
                        <option value="naranja" selected>En Proceso (Naranja)</option>
                        <option value="verde">Completado / Recibido (Verde)</option>
                        <option value="rojo">Cancelado (Rojo)</option>
                    </select>
                </div>

                <div class="form-group-panel">
                    <label>Costo:</label>
                    <input type="number" step="0.01" name="costo" class="form-input-white" required placeholder="0.00">
                </div>

                <button type="submit" class="btn-guardar-panel">
                    Guardar
                </button>
            </form>
        <?php endif; ?>
    </aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($mensaje_alerta): ?>
        if (typeof KomixAlert !== 'undefined') {
            KomixAlert.<?php echo $tipo_alerta; ?>('<?php echo addslashes($mensaje_alerta); ?>');
        }
    <?php endif; ?>

    <?php if ($pedido_en_edicion): ?>
        const panel = document.getElementById('panel-pedido');
        if (panel) {
            panel.scrollIntoView({ behavior: 'smooth' });
        }
    <?php endif; ?>
});

/**
 * Confirmar eliminación de pedido mediante JS/alertas.js
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
                window.location.href = `pedidos_nuevo.php?eliminar=${encodeURIComponent(id)}`;
            }
        });
    } else {
        if (confirm(`¿Estás seguro de eliminar el pedido ${id} de ${proveedor}?`)) {
            window.location.href = `pedidos_nuevo.php?eliminar=${encodeURIComponent(id)}`;
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>
