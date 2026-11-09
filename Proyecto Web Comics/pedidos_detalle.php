<?php
/**
 * KomiX - Modal de Detalles de Pedido
 * Archivo: pedidos_detalle.php (Página 10 del diseño)
 */
require_once 'includes/auth.php';
$pagina_actual = 'pedidos';
$titulo_pagina = 'KomiX - Detalles de Pedido';

$detalles = [
    ['producto' => 'Spider-Man Vol. 1 #300', 'cantidad' => 20, 'precio_unit' => '$75.00', 'subtotal' => '$1,500.00'],
    ['producto' => 'Batman Year One Deluxe', 'cantidad' => 15, 'precio_unit' => '$90.00', 'subtotal' => '$1,350.00'],
    ['producto' => 'X-Men Days of Future Past', 'cantidad' => 10, 'precio_unit' => '$120.00', 'subtotal' => '$1,200.00']
];

if (empty($_SESSION['komix_pedidos'])) {
    header('Location: pedidos.php');
    exit;
}

$id_pedido = $_GET['id'] ?? array_key_first($_SESSION['komix_pedidos']);
if (!isset($_SESSION['komix_pedidos'][$id_pedido])) {
    header('Location: pedidos.php?msg=' . urlencode('No se encontró el pedido solicitado.') . '&tipo=warning');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'recibir') {
    $id_recibir = $_POST['id_pedido'] ?? '';
    if (isset($_SESSION['komix_pedidos'][$id_recibir]) && $_SESSION['komix_pedidos'][$id_recibir]['estado'] !== 'verde') {
        $_SESSION['komix_pedidos'][$id_recibir]['estado'] = 'verde';
        $_SESSION['komix_entradas_inventario'][$id_recibir] = $detalles;
        foreach ($_SESSION['komix_entradas_inventario'][$id_recibir] as &$producto) {
            $producto['nombre'] = $producto['producto'];
            $producto['tipo'] = 'Comic';
            $producto['stock_actual'] = $producto['cantidad'];
            $producto['stock_minimo'] = 5;
            $producto['estado'] = 'verde';
            unset($producto['producto'], $producto['cantidad'], $producto['precio_unit'], $producto['subtotal']);
        }
        unset($producto);
        header('Location: inventario.php?msg=' . urlencode('Pedido recibido e ingresado al inventario.') . '&tipo=success');
        exit;
    }

    header('Location: pedidos.php?msg=' . urlencode('Este pedido ya fue recibido o no está disponible.') . '&tipo=warning');
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="content-header-row">
        <h1 class="page-title">Pedidos</h1>
        <a href="pedidos_nuevo.php" class="btn-nuevo-rojo">+ Crear pedido</a>
    </div>

    <!-- Tabla de Fondo -->
    <div class="komix-table-container" style="opacity: 0.6; pointer-events: none;">
        <div class="table-header-row" style="grid-template-columns: 1.2fr 2fr 1.5fr 100px 1.5fr 120px;">
            <div>ID pedido:</div>
            <div>Proveedor:</div>
            <div>Fecha emision:</div>
            <div style="text-align: center;">Estado:</div>
            <div>Costo total:</div>
            <div style="text-align: right;">Acciones:</div>
        </div>

        <div class="table-data-row" style="grid-template-columns: 1.2fr 2fr 1.5fr 100px 1.5fr 120px;">
            <div style="font-weight: 700;"><?php echo htmlspecialchars($_SESSION['komix_pedidos'][$id_pedido]['id']); ?></div>
            <div><?php echo htmlspecialchars($_SESSION['komix_pedidos'][$id_pedido]['proveedor']); ?></div>
            <div style="color: #555;"><?php echo htmlspecialchars($_SESSION['komix_pedidos'][$id_pedido]['fecha']); ?></div>
            <div style="text-align: center;"><span class="badge-estado <?php echo htmlspecialchars($_SESSION['komix_pedidos'][$id_pedido]['estado']); ?>"></span></div>
            <div style="font-weight: 700;"><?php echo htmlspecialchars($_SESSION['komix_pedidos'][$id_pedido]['costo']); ?></div>
            <div class="table-actions" style="justify-content: flex-end;">
                <button class="btn-action-icon"><i class="fa-solid fa-eye"></i></button>
            </div>
        </div>
    </div>

    <!-- MODAL POPUP: DETALLES DE PEDIDO (Página 10) -->
    <div class="modal-overlay">
        <div class="modal-box">
            <h2 class="modal-title">Detalles de <?php echo htmlspecialchars($id_pedido); ?></h2>

            <table class="modal-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad solicitada</th>
                        <th>Precio.Unit</th>
                        <th>Sub.T</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $item): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($item['producto']); ?></td>
                            <td style="text-align: center;"><?php echo $item['cantidad']; ?></td>
                            <td><?php echo $item['precio_unit']; ?></td>
                            <td style="font-weight: 700;"><?php echo $item['subtotal']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="modal-actions">
                <a href="pedidos.php" class="btn-modal-cerrar">cerrar</a>
                <?php if ($_SESSION['komix_pedidos'][$id_pedido]['estado'] === 'verde'): ?>
                    <button class="btn-modal-ingresar" type="button" disabled>Pedido recibido</button>
                <?php else: ?>
                    <form method="POST" action="pedidos_detalle.php?id=<?php echo urlencode($id_pedido); ?>">
                        <input type="hidden" name="accion" value="recibir">
                        <input type="hidden" name="id_pedido" value="<?php echo htmlspecialchars($id_pedido); ?>">
                        <button class="btn-modal-ingresar" type="submit">Ingresar a inventario</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
