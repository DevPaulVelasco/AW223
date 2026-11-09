<?php
/**
 * KomiX - Gestión de Usuarios
 * Archivo: usuarios.php (Página 16 del diseño)
 * Funcionalidad: Listado general de usuarios con eliminación real JS y enlaces a la interfaz independiente de creación/edición
 */
require_once 'includes/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = 'usuario';
$titulo_pagina = 'KomiX - Gestión de Usuarios';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'estado') {
    $id_estado = (int)($_POST['id_usuario'] ?? 0);
    if (isset($_SESSION['komix_usuarios'][$id_estado])) {
        $_SESSION['komix_usuarios'][$id_estado]['activo'] = isset($_POST['activo']);
        header('Location: usuarios.php?msg=' . urlencode('Estado del usuario actualizado.') . '&tipo=success');
        exit;
    }
}

$mensaje_alerta = $_GET['msg'] ?? null;
$tipo_alerta = $_GET['tipo'] ?? 'info';

// Procesar eliminación de usuario
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    if (isset($_SESSION['komix_usuarios'][$id_eliminar])) {
        $nombre_eliminado = $_SESSION['komix_usuarios'][$id_eliminar]['nombre'];
        unset($_SESSION['komix_usuarios'][$id_eliminar]);
        $mensaje_alerta = "El usuario '{$nombre_eliminado}' ha sido eliminado exitosamente.";
        $tipo_alerta = 'success';
    }
}

$usuarios = $_SESSION['komix_usuarios'];
$usuarios_activos = count(array_filter($usuarios, function ($usuario) {
    return !empty($usuario['activo']);
}));

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="content-header-row" style="margin-bottom: 24px;">
        <h1 class="page-title">Gestion de usuarios</h1>
        <div style="display: flex; align-items: center; gap: 14px;">
            <span style="font-size: 0.9rem; color: #444; font-weight: 600;">
                Total usuarios activos: <strong><?php echo $usuarios_activos; ?></strong>
            </span>
            <!-- Botón hacia la interfaz independiente de Crear Nuevo Usuario -->
            <a href="usuarios_nuevo.php" class="btn-nuevo-rojo" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-plus"></i> Crear nuevo usuario
            </a>
        </div>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="filter-bar-row">
        <div class="busqueda-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" class="input-busqueda-pill" placeholder="Buscar usuario por nombre o correo">
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="komix-table-container" id="tabla-usuarios">
        <div class="table-header-row" style="grid-template-columns: 1.8fr 2.2fr 1.5fr 1fr 100px;">
            <div>Nombre:</div>
            <div>Correo:</div>
            <div style="text-align: center;">Rol:</div>
            <div style="text-align: center;">Estado:</div>
            <div style="text-align: right;">Acciones:</div>
        </div>

        <?php if (empty($usuarios)): ?>
            <div class="table-data-row" style="text-align: center; display: block; padding: 28px;">
                <p style="color: #666; font-size: 0.95rem;">No hay usuarios registrados actualmente.</p>
            </div>
        <?php else: ?>
            <?php foreach ($usuarios as $u): ?>
                <div 
                    class="table-data-row" 
                    id="fila-usuario-<?php echo $u['id']; ?>"
                    style="grid-template-columns: 1.8fr 2.2fr 1.5fr 1fr 100px;"
                >
                    <div style="font-weight: 700;">
                        <?php echo htmlspecialchars($u['nombre']); ?>
                    </div>
                    <div style="color: #444;"><?php echo htmlspecialchars($u['correo']); ?></div>
                    <div style="text-align: center;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: #7b0808; background: #ffeaea; padding: 4px 12px; border-radius: var(--radio-pill);">
                            <?php echo htmlspecialchars($u['rol']); ?>
                        </span>
                    </div>
                    <div style="text-align: center;">
                        <form method="POST" action="usuarios.php" style="display: inline-flex;">
                            <input type="hidden" name="accion" value="estado">
                            <input type="hidden" name="id_usuario" value="<?php echo $u['id']; ?>">
                            <label class="switch-toggle">
                                <input type="checkbox" name="activo" value="1" <?php echo $u['activo'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <span class="toggle-slider"></span>
                            </label>
                        </form>
                    </div>
                    <div class="table-actions" style="justify-content: flex-end;">
                        <!-- Botón Editar: dirige a la interfaz independiente usuarios_nuevo.php?editar=ID -->
                        <a 
                            href="usuarios_nuevo.php?editar=<?php echo $u['id']; ?>" 
                            class="btn-action-icon" 
                            title="Editar usuario <?php echo htmlspecialchars($u['nombre']); ?>"
                            style="text-decoration: none; display: flex; align-items: center; justify-content: center;"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </a>

                        <!-- Botón Eliminar con confirmación modal JS -->
                        <button 
                            type="button" 
                            class="btn-action-icon" 
                            title="Eliminar usuario"
                            onclick="confirmarEliminarUsuario(<?php echo $u['id']; ?>, <?php echo htmlspecialchars(json_encode($u['nombre'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)"
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
 * Confirmar eliminación de usuario mediante JS/alertas.js
 */
function confirmarEliminarUsuario(id, nombre) {
    if (typeof KomixAlert !== 'undefined') {
        KomixAlert.confirm({
            titulo: '¿Eliminar Usuario?',
            mensaje: `¿Estás seguro de que deseas eliminar permanentemente al usuario <strong>"${nombre}"</strong> del sistema KomiX? Esta acción no se puede deshacer.`,
            tipo: 'danger',
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            onConfirm: function () {
                window.location.href = `usuarios.php?eliminar=${id}`;
            }
        });
    } else {
        if (confirm(`¿Estás seguro de eliminar al usuario ${nombre}?`)) {
            window.location.href = `usuarios.php?eliminar=${id}`;
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>
