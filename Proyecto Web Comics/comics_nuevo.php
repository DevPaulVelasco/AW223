<?php
/**
 * KomiX - Interfaz Independiente: Crear / Editar Cómic
 * Archivo: comics_nuevo.php
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'comics';
$titulo_pagina = 'KomiX - Crear Nuevo Cómic';

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

$comic_en_edicion = null;

// Comprobar si se solicitó editar un cómic por GET
if (isset($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    if (isset($_SESSION['komix_comics'][$id_editar])) {
        $comic_en_edicion = $_SESSION['komix_comics'][$id_editar];
        $titulo_pagina = 'KomiX - Editar Cómic';
    }
}

// Procesar guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'crear';
    $titulo = trim($_POST['titulo'] ?? '');
    $editorial = trim($_POST['editorial'] ?? 'DC Comics');
    $volumen = trim($_POST['volumen'] ?? 'Vol. 1');
    $ano = trim($_POST['ano'] ?? date('Y'));
    $pc_raw = trim($_POST['precio_compra'] ?? '0');
    $pv_raw = trim($_POST['precio_venta'] ?? '0');

    $precio_compra = '$' . number_format((float)str_replace(['$', ','], '', $pc_raw), 2);
    $precio_venta = '$' . number_format((float)str_replace(['$', ','], '', $pv_raw), 2);

    if ($accion === 'editar') {
        $id_editar = (int)($_POST['id_comic'] ?? 0);
        if (isset($_SESSION['komix_comics'][$id_editar]) && !empty($titulo)) {
            $_SESSION['komix_comics'][$id_editar]['titulo'] = $titulo;
            $_SESSION['komix_comics'][$id_editar]['editorial'] = $editorial;
            $_SESSION['komix_comics'][$id_editar]['volumen'] = $volumen;
            $_SESSION['komix_comics'][$id_editar]['ano'] = $ano;
            $_SESSION['komix_comics'][$id_editar]['precio_compra'] = $precio_compra;
            $_SESSION['komix_comics'][$id_editar]['precio_venta'] = $precio_venta;
            header("Location: comics.php?msg=" . urlencode("Los datos del cómic '{$titulo}' han sido actualizados con éxito.") . "&tipo=success");
            exit;
        }
    } else {
        // Crear nuevo cómic
        if (!empty($titulo)) {
            $nuevo_id = empty($_SESSION['komix_comics']) ? 1 : max(array_keys($_SESSION['komix_comics'])) + 1;
            $_SESSION['komix_comics'][$nuevo_id] = [
                'id' => $nuevo_id,
                'titulo' => $titulo,
                'editorial' => $editorial,
                'volumen' => $volumen,
                'ano' => $ano,
                'precio_compra' => $precio_compra,
                'precio_venta' => $precio_venta
            ];
            header("Location: comics.php?msg=" . urlencode("Cómic '{$titulo}' registrado exitosamente.") . "&tipo=success");
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
            <a href="comics.php" class="btn-ticket-action" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; padding: 8px 16px;">
                <i class="fa-solid fa-arrow-left"></i> Volver a Cómics
            </a>
            <h1 class="page-title" style="margin: 0;">
                <?php echo $comic_en_edicion ? 'Editar Cómic' : 'Crear Nuevo Cómic'; ?>
            </h1>
        </div>
    </div>

    <!-- Formulario Independiente en Tarjeta Carmesí -->
    <div class="standalone-form-container">
        <div class="standalone-form-card">
            <div class="standalone-form-header">
                <h2 class="standalone-form-title">
                    <?php echo $comic_en_edicion ? 'Editar Cómic #' . $comic_en_edicion['id'] : '+ Nuevo Cómic'; ?>
                </h2>
                <?php if ($comic_en_edicion): ?>
                    <span class="badge-editando-indicador" style="background: rgba(0,0,0,0.3); border: 1px solid #fff;">
                        Modo Edición
                    </span>
                <?php endif; ?>
            </div>

            <p style="font-size: 0.88rem; color: #ffe6e6; margin-bottom: 20px;">
                <?php echo $comic_en_edicion 
                    ? 'Modifique la información de <strong>' . htmlspecialchars($comic_en_edicion['titulo']) . '</strong> y guarde los cambios.' 
                    : 'Complete la ficha técnica para registrar un nuevo ejemplar o serie de cómics.'; ?>
            </p>

            <form action="comics_nuevo.php" method="POST" class="side-panel-form">
                <input type="hidden" name="accion" value="<?php echo $comic_en_edicion ? 'editar' : 'crear'; ?>">
                <?php if ($comic_en_edicion): ?>
                    <input type="hidden" name="id_comic" value="<?php echo $comic_en_edicion['id']; ?>">
                <?php endif; ?>

                <div class="form-group-panel">
                    <label>Título del Cómic:</label>
                    <input 
                        type="text" 
                        name="titulo" 
                        class="form-input-white" 
                        required 
                        placeholder="Ej: Batman: The Long Halloween"
                        value="<?php echo htmlspecialchars($comic_en_edicion['titulo'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Editorial:</label>
                    <select name="editorial" class="form-input-white" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23111\' stroke-width=\'2\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 14px center; background-size: 14px;">
                        <?php $ed_actual = $comic_en_edicion['editorial'] ?? 'DC Comics'; ?>
                        <option value="DC Comics" <?php echo $ed_actual === 'DC Comics' ? 'selected' : ''; ?>>DC Comics</option>
                        <option value="Marvel Comics" <?php echo $ed_actual === 'Marvel Comics' ? 'selected' : ''; ?>>Marvel Comics</option>
                        <option value="Image Comics" <?php echo $ed_actual === 'Image Comics' ? 'selected' : ''; ?>>Image Comics</option>
                        <option value="Kamite" <?php echo $ed_actual === 'Kamite' ? 'selected' : ''; ?>>Editorial Kamite</option>
                        <option value="Panini Cómics" <?php echo $ed_actual === 'Panini Cómics' ? 'selected' : ''; ?>>Panini Cómics</option>
                    </select>
                </div>

                <div class="form-group-panel">
                    <label>Volumen / Edición / Número:</label>
                    <input 
                        type="text" 
                        name="volumen" 
                        class="form-input-white" 
                        required 
                        placeholder="Ej: Vol. 1 #01 o Tomo Único"
                        value="<?php echo htmlspecialchars($comic_en_edicion['volumen'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Año de Publicación:</label>
                    <input 
                        type="text" 
                        name="ano" 
                        class="form-input-white" 
                        required 
                        placeholder="2024"
                        value="<?php echo htmlspecialchars($comic_en_edicion['ano'] ?? date('Y')); ?>"
                    >
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
                        value="<?php echo htmlspecialchars(str_replace(['$', ','], '', $comic_en_edicion['precio_compra'] ?? '')); ?>"
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
                        value="<?php echo htmlspecialchars(str_replace(['$', ','], '', $comic_en_edicion['precio_venta'] ?? '')); ?>"
                    >
                </div>

                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button 
                        type="submit" 
                        class="btn-guardar-panel" 
                        style="flex: 1; background-color: #ffffff; color: #7b0808; font-weight: 900;"
                    >
                        <?php echo $comic_en_edicion ? 'Actualizar Cómic' : 'Guardar Cómic'; ?>
                    </button>
                    <a 
                        href="comics.php" 
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
