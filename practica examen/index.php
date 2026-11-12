<?php
// Procesa el formulario antes de enviar HTML al navegador.
// send.php define $mensaje y solo se conecta a la base al recibir un POST.
include "send.php";
?>
<!DOCTYPE html>
<!-- DOCTYPE indica al navegador que este documento usa HTML5. -->
<!-- El atributo lang indica que el contenido está en español. -->
<html lang="es">
<head>
    <!-- Metadatos básicos del documento y configuración para móviles. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Conexión anticipada a Google Fonts y carga de las fuentes usadas en la página. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cal+Sans&family=Fira+Sans:wght@400;500&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Hoja de estilos del proyecto. -->
    <link rel="stylesheet" href="style.css">
    <title>web</title>
</head>
<body>
    <!-- Encabezado y navegación principal de la página. -->
    <header class="header">
        <div class="menu container">
            <a href="#" class="logo">Mi Sitio Web</a>
            <input type="checkbox" id="menu"/>
            <label for="menu">
                <img src="./imagenes/lista.png" alt="menu" class="menu-icon">
            </label>
            <nav>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Acerca de</a></li>
                    <li><a href="#">Servicios</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>

        </div>
        <div class="header-content container">
            <div class="header-text">
                <h1>Medical Center</h1>
                <p>lore ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet consectetur, adipisicing elit. Sapiente, eius ab. Recusandae vitae odit ducimus modi excepturi nihil repellat quisquam, tempore voluptas, quo, temporibus animi nam deserunt quia nobis obcaecati?</p>
                <a href="#" class="btn-1">Informacion</a>

                </div>
                <div class="header-img">
                    <img src="imagenes/about.png" alt="about">
                </div>


            </div>


        </div>

    </header>

    <!-- Sección informativa sobre el sitio. -->
    <section class="about container">
        <div class="about-img">
            <img src="imagenes/about.png" alt="about">
        </div>
        <div class="about-text">
            <h2>Acerca de Nosotros</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet consectetur, adipisicing elit. Sapiente, eius ab. Recusandae vitae odit ducimus modi excepturi nihil repellat quisquam, tempore voluptas, quo, temporibus animi nam deserunt quia nobis obcaecati?</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet consectetur, adipisicing elit. Sapiente, eius ab. Recusandae vitae odit ducimus modi excepturi nihil repellat quisquam, tempore voluptas, quo, temporibus animi nam deserunt quia nobis obcaecati?</p>
            <a href="#" class="btn-1">Leer Más</a>
        </div>

    </section>
    
    <!-- Sección que presenta los servicios. -->
    <main class="servicios">
        <section class="servicios container">
            <h2>Nuestros Servicios</h2>
            <div class="servicios-content container">
                <div class="servicio-1">
                    <img src="imagenes/service1.png" alt="Servicio 1">
                    <h3>Servicio 1</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
                <div class="servicio-2">
                    <img src="imagenes/service2.png" alt="Servicio 2">
                    <h3>Servicio 2</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
                <div class="servicio-3">
                    <img src="imagenes/service3.png" alt="Servicio 3">
                    <h3>Servicio 3</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
                <div class="servicio-4">
                    <img src="imagenes/service4.png" alt="Servicio 4">
                    <h3>Servicio 4</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Formulario para crear una consulta nueva. -->
    <section class="formulario container">
        <form method="post" auto-complete="off">
            <h2>Agenda Consulta</h2>
            <!-- PHP imprime el mensaje de éxito o error preparado por send.php. -->
            <?php if ($mensaje !== ""): ?>
                <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8"); ?></p>
            <?php endif; ?>
            <div class="input-group">
                <div class="input-container">
                    <input type="text" placeholder="Nombre y apellido" name="nombre" required>
                    <label for=""></label>

                </div>
                <div class="input-container">
                    <input type="tel" placeholder="Número de teléfono" name="telefono" required>
                    <label for=""></label>

                </div>
                <div class="input-container">
                    <input type="email" placeholder="Correo electrónico" name="email" required>
                    <label for=""></label>

                </div>
                <div class="input-container">
                    <textarea placeholder="Detalles de consulta" name="message" required></textarea>
                    <label for=""></label>

                </div>
                <input type="submit" name="send" class="btn">

            </div>
        </form>
        <!-- El listado CRUD está en una página separada para no mezclarlo con el formulario. -->
        <p><a href="consultas.php">Ver consultas registradas</a></p>
    </section>

    <!-- Pie de página. -->
    <footer class="footer-content container">
        <div>
            <a href="#" class="logo">Logo</a>
        </div>
        <div class="link">
            <a href="#">Inicio</a>
            <a href="#">Acerca de</a>
            <a href="#">Servicios</a>
            <a href="#">Contacto</a>

        </div>
    </footer>
</body>
</html>