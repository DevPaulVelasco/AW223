<?php
/**
 * KomiX - Punto de Venta (POS) y Ticket
 * Archivo: ventas.php (Página 13 del diseño)
 * Funcionalidad: Catálogo de cómics, ticket con eliminación de productos confirmada por JS
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'ventas';
$titulo_pagina = 'KomiX - Punto de Venta';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'cobrar') {
    $items = json_decode($_POST['items_json'] ?? '[]', true);
    if (is_array($items) && count($items) > 0) {
        if (!isset($_SESSION['komix_ventas'])) {
            $_SESSION['komix_ventas'] = [];
        }

        $numero_folio = 980;
        foreach (array_keys($_SESSION['komix_ventas']) as $folio_existente) {
            if (preg_match('/^TK-(\d+)$/', $folio_existente, $coincidencia)) {
                $numero_folio = max($numero_folio, (int)$coincidencia[1]);
            }
        }
        $folio = 'TK-' . str_pad((string)($numero_folio + 1), 5, '0', STR_PAD_LEFT);
        $total_venta = 0;
        foreach ($items as $item) {
            $total_venta += (float)($item['precio'] ?? 0);
        }

        $_SESSION['komix_ventas'][$folio] = [
            'id' => (string)(count($_SESSION['komix_ventas']) + 1),
            'folio' => $folio,
            'fecha' => date('d/m/Y'),
            'cliente' => 'Venta de mostrador',
            'vendedor' => $_SESSION['komix_usuario']['nombre'] ?? 'Usuario',
            'estado' => 'verde',
            'total' => '$' . number_format($total_venta, 2)
        ];
        header('Location: historial.php?msg=' . urlencode('Venta ' . $folio . ' registrada correctamente.') . '&tipo=success');
        exit;
    }

    header('Location: ventas.php?msg=' . urlencode('Agrega al menos un producto antes de cobrar.') . '&tipo=warning');
    exit;
}

$comics_catalogo = [
    [
        'id' => 1,
        'titulo' => 'Classic Comic Book Action',
        'precio' => 150.00,
        'precio_formato' => '$150.00',
        'imagen' => './images/comic_action.jpg'
    ],
    [
        'id' => 2,
        'titulo' => 'The Incredible Hulk vs Wolverine',
        'precio' => 280.00,
        'precio_formato' => '$280.00',
        'imagen' => './images/comic_hulk.jpg'
    ],
    [
        'id' => 3,
        'titulo' => 'X-Men: Mutant Genesis #1',
        'precio' => 220.00,
        'precio_formato' => '$220.00',
        'imagen' => './images/comic_xmen.jpg'
    ],
    [
        'id' => 4,
        'titulo' => 'Justice League: New 52 #1',
        'precio' => 195.00,
        'precio_formato' => '$195.00',
        'imagen' => './images/comic_justice.jpg'
    ]
];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="layout-con-panel-derecho">
    <!-- Columna Principal: Catálogo de Venta -->
    <main class="main-column">
        <div class="content-header-row" style="margin-bottom: 20px;">
            <h1 class="page-title">Catalogo</h1>
            <div class="busqueda-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="search" class="input-busqueda-pill" placeholder="Buscador" id="buscador-comics" aria-label="Buscar cómics">
            </div>
        </div>

        <div class="catalogo-grid" id="catalogo-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <?php foreach ($comics_catalogo as $comic): ?>
                <div class="comic-card" onclick="agregarAlTicket(<?php echo htmlspecialchars(json_encode($comic['titulo'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>, <?php echo $comic['precio']; ?>)">
                    <img 
                        src="<?php echo htmlspecialchars($comic['imagen']); ?>" 
                        alt="<?php echo htmlspecialchars($comic['titulo']); ?>" 
                        class="comic-cover-img"
                        onerror="this.onerror=null; this.src='assets/images/comic_action.png';"
                    >
                    <div class="comic-card-info">
                        <span class="comic-card-title"><?php echo htmlspecialchars($comic['titulo']); ?></span>
                        <span class="comic-card-price"><?php echo htmlspecialchars($comic['precio_formato']); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 24px;">
            <a href="historial.php" class="btn-ticket-action" style="display: inline-block; padding: 10px 24px; text-decoration: none; width: auto; font-size: 0.9rem;">
                Ver historial de tickets
            </a>
        </div>
    </main>

    <!-- Panel Lateral Derecho: Ticket de Venta -->
    <aside class="ticket-panel">
        <h2 style="font-size: 1.45rem; font-weight: 800; text-align: center; color: #fff;">Ticket</h2>

        <!-- Encabezado del Ticket -->
        <div class="ticket-header-box">
            <div><strong>Folio:</strong> TK-10294</div>
            <div><strong>Fecha:</strong> <?php echo date('d/m/Y'); ?></div>
            <div><strong>Hora:</strong> <?php echo date('H:i'); ?></div>
            <div><strong>Atendio:</strong> Mendoza A.</div>
        </div>

        <!-- Líneas de Venta Interactiva -->
        <div class="ticket-lineas" id="contenedor-lineas-ticket">
            <div style="display: flex; justify-content: space-between; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 4px;">
                <span>CANT:</span>
                <span>DESCRIPCION:</span>
                <span>IMPORTE:</span>
                <span></span>
            </div>
            
            <div id="lista-items-ticket" style="display: flex; flex-direction: column; gap: 8px;">
                <div class="ticket-item-row" id="item-ticket-1" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>1</span>
                    <span style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">The Incredible Hulk #1</span>
                    <span>$280.00</span>
                    <button type="button" onclick="confirmarEliminarItemTicket(1, 'The Incredible Hulk #1')" style="background: transparent; color: #ff8888; cursor: pointer; padding: 0 4px;" title="Quitar del ticket">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
                <div class="ticket-item-row" id="item-ticket-2" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>2</span>
                    <span style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Classic Comic Book</span>
                    <span>$300.00</span>
                    <button type="button" onclick="confirmarEliminarItemTicket(2, 'Classic Comic Book')" style="background: transparent; color: #ff8888; cursor: pointer; padding: 0 4px;" title="Quitar del ticket">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Total y Métodos de Pago -->
        <div class="ticket-pago-box">
            <div id="ticket-total-display" style="font-weight: 800; font-size: 0.95rem; margin-bottom: 6px;">
                Modal de cambio - Total: $580.00
            </div>
            <div id="ticket-cambio-display" style="font-size: 0.85rem; color: #444;">
                Efectivo recibido: $600.00 | Cambio: $20.00
            </div>
            <div class="payment-methods-row">
                <span class="payment-badge" style="color: #1a1f71;"><i class="fa-brands fa-cc-visa"></i> VISA</span>
                <span class="payment-badge" style="color: #eb001b;"><i class="fa-brands fa-cc-mastercard"></i> MC</span>
                <span class="payment-badge" style="color: #003087;"><i class="fa-brands fa-paypal"></i> PayPal</span>
                <span class="payment-badge" style="color: #000;"><i class="fa-brands fa-apple-pay"></i> Pay</span>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="ticket-actions-row">
            <button type="button" class="btn-ticket-action" onclick="window.print()">Imprimir</button>
            <button type="button" class="btn-ticket-action" onclick="descargarTicket()">Descargar</button>
            <button type="button" class="btn-ticket-action" onclick="nuevoPedidoTicket()">Nuevo Pedido</button>
            <button type="button" class="btn-ticket-action" onclick="finalizarVenta()">Cobrar venta</button>
        </div>
    </aside>
</div>

<form id="form-cobro" method="POST" action="ventas.php" hidden>
    <input type="hidden" name="accion" value="cobrar">
    <input type="hidden" name="items_json" id="items-json">
</form>

<script>
let ticketItems = [
    { id: 1, titulo: 'The Incredible Hulk #1', precio: 280 },
    { id: 2, titulo: 'Classic Comic Book', precio: 300 }
];
let siguienteIdTicket = 3;
let totalActual = 0;

function agregarAlTicket(titulo, precio) {
    const idItem = siguienteIdTicket;
    ticketItems.push({ id: idItem, titulo: titulo, precio: precio });
    const lista = document.getElementById('lista-items-ticket');
    if (ticketItems.length === 1) lista.innerHTML = '';
    const item = document.createElement('div');
    item.className = 'ticket-item-row';
    item.id = `item-ticket-${idItem}`;
    item.style.cssText = 'display: flex; justify-content: space-between; align-items: center;';

    const cantidad = document.createElement('span');
    cantidad.textContent = ticketItems.length;
    const descripcion = document.createElement('span');
    descripcion.style.cssText = 'max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
    descripcion.textContent = titulo;
    const importe = document.createElement('span');
    importe.textContent = `$${precio.toFixed(2)}`;
    const quitar = document.createElement('button');
    quitar.type = 'button';
    quitar.title = 'Quitar del ticket';
    quitar.style.cssText = 'background: transparent; color: #ff8888; cursor: pointer; padding: 0 4px;';
    quitar.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
    quitar.addEventListener('click', function () {
        confirmarEliminarItemTicket(idItem, titulo);
    });

    item.append(cantidad, descripcion, importe, quitar);
    lista.appendChild(item);
    siguienteIdTicket++;
    actualizarDisplays();
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.success(`"${titulo}" agregado al ticket.`);
    }
}

/**
 * Eliminación interactiva con modal de confirmación en el ticket
 */
