<?php
/**
 * KomiX - Interfaz Independiente: Crear / Editar Artículo
 * Archivo: articulos_nuevo.php
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'articulos';
$titulo_pagina = 'KomiX - Crear Nuevo Artículo';

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

$articulo_en_edicion = null;

// Comprobar si se solicitó editar un artículo por GET
if (isset($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    if (isset($_SESSION['komix_articulos'][$id_editar])) {
        $articulo_en_edicion = $_SESSION['komix_articulos'][$id_editar];
        $titulo_pagina = 'KomiX - Editar Artículo';
    }
}

// Procesar guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'crear';
    $titulo = trim($_POST['nombre'] ?? '');
    $volumen = trim($_POST['volumen'] ?? 'Estándar');
    $categoria = trim($_POST['categoria'] ?? 'comic');
    $pc_raw = trim($_POST['precio_compra'] ?? '0');
    $pv_raw = trim($_POST['precio_venta'] ?? '0');
    $stock = (int)($_POST['stock_inicial'] ?? 0);

    $precio_compra = '$' . number_format((float)str_replace(['$', ','], '', $pc_raw), 2);
    $precio_venta = '$' . number_format((float)str_replace(['$', ','], '', $pv_raw), 2);

    if ($accion === 'editar') {
        $id_editar = (int)($_POST['id_articulo'] ?? 0);
        if (isset($_SESSION['komix_articulos'][$id_editar]) && !empty($titulo)) {
            $_SESSION['komix_articulos'][$id_editar]['titulo'] = $titulo;
            $_SESSION['komix_articulos'][$id_editar]['volumen'] = $volumen;
            $_SESSION['komix_articulos'][$id_editar]['categoria'] = $categoria;
            $_SESSION['komix_articulos'][$id_editar]['precio_compra'] = $precio_compra;
            $_SESSION['komix_articulos'][$id_editar]['precio_venta'] = $precio_venta;
            $_SESSION['komix_articulos'][$id_editar]['stock'] = $stock;
            header("Location: articulos.php?msg=" . urlencode("Los datos del artículo '{$titulo}' han sido actualizados con éxito.") . "&tipo=success");
            exit;
        }
    } else {
        // Crear nuevo artículo
        if (!empty($titulo)) {
            $nuevo_id = empty($_SESSION['komix_articulos']) ? 1 : max(array_keys($_SESSION['komix_articulos'])) + 1;
            $_SESSION['komix_articulos'][$nuevo_id] = [
                'id' => $nuevo_id,
                'titulo' => $titulo,
                'volumen' => $volumen,
                'categoria' => $categoria,
                'precio_compra' => $precio_compra,
                'precio_venta' => $precio_venta,
                'stock' => $stock
            ];
            header("Location: articulos.php?msg=" . urlencode("Artículo '{$titulo}' registrado exitosamente.") . "&tipo=success");
            exit;
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="content-header-row" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="articulos.php" class="btn-ticket-action" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; padding: 8px 16px;">
                <i class="fa-solid fa-arrow-left"></i> Volver a Artículos
            </a>
            <h1 class="page-title" style="margin: 0;">
                <?php echo $articulo_en_edicion ? 'Editar Artículo' : 'Crear Nuevo Artículo'; ?>
            </h1>
        </div>
    </div>

    <!-- Formulario Independiente en Tarjeta Carmesí -->
    <div class="standalone-form-container">
        <div class="standalone-form-card">
            <div class="standalone-form-header">
                <h2 class="standalone-form-title">
                    <?php echo $articulo_en_edicion ? 'Editar Artículo #' . $articulo_en_edicion['id'] : '+ Nuevo Artículo'; ?>
                </h2>
                <?php if ($articulo_en_edicion): ?>
                    <span class="badge-editando-indicador" style="background: rgba(0,0,0,0.3); border: 1px solid #fff;">
                        Modo Edición
                    </span>
                <?php endif; ?>
            </div>

            <p style="font-size: 0.88rem; color: #ffe6e6; margin-bottom: 20px;">
                <?php echo $articulo_en_edicion 
                    ? 'Modifique los datos de <strong>' . htmlspecialchars($articulo_en_edicion['titulo']) . '</strong> y guarde los cambios.' 
                    : 'Complete la información para registrar un nuevo producto o artículo en el inventario general.'; ?>
            </p>

            <form action="articulos_nuevo.php" method="POST" class="side-panel-form">
                <input type="hidden" name="accion" value="<?php echo $articulo_en_edicion ? 'editar' : 'crear'; ?>">
                <?php if ($articulo_en_edicion): ?>
                    <input type="hidden" name="id_articulo" value="<?php echo $articulo_en_edicion['id']; ?>">
                <?php endif; ?>

                <div class="form-group-panel">
                    <label>Nombre / Título del Artículo:</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        class="form-input-white" 
                        required 
                        placeholder="Ej: Figura Batman McFarlane DC"
                        value="<?php echo htmlspecialchars($articulo_en_edicion['titulo'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Volumen / Edición / Detalle:</label>
                    <input 
                        type="text" 
                        name="volumen" 
                        class="form-input-white" 
                        required 
                        placeholder="Ej: Serie 7 pulgadas o Edición Especial"
                        value="<?php echo htmlspecialchars($articulo_en_edicion['volumen'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Categoría:</label>
                    <select name="categoria" class="form-input-white" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23111\' stroke-width=\'2\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 14px center; background-size: 14px;">
                        <?php $cat_actual = $articulo_en_edicion['categoria'] ?? 'comic'; ?>
                        <option value="comic" <?php echo $cat_actual === 'comic' ? 'selected' : ''; ?>>Cómics y Novelas Gráficas</option>
                        <option value="figuras" <?php echo $cat_actual === 'figuras' ? 'selected' : ''; ?>>Figuras de Acción y Coleccionables</option>
                        <option value="merch" <?php echo $cat_actual === 'merch' ? 'selected' : ''; ?>>Merchandising y Ropa</option>
                        <option value="posters" <?php echo $cat_actual === 'posters' ? 'selected' : ''; ?>>Pósters y Decoración</option>
                    </select>
                </div>

                <div class="form-group-panel">
                    <label>Precio de Compra:</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="precio_compra" 
                        class="form-input-white" 
                        required 
                        placeholder="0.00"
                        value="<?php echo htmlspecialchars(str_replace(['$', ','], '', $articulo_en_edicion['precio_compra'] ?? '')); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Precio de Venta:</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="precio_venta" 
                        class="form-input-white" 
                        required 
                        placeholder="0.00"
                        value="<?php echo htmlspecialchars(str_replace(['$', ','], '', $articulo_en_edicion['precio_venta'] ?? '')); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Stock Inicial / Disponible:</label>
                    <input 
                        type="number" 
                        name="stock_inicial" 
                        class="form-input-white" 
                        required 
                        placeholder="10"
                        value="<?php echo htmlspecialchars($articulo_en_edicion['stock'] ?? '10'); ?>"
                    >
                </div>

                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button 
                        type="submit" 
                        class="btn-guardar-panel" 
                        style="flex: 1; background-color: #ffffff; color: #7b0808; font-weight: 900;"
                    >
                        <?php echo $articulo_en_edicion ? 'Actualizar Artículo' : 'Guardar Artículo'; ?>
                    </button>
                    <a 
                        href="articulos.php" 
                        class="btn-cancelar-edicion" 
                        style="flex: 1; text-align: center; display: flex; align-items: center; justify-content: center; text-decoration: none; margin-top: 0;"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
