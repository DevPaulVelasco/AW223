<?php
/**
 * KomiX - Interfaz Independiente: Crear / Editar Usuario
 * Archivo: usuarios_nuevo.php
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'usuario';
$titulo_pagina = 'KomiX - Crear Nuevo Usuario';

// Inicializar lista de usuarios en sesión si no existe
if (!isset($_SESSION['komix_usuarios'])) {
    $_SESSION['komix_usuarios'] = [
        1 => ['id' => 1, 'nombre' => 'Alberto Mendoza', 'correo' => 'mendoza@komix.com', 'rol' => 'Admin', 'activo' => true],
        2 => ['id' => 2, 'nombre' => 'Rodrigo García', 'correo' => 'rgarcia@komix.com', 'rol' => 'Vendedor', 'activo' => true],
        3 => ['id' => 3, 'nombre' => 'Lucía Pérez', 'correo' => 'lperez@komix.com', 'rol' => 'Vendedor', 'activo' => true],
        4 => ['id' => 4, 'nombre' => 'Carlos Morales', 'correo' => 'cmorales@komix.com', 'rol' => 'Inventario', 'activo' => false],
        5 => ['id' => 5, 'nombre' => 'Valeria Castillo', 'correo' => 'vcastillo@komix.com', 'rol' => 'Cajero', 'activo' => true],
    ];
}

$mensaje_alerta = null;
$tipo_alerta = 'info';
$usuario_en_edicion = null;

// Comprobar si se solicitó editar un usuario por GET
if (isset($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    if (isset($_SESSION['komix_usuarios'][$id_editar])) {
        $usuario_en_edicion = $_SESSION['komix_usuarios'][$id_editar];
        $titulo_pagina = 'KomiX - Editar Usuario';
    }
}

// Procesar guardado (creación o edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'crear';
    $nombre = trim($_POST['nombre'] ?? '');
    $rol = trim($_POST['rol'] ?? 'Vendedor');
    $correo = trim($_POST['correo'] ?? '');
    $activo = isset($_POST['activo']) ? true : false;

    if ($accion === 'editar') {
        $id_editar = (int)($_POST['id_usuario'] ?? 0);
        if (isset($_SESSION['komix_usuarios'][$id_editar]) && !empty($nombre) && !empty($correo)) {
            $_SESSION['komix_usuarios'][$id_editar]['nombre'] = $nombre;
            $_SESSION['komix_usuarios'][$id_editar]['rol'] = $rol;
            $_SESSION['komix_usuarios'][$id_editar]['correo'] = $correo;
            $_SESSION['komix_usuarios'][$id_editar]['activo'] = $activo;
            header("Location: usuarios.php?msg=" . urlencode("Los datos de '{$nombre}' han sido actualizados con éxito.") . "&tipo=success");
            exit;
        }
    } else {
        // Crear nuevo usuario
        if (!empty($nombre) && !empty($correo)) {
            $nuevo_id = empty($_SESSION['komix_usuarios']) ? 1 : max(array_keys($_SESSION['komix_usuarios'])) + 1;
            $_SESSION['komix_usuarios'][$nuevo_id] = [
                'id' => $nuevo_id,
                'nombre' => $nombre,
                'correo' => $correo,
                'rol' => $rol,
                'activo' => true
            ];
            header("Location: usuarios.php?msg=" . urlencode("Usuario '{$nombre}' registrado exitosamente.") . "&tipo=success");
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
            <a href="usuarios.php" class="btn-ticket-action" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; padding: 8px 16px;">
                <i class="fa-solid fa-arrow-left"></i> Volver a Usuarios
            </a>
            <h1 class="page-title" style="margin: 0;">
                <?php echo $usuario_en_edicion ? 'Editar Usuario' : 'Crear Nuevo Usuario'; ?>
            </h1>
        </div>
    </div>

    <!-- Formulario Independiente en Tarjeta Carmesí -->
    <div class="standalone-form-container">
        <div class="standalone-form-card">
            <div class="standalone-form-header">
                <h2 class="standalone-form-title">
                    <?php echo $usuario_en_edicion ? 'Editar Usuario #' . $usuario_en_edicion['id'] : '+ Nuevo Usuario'; ?>
                </h2>
                <?php if ($usuario_en_edicion): ?>
                    <span class="badge-editando-indicador" style="background: rgba(0,0,0,0.3); border: 1px solid #fff;">
                        Modo Edición
                    </span>
                <?php endif; ?>
            </div>

            <p style="font-size: 0.88rem; color: #ffe6e6; margin-bottom: 20px;">
                <?php echo $usuario_en_edicion 
                    ? 'Modifique los datos de <strong>' . htmlspecialchars($usuario_en_edicion['nombre']) . '</strong> y guarde los cambios.' 
                    : 'Complete los datos requeridos para registrar un nuevo usuario en la plataforma KomiX.'; ?>
            </p>

            <form action="usuarios_nuevo.php" method="POST" class="side-panel-form">
                <input type="hidden" name="accion" value="<?php echo $usuario_en_edicion ? 'editar' : 'crear'; ?>">
                <?php if ($usuario_en_edicion): ?>
                    <input type="hidden" name="id_usuario" value="<?php echo $usuario_en_edicion['id']; ?>">
                <?php endif; ?>

                <div class="form-group-panel">
                    <label>Nombre Completo:</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        class="form-input-white" 
                        required 
                        placeholder="Ej: Diana Prince"
                        value="<?php echo htmlspecialchars($usuario_en_edicion['nombre'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group-panel">
                    <label>Asignar Rol:</label>
                    <select name="rol" class="form-input-white" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23111\' stroke-width=\'2\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 14px center; background-size: 14px;">
                        <?php $rol_actual = $usuario_en_edicion['rol'] ?? 'Vendedor'; ?>
                        <option value="Admin" <?php echo $rol_actual === 'Admin' ? 'selected' : ''; ?>>Administrador General</option>
                        <option value="Vendedor" <?php echo $rol_actual === 'Vendedor' ? 'selected' : ''; ?>>Vendedor de Mostrador</option>
                        <option value="Inventario" <?php echo $rol_actual === 'Inventario' ? 'selected' : ''; ?>>Encargado de Almacén</option>
                        <option value="Cajero" <?php echo $rol_actual === 'Cajero' ? 'selected' : ''; ?>>Cajero POS</option>
                    </select>
                </div>

                <div class="form-group-panel">
                    <label>Correo Electrónico:</label>
                    <input 
                        type="email" 
                        name="correo" 
                        class="form-input-white" 
                        required 
                        placeholder="correo@komix.com"
                        value="<?php echo htmlspecialchars($usuario_en_edicion['correo'] ?? ''); ?>"
                    >
                </div>

                <?php if ($usuario_en_edicion): ?>
                    <div class="form-group-panel" style="flex-direction: row; align-items: center; justify-content: space-between; margin-top: 6px;">
                        <label style="margin-bottom: 0;">Estado Activo:</label>
                        <label class="switch-toggle">
                            <input type="checkbox" name="activo" value="1" <?php echo !empty($usuario_en_edicion['activo']) ? 'checked' : ''; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                <?php endif; ?>

                <div style="display: flex; gap: 12px; margin-top: 12px;">
                    <button 
                        type="submit" 
                        class="btn-guardar-panel" 
                        style="flex: 1; background-color: #ffffff; color: #7b0808; font-weight: 900;"
                    >
                        <?php echo $usuario_en_edicion ? 'Actualizar Usuario' : 'Guardar Usuario'; ?>
                    </button>
                    <a 
                        href="usuarios.php" 
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
