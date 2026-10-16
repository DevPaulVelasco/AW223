<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portafolio de Paul</title>
    <link rel="stylesheet" href="../Css/style.css">
</head>
<body>
    <header class="top-banner">
        <div class="banner-content">
            <p class="banner-tag">Desarrollo web</p>
            <h1>Mi Portafolio</h1>
            <button id="cambiar-bienvenida" type="button">Cambiar bienvenida</button>
        </div>
    </header>

    <nav aria-label="Navegación principal">
        <a href="#inicio">Inicio</a>
        <a href="#sobre-mi">Sobre mí</a>
        <a href="#habilidades">Habilidades</a>
        <a href="#proyectos">Proyectos</a>
        <a href="#contacto">Contacto</a>
        <a href="admin.php">Admin</a>
    </nav>

    <main id="inicio">
        <section class="seccion-inicio">
            <div class="seccion-contenido">
                <h1 id="titulo-principal">Hola, soy Paul</h1>
                <p>Soy un desarrollador web.</p>
                <a class="boton" href="#proyectos">Ver mis proyectos</a>
            </div>
        </section>

        <section  id="sobre-mi">
            <h2 id="sobre-mi-titulo">Sobre mí</h2>

            <figure class="sobre-mi-contenido">
                <img src="./foto-de-perfil.jpg" alt="mi portafolio">
                <figcaption> Desarrollador Web y Movil multiplataforma con React-native</figcaption>
            </figure>
        </section>


        <section id="habilidades">
            <h2>Habilidades</h2>
            <ul class="lista-habilidades">
                <li>HTML</li>
                <li>CSS</li>
                <li>JavaScript</li>
                <li>React</li>
                <li>Node.js</li>
            </ul>
            <div class="controles-habilidades">
                <button id="cambiar-color-habilidades" type="button">Cambiar color</button>
                <button id="cambiar-fuente-habilidades" type="button">Cambiar tipografía</button>
            </div>
        </section>

        <section id="proyectos">
            <h2 id="proyectos-titulo">Mis Proyectos</h2>

            <div class="contenedor-proyectos">
                <article class="proyecto">
                    <h3>Sistema Integral de Gestión de Proyectos</h3>
                    <p>Proyecto empresarial.</p>
                </article>

                <article class="proyecto">
                    <h3>Proyecto integrador</h3>
                    <p>Diseño y desarrollo de mi portfolio profesional.</p>
                </article>
            </div>
        </section>

        <section id="contacto">
            <h2>Contáctame</h2>
            <form class="contact-form" id="formulario-contacto" action="guardar_mensaje.php" method="post">
                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="txtnombre" type="text" placeholder="Tu nombre" required>
                </div>

                <div class="campo">
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="txtemail" type="email" placeholder="tu@email.com" required>
                </div>

                <div class="campo">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="txtmensaje" rows="6" placeholder="Escribe tu mensaje" required></textarea>
                </div>

                <button type="submit">Enviar</button>
                <p id="formulario-estado" role="status" aria-live="polite"></p>
            </form>
        </section>

        
    </main>

    <footer>
        <p>&copy; 2026 Paul Velasco. Todos los derechos reservados.</p>
        <p>Mi portafolio Web Practica de programacion</p>
    </footer>
    <script src="../Js/script.js" defer></script>
    <script src="../Js/AlertInsert.js" defer></script>
</body>
</html>