<?php
/**
 * KomiX - Pantalla de Recordar Contraseña
 * Archivo: recordar_contrasena.php (Página 2 del diseño)
 */
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    if (!empty($usuario)) {
        $mensaje = 'Se han enviado las instrucciones de recuperación a su correo.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KomiX - Recordar Contraseña</title>
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
                <h1 class="auth-title">RECORDAR CONTRASEÑA</h1>

                <?php if (!empty($mensaje)): ?>
                    <p style="background: rgba(0,0,0,0.3); padding: 10px 16px; border-radius: 20px; color: #b7ffb7; margin-bottom: 20px; font-weight: 600;">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </p>
                <?php endif; ?>

                <form action="recordar_contrasena.php" method="POST" class="auth-form">
                    <input 
                        type="text" 
                        name="usuario" 
                        placeholder="USUARIO / CORREO" 
                        required 
                        class="auth-input"
                    >

                    <button type="submit" class="auth-btn-primary">
                        Continuar
                    </button>
                </form>

                <div class="auth-links">
                    <a href="login.php" class="auth-link">Volver a Iniciar Sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
