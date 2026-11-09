<?php
/**
 * KomiX - Interfaz Independiente: Crear / Editar Proveedor
 * Archivo: proveedores_nuevo.php
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'proveedores';
$titulo_pagina = 'KomiX - Crear Nuevo Proveedor';

// Inicializar lista de proveedores en sesión si no existe
if (!isset($_SESSION['komix_proveedores'])) {
    $_SESSION['komix_proveedores'] = [
        1 => ['id' => 1, 'nombre' => 'Distribuidora Panini México', 'contacto' => 'Carlos Ramírez', 'telefono' => '+52 55 1234 5678', 'correo' => 'ventas@panini.com.mx'],
        2 => ['id' => 2, 'nombre' => 'Editorial Kamite Cómics', 'contacto' => 'Sofía Morales', 'telefono' => '+52 55 8765 4321', 'correo' => 'contacto@kamite.com.mx'],
        3 => ['id' => 3, 'nombre' => 'Diamond Comic Distributors', 'contacto' => 'John Gallagher', 'telefono' => '+1 443 318 8000', 'correo' => 'orders@diamondcomics.com'],
        4 => ['id' => 4, 'nombre' => 'Coleccionables Anime & Comics S.A.', 'contacto' => 'Mariana Torres', 'telefono' => '+52 33 9988 7766', 'correo' => 'mtorres@coleccionables.mx'],
    ];
}

$proveedor_en_edicion = null;

// Comprobar si se solicitó editar un proveedor por GET
if (isset($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    if (isset($_SESSION['komix_proveedores'][$id_editar])) {
        $proveedor_en_edicion = $_SESSION['komix_proveedores'][$id_editar];
        $titulo_pagina = 'KomiX - Editar Proveedor';
    }
}

// Procesar guardado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'crear';
    $nombre = trim($_POST['nombre'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    if ($accion === 'editar') {
        $id_editar = (int)($_POST['id_proveedor'] ?? 0);
        if (isset($_SESSION['komix_proveedores'][$id_editar]) && !empty($nombre) && !empty($contacto)) {
            $_SESSION['komix_proveedores'][$id_editar]['nombre'] = $nombre;
            $_SESSION['komix_proveedores'][$id_editar]['contacto'] = $contacto;
            $_SESSION['komix_proveedores'][$id_editar]['telefono'] = $telefono;
            $_SESSION['komix_proveedores'][$id_editar]['correo'] = $correo;
            header("Location: proveedores.php?msg=" . urlencode("Los datos del proveedor '{$nombre}' han sido actualizados con éxito.") . "&tipo=success");
            exit;
        }
    } else {
        // Crear nuevo proveedor
        if (!empty($nombre) && !empty($contacto)) {
            $nuevo_id = empty($_SESSION['komix_proveedores']) ? 1 : max(array_keys($_SESSION['komix_proveedores'])) + 1;
            $_SESSION['komix_proveedores'][$nuevo_id] = [
                'id' => $nuevo_id,
                'nombre' => $nombre,
                'contacto' => $contacto,
                'telefono' => $telefono,
                'correo' => $correo
            ];
            header("Location: proveedores.php?msg=" . urlencode("Proveedor '{$nombre}' registrado exitosamente.") . "&tipo=success");
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
            <a href="proveedores.php" class="btn-ticket-action" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; padding: 8px 16px;">
                <i class="fa-solid fa-arrow-left"></i> Volver a Proveedores
            </a>
            <h1 class="page-title" style="margin: 0;">
                <?php echo $proveedor_en_edicion ? 'Editar Proveedor' : 'Crear Nuevo Proveedor'; ?>
            </h1>
        </div>
    </div>

    <!-- Formulario Independiente en Tarjeta Carmesí -->
    <div class="standalone-form-container">
        <div class="standalone-form-card">
            <div class="standalone-form-header">
                <h2 class="standalone-form-title">
                    <?php echo $proveedor_en_edicion ? 'Editar Proveedor #' . $proveedor_en_edicion['id'] : '+ Nuevo Proveedor'; ?>
                </h2>
                <?php if ($proveedor_en_edicion): ?>
                    <span class="badge-editando-indicador" style="background: rgba(0,0,0,0.3); border: 1px solid #fff;">
                        Modo Edición
                    </span>
                <?php endif; ?>
            </div>

            <p style="font-size: 0.88rem; color: #ffe6e6; margin-bottom: 20px;">
                <?php echo $proveedor_en_edicion 
                    ? 'Modifique los datos de contacto y detalles de <strong>' . htmlspecialchars($proveedor_en_edicion['nombre']) . '</strong>.' 
                    : 'Complete los campos para registrar un nuevo proveedor o distribuidora en el catálogo.'; ?>
            </p>

            <form action="proveedores_nuevo.php" method="POST" class="side-panel-form">
                <input type="hidden" name="accion" value="<?php echo $proveedor_en_edicion ? 'editar' : 'crear'; ?>">
                <?php if ($proveedor_en_edicion): ?>
                    <input type="hidden" name="id_proveedor" value="<?php echo $proveedor_en_edicion['id']; ?>">
                <?php endif; ?>

                <div class="form-group-panel">
                    <label>Nombre de la Empresa / Editorial:</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        class="form-input-white" 
                        required 
                        placeholder="Ej: Editorial Panini México"
                        value="<?php echo htmlspecialchars($proveedor_en_edicion['nombre'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Persona de Contacto:</label>
                    <input 
                        type="text" 
                        name="contacto" 
                        class="form-input-white" 
                        required 
                        placeholder="Ej: Lic. Carlos Ramírez"
                        value="<?php echo htmlspecialchars($proveedor_en_edicion['contacto'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Teléfono:</label>
                    <input 
                        type="tel" 
                        name="telefono" 
                        class="form-input-white" 
                        required 
                        placeholder="+52 55 ..."
                        value="<?php echo htmlspecialchars($proveedor_en_edicion['telefono'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Correo Electrónico:</label>
                    <input 
                        type="email" 
                        name="correo" 
                        class="form-input-white" 
                        required 
                        placeholder="contacto@proveedor.com"
                        value="<?php echo htmlspecialchars($proveedor_en_edicion['correo'] ?? ''); ?>"
                    >
                </div>

                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button 
                        type="submit" 
                        class="btn-guardar-panel" 
                        style="flex: 1; background-color: #ffffff; color: #7b0808; font-weight: 900;"
                    >
                        <?php echo $proveedor_en_edicion ? 'Actualizar Proveedor' : 'Guardar Proveedor'; ?>
                    </button>
                    <a 
                        href="proveedores.php" 
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
