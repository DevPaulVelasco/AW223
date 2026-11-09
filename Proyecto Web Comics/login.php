<?php
/**
 * KomiX - Pantalla de Inicio de Sesión
 * Archivo: login.php (Página 1 del diseño)
 */
$error = '';
$mensaje = '';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['komix_mensaje_login'])) {
    $mensaje = $_SESSION['komix_mensaje_login'];
    unset($_SESSION['komix_mensaje_login']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($usuario) && !empty($password)) {
        $cuenta = $_SESSION['komix_cuentas_demo'][$usuario] ?? null;
        $_SESSION['komix_usuario'] = [
            'nombre' => $cuenta['nombre'] ?? $usuario,
            'correo' => $usuario,
            'rol' => $cuenta['rol'] ?? 'Admin'
        ];
        header('Location: reportes.php');
        exit;
    } else {
        $error = 'Por favor ingrese su usuario y contraseña';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KomiX - Inicio de Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="auth-wrapper">
        <!-- Lado Izquierdo: Ilustración de Superhéroes con Marca de Agua KomiX -->
        <div class="auth-banner">
            <img src="./images/komixLogin.png" alt="Superheroes KomiX" class="auth-banner-img" onerror="this.onerror=null; this.src='./images/komixLogin.png';">
            <div class="auth-banner-overlay">
                <!-- Logotipo KomiX superpuesto -->
            </div>
        </div>

        <!-- Lado Derecho: Formulario Rojo Carmesí -->
        <div class="auth-content">
            <div class="auth-box">
                <h1 class="auth-title">INICIO DE SESIÓN</h1>

                <?php if (!empty($mensaje)): ?>
                    <p style="color: #b7ffb7; margin-bottom: 15px; font-weight: 600;"><?php echo htmlspecialchars($mensaje); ?></p>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <p style="color: #ffcccc; margin-bottom: 15px; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <form action="login.php" method="POST" class="auth-form">
                    <input 
                        type="text" 
                        name="usuario" 
                        placeholder="USUARIO / CORREO" 
                        required 
                        class="auth-input"
                        autocomplete="username"
                        value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>"
                    >

                    <input 
                        type="password" 
                        name="password" 
                        placeholder="CONTRASEÑA" 
                        required 
                        class="auth-input"
                        autocomplete="current-password"
                    >

                    <button type="submit" class="auth-btn-primary">
                        Iniciar Sesion
                    </button>
                </form>

                <div class="auth-links">
                    <a href="recordar_contrasena.php" class="auth-link">Olvidaste tu contraseña ?</a>
                    <a href="crear_cuenta.php" class="auth-link">Crear cuenta</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>