function confirmarEliminarItemTicket(id, titulo) {
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.confirm({
            titulo: '¿Quitar Producto del Ticket?',
            mensaje: `¿Deseas eliminar <strong>"${titulo}"</strong> del ticket actual?`,
            tipo: 'danger',
            confirmText: 'Sí, quitar',
            cancelText: 'Cancelar',
            onConfirm: function () {
                const elem = document.getElementById(`item-ticket-${id}`);
                if (elem) elem.remove();
                ticketItems = ticketItems.filter(function (item) {
                    return item.id !== id;
                });
                actualizarDisplays();
                KomixAlert.success(`"${titulo}" removido del ticket.`);
            }
        });
    } else {
        if (confirm(`¿Quitar "${titulo}" del ticket?`)) {
            const elem = document.getElementById(`item-ticket-${id}`);
            if (elem) elem.remove();
            ticketItems = ticketItems.filter(function (item) {
                return item.id !== id;
            });
            actualizarDisplays();
        }
    }
}

function actualizarDisplays() {
    totalActual = ticketItems.reduce(function (total, item) {
        return total + item.precio;
    }, 0);
    document.querySelectorAll('.ticket-item-row').forEach(function (fila, indice) {
        const cantidad = fila.querySelector('span');
        if (cantidad) cantidad.textContent = indice + 1;
    });
    const totalElem = document.getElementById('ticket-total-display');
    const cambioElem = document.getElementById('ticket-cambio-display');
    if (totalElem) {
        totalElem.textContent = `Modal de cambio - Total: $${totalActual.toFixed(2)}`;
    }
    if (cambioElem) {
        const recibido = Math.max(totalActual, 600);
        const cambio = Math.max(0, recibido - totalActual);
        cambioElem.textContent = `Efectivo recibido: $${recibido.toFixed(2)} | Cambio: $${cambio.toFixed(2)}`;
    }
}

