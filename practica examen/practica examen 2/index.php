<!DOCTYPE html>
<!-- Este archivo es la página PHP principal; el marcado HTML queda separado de la lógica en app.js. -->
<html lang="es">
<head>
    <!-- Configuración básica para caracteres y pantallas pequeñas. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Buen Plato | Menú</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/app.js" defer></script>
</head>
<body>
    <header class="encabezado">
        <div class="contenedor encabezado-contenido">
            <a class="marca" href="index.php">El Buen Plato</a>
            <a class="enlace-carrito" href="#carrito">Mi carrito</a>
        </div>
    </header>

    <main class="contenedor">
        <section class="presentacion">
            <p class="etiqueta">Cocina hecha con cariño</p>
            <h1>Un buen plato mejora cualquier día.</h1>
            <p>Elige tus favoritos y agrégalos al carrito.</p>
        </section>

        <div id="mensaje" class="mensaje" role="status" aria-live="polite"></div>

        <section aria-labelledby="titulo-menu">
            <div class="titulo-seccion">
                <div>
                    <p class="etiqueta">Preparado al momento</p>
                    <h2 id="titulo-menu">Nuestro menú</h2>
                </div>
            </div>
            <!-- JavaScript carga aquí los productos guardados en la base de datos. -->
            <div id="lista-productos" class="productos">
                <p>Cargando el menú...</p>
            </div>
        </section>

        <section id="carrito" class="panel carrito" aria-labelledby="titulo-carrito">
            <div class="titulo-seccion">
                <div>
                    <p class="etiqueta">Tu selección</p>
                    <h2 id="titulo-carrito">Carrito</h2>
                </div>
                <button id="vaciar-carrito" class="boton boton-secundario" type="button">Vaciar carrito</button>
            </div>
            <div id="lista-carrito">
                <p>El carrito está vacío.</p>
            </div>
            <p class="total">Total: <span id="total-carrito">$0.00</span></p>
        </section>

        <section class="panel administracion" aria-labelledby="titulo-administracion">
            <p class="etiqueta">Administración básica (CRUD)</p>
            <h2 id="titulo-administracion">Administrar productos</h2>
            <p class="texto-ayuda">Agrega, edita o elimina los productos del menú. Los cambios se guardan en MySQL.</p>

            <form id="formulario-producto">
                <input id="producto-id" name="id" type="hidden">
                <div class="campos-formulario">
                    <label>
                        Nombre
                        <input id="producto-nombre" name="nombre" type="text" maxlength="100" required>
                    </label>
                    <label>
                        Precio
                        <input id="producto-precio" name="precio" type="number" min="0.01" step="0.01" required>
                    </label>
                    <label class="campo-completo">
                        Descripción
                        <textarea id="producto-descripcion" name="descripcion" rows="3"></textarea>
                    </label>
                </div>
                <div class="acciones-formulario">
                    <button id="guardar-producto" class="boton" type="submit">Agregar producto</button>
                    <button id="cancelar-edicion" class="boton boton-secundario" type="button" hidden>Cancelar edición</button>
                </div>
            </form>
        </section>
    </main>

    <footer class="pie">
        <p>El Buen Plato · Menú de ejemplo</p>
    </footer>
</body>
</html>
