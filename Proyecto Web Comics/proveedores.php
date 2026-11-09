<?php
/**
 * KomiX - Módulo de Proveedores
 * Archivo: proveedores.php (Página 8 del diseño)
 */
require_once 'includes/auth.php';
$pagina_actual = 'proveedores';
$titulo_pagina = 'KomiX - Proveedores';

if (!isset($_SESSION['komix_proveedores'])) {
    $_SESSION['komix_proveedores'] = [
        1 => ['id' => 1, 'nombre' => 'Distribuidora Panini México', 'contacto' => 'Carlos Ramírez', 'telefono' => '+52 55 1234 5678', 'correo' => 'ventas@panini.com.mx'],
        2 => ['id' => 2, 'nombre' => 'Editorial Kamite Cómics', 'contacto' => 'Sofía Morales', 'telefono' => '+52 55 8765 4321', 'correo' => 'contacto@kamite.com.mx'],
        3 => ['id' => 3, 'nombre' => 'Diamond Comic Distributors', 'contacto' => 'John Gallagher', 'telefono' => '+1 443 318 8000', 'correo' => 'orders@diamondcomics.com'],
        4 => ['id' => 4, 'nombre' => 'Coleccionables Anime & Comics S.A.', 'contacto' => 'Mariana Torres', 'telefono' => '+52 33 9988 7766', 'correo' => 'mtorres@coleccionables.mx'],
    ];
}

$mensaje_alerta = $_GET['msg'] ?? null;
$tipo_alerta = $_GET['tipo'] ?? 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre']) && !empty($_POST['contacto'])) {
    $nuevo_id = empty($_SESSION['komix_proveedores']) ? 1 : max(array_keys($_SESSION['komix_proveedores'])) + 1;
    $_SESSION['komix_proveedores'][$nuevo_id] = [
        'id' => $nuevo_id,
        'nombre' => trim($_POST['nombre']),
        'contacto' => trim($_POST['contacto']),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'correo' => trim($_POST['correo'] ?? '')
    ];
    header('Location: proveedores.php?msg=' . urlencode('Proveedor registrado correctamente.') . '&tipo=success');
    exit;
}

if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    if (isset($_SESSION['komix_proveedores'][$id_eliminar])) {
        unset($_SESSION['komix_proveedores'][$id_eliminar]);
        $mensaje_alerta = 'Proveedor eliminado correctamente.';
        $tipo_alerta = 'success';
    }
}

$proveedores = $_SESSION['komix_proveedores'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="layout-con-panel-derecho">
    <!-- Columna Principal -->
    <main class="main-column">
        <h1 class="page-title" style="margin-bottom: 24px;">Proveedores</h1>

        <div class="proveedores-grid">
            <?php foreach ($proveedores as $prov): ?>
                <div class="proveedor-card">
                    <div class="proveedor-nombre"><?php echo htmlspecialchars($prov['nombre']); ?></div>
                    <div class="proveedor-detalle"><strong>Contacto:</strong> <?php echo htmlspecialchars($prov['contacto']); ?></div>
                    <div class="proveedor-detalle"><strong>Teléfono:</strong> <?php echo htmlspecialchars($prov['telefono']); ?></div>
                    <div class="proveedor-detalle"><strong>Correo:</strong> <?php echo htmlspecialchars($prov['correo']); ?></div>

                    <div class="proveedor-actions">
                        <a class="btn-action-icon" href="proveedores_nuevo.php?editar=<?php echo $prov['id']; ?>" title="Editar proveedor" style="display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-pen"></i></a>
                        <button type="button" class="btn-action-icon" title="Eliminar proveedor" onclick="confirmarEliminarProveedor(<?php echo $prov['id']; ?>, <?php echo htmlspecialchars(json_encode($prov['nombre'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Panel Lateral Derecho Rojo: Nuevo Proveedor -->
    <aside class="side-panel-rojo">
        <h2 class="side-panel-title">+ Nuevo Proveedor</h2>

        <form action="proveedores.php" method="POST" class="side-panel-form">
            <div class="form-group-panel">
                <label>Nombre:</label>
                <input type="text" name="nombre" class="form-input-white" required>
            </div>

            <div class="form-group-panel">
                <label>Contacto:</label>
                <input type="text" name="contacto" class="form-input-white" required>
            </div>

            <div class="form-group-panel">
                <label>Telefono:</label>
                <input type="tel" name="telefono" class="form-input-white" required>
            </div>

            <div class="form-group-panel">
                <label>Correo:</label>
                <input type="email" name="correo" class="form-input-white" required>
            </div>

            <button type="submit" class="btn-guardar-panel">
                Guardar
            </button>
        </form>
    </aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($mensaje_alerta): ?>
        if (typeof KomixAlert !== 'undefined') KomixAlert.<?php echo $tipo_alerta; ?>('<?php echo addslashes($mensaje_alerta); ?>');
    <?php endif; ?>
});

function confirmarEliminarProveedor(id, nombre) {
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.confirm({
            titulo: '¿Eliminar proveedor?',
            mensaje: `¿Deseas eliminar a "${nombre}" del catálogo?`,
            tipo: 'danger',
            onConfirm: function () {
                window.location.href = `proveedores.php?eliminar=${id}`;
            }
        });
    } else if (confirm(`¿Eliminar a ${nombre}?`)) {
        window.location.href = `proveedores.php?eliminar=${id}`;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