function descargarTicket() {
    const lineas = ['Ticket KomiX', '--------------------'];
    ticketItems.forEach(function (item) {
        lineas.push(`${item.titulo} - $${item.precio.toFixed(2)}`);
    });
    lineas.push(`Total: $${totalActual.toFixed(2)}`);
    const archivo = new Blob([lineas.join('\n')], { type: 'text/plain' });
    const enlace = document.createElement('a');
    enlace.href = URL.createObjectURL(archivo);
    enlace.download = 'ticket-komix.txt';
    document.body.appendChild(enlace);
    enlace.click();
    setTimeout(function () {
        enlace.remove();
        URL.revokeObjectURL(enlace.href);
    }, 1000);
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.success('Ticket listo para descargar como ticket-komix.txt.');
    }
}

function nuevoPedidoTicket() {
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.confirm({
            titulo: '¿Iniciar Nuevo Pedido?',
            mensaje: '¿Deseas limpiar el ticket actual para comenzar una nueva venta?',
            tipo: 'warning',
            confirmText: 'Sí, nuevo ticket',
            cancelText: 'Continuar actual',
            onConfirm: function () {
                const lista = document.getElementById('lista-items-ticket');
                if (lista) lista.innerHTML = '<div style="color: #ddd; font-style: italic; font-size: 0.8rem; padding: 4px 0;">Ticket vacío. Selecciona cómics para agregar.</div>';
                ticketItems = [];
                actualizarDisplays();
                KomixAlert.success('Nuevo ticket listo para agregar productos.');
            }
        });
    } else if (confirm('¿Limpiar el ticket actual?')) {
        ticketItems = [];
        document.getElementById('lista-items-ticket').innerHTML = '';
        actualizarDisplays();
    }
}

function finalizarVenta() {
    if (!ticketItems.length) {
        if (typeof KomixAlert !== 'undefined') KomixAlert.warning('Agrega al menos un producto antes de cobrar.');
        return;
    }
    document.getElementById('items-json').value = JSON.stringify(ticketItems);
    document.getElementById('form-cobro').submit();
}

actualizarDisplays();
</script>

<?php include 'includes/footer.php'; ?>
