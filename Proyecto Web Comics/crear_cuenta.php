<?php
/**
 * KomiX - Pantalla de Crear Cuenta
 * Archivo: crear_cuenta.php (Página 3 del diseño)
 */
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $edad = trim($_POST['edad'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($usuario) && !empty($nombre) && !empty($password)) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['komix_cuentas_demo'][$usuario] = [
            'nombre' => $nombre,
            'correo' => $usuario,
            'edad' => $edad,
            'rol' => 'Vendedor'
        ];
        $_SESSION['komix_mensaje_login'] = 'Cuenta de demostración creada. Inicia sesión para continuar.';
        header('Location: login.php?registro=exito');
        exit;
    } else {
        $mensaje = 'Por favor complete todos los campos obligatorios.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KomiX - Crear Cuenta</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="auth-wrapper">
        <!-- Lado Izquierdo: Ilustración de Superhéroes con Marca de Agua KomiX -->
        <div class="auth-banner">
            <img src="./images/komixLogin.png" alt="Superheroes KomiX" class="auth-banner-img" onerror="this.onerror=null; this.src='./images/komixLogin.png';">
        </div>

        <!-- Lado Derecho: Formulario Rojo Carmesí -->
        <div class="auth-content">
            <div class="auth-box">
                <h1 class="auth-title">CREAR CUENTA</h1>

                <?php if (!empty($mensaje)): ?>
                    <p style="color: #ffcccc; margin-bottom: 15px; font-weight: 600;"><?php echo htmlspecialchars($mensaje); ?></p>
                <?php endif; ?>

                <form action="crear_cuenta.php" method="POST" class="auth-form">
                    <input 
                        type="text" 
                        name="usuario" 
                        placeholder="USUARIO / CORREO" 
                        required 
                        class="auth-input"
                    >

                    <input 
                        type="text" 
                        name="nombre" 
                        placeholder="NOMBRE" 
                        required 
                        class="auth-input"
                    >

                    <input 
                        type="number" 
                        name="edad" 
                        placeholder="EDAD" 
                        min="1" 
                        max="120" 
                        required 
                        class="auth-input"
                    >

                    <input 
                        type="password" 
                        name="password" 
                        placeholder="CONTRASEÑA" 
                        required 
                        class="auth-input"
                    >

                    <button type="submit" class="auth-btn-primary">
                        Crear cuenta
                    </button>
                </form>

                <div class="auth-links">
                    <a href="login.php" class="auth-link">¿Ya tienes una cuenta? Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
