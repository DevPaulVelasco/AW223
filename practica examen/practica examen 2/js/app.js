// La página se comunica con PHP usando peticiones fetch y recibe respuestas JSON.
const rutaProductos = "api/productos/";
const rutaCarrito = "api/carrito.php";

const listaProductos = document.getElementById("lista-productos");
const listaCarrito = document.getElementById("lista-carrito");
const totalCarrito = document.getElementById("total-carrito");
const mensaje = document.getElementById("mensaje");
const formularioProducto = document.getElementById("formulario-producto");
const campoId = document.getElementById("producto-id");
const campoNombre = document.getElementById("producto-nombre");
const campoDescripcion = document.getElementById("producto-descripcion");
const campoPrecio = document.getElementById("producto-precio");
const botonGuardar = document.getElementById("guardar-producto");
const botonCancelar = document.getElementById("cancelar-edicion");

let productos = [];
let cantidadesCarrito = new Map();

// Convierte las respuestas PHP en datos y muestra un error claro si algo falla.
async function pedirJson(url, opciones = {}) {
    const respuesta = await fetch(url, opciones);
    const resultado = await respuesta.json();

    if (!respuesta.ok || !resultado.ok) {
        throw new Error(resultado.mensaje || "No se pudo completar la operación.");
    }

    return resultado.datos;
}

function mostrarMensaje(texto) {
    mensaje.textContent = texto;
}

function crearBoton(texto, clase, accion) {
    const boton = document.createElement("button");
    boton.type = "button";
    boton.className = clase;
    boton.textContent = texto;
    boton.addEventListener("click", accion);
    return boton;
}

// Dibuja las tarjetas de productos sin interpretar los nombres como código HTML.
function mostrarProductos() {
    listaProductos.replaceChildren();

    if (productos.length === 0) {
        listaProductos.textContent = "Todavía no hay productos. Puedes agregar uno abajo.";
        return;
    }

    productos.forEach((producto) => {
        const tarjeta = document.createElement("article");
        tarjeta.className = "producto";

        const nombre = document.createElement("h3");
        nombre.textContent = producto.nombre;

        const descripcion = document.createElement("p");
        descripcion.textContent = producto.descripcion || "Preparado especialmente para ti.";

        const precio = document.createElement("p");
        precio.className = "precio";
        precio.textContent = formatoPrecio(producto.precio);

        const controles = document.createElement("div");
        controles.className = "controles-cantidad";
        const cantidad = cantidadesCarrito.get(Number(producto.id)) || 0;
        const botonMenos = crearBoton("−", "boton boton-secundario boton-cantidad", () => quitarDelCarrito(producto.id));
        botonMenos.disabled = cantidad === 0;
        botonMenos.setAttribute("aria-label", `Quitar ${producto.nombre} del carrito`);
        const cantidadSeleccionada = document.createElement("span");
        cantidadSeleccionada.textContent = cantidad;
        cantidadSeleccionada.setAttribute("aria-label", `${cantidad} en el carrito`);
        const botonMas = crearBoton("+", "boton boton-cantidad", () => agregarAlCarrito(producto.id));
        botonMas.setAttribute("aria-label", `Agregar ${producto.nombre} al carrito`);
        controles.append(botonMenos, cantidadSeleccionada, botonMas);

        const acciones = document.createElement("div");
        acciones.className = "acciones-producto";
        acciones.append(
            crearBoton("Editar", "boton boton-secundario boton-pequeno", () => empezarEdicion(producto)),
            crearBoton("Eliminar", "boton boton-secundario boton-pequeno", () => eliminarProducto(producto.id))
        );

        tarjeta.append(nombre, descripcion, precio, controles, acciones);
        listaProductos.append(tarjeta);
    });
}

function formatoPrecio(precio) {
    return `$${Number(precio).toFixed(2)}`;
}

async function cargarProductos() {
    productos = await pedirJson(`${rutaProductos}listar.php`);
    mostrarProductos();
}

// El carrito guarda cantidades en una sesión PHP para el visitante actual.
async function cargarCarrito() {
    const articulos = await pedirJson(rutaCarrito);
    cantidadesCarrito = new Map(
        articulos.map((articulo) => [Number(articulo.id), Number(articulo.cantidad)])
    );
    listaCarrito.replaceChildren();
    mostrarProductos();

    if (articulos.length === 0) {
        listaCarrito.textContent = "El carrito está vacío.";
        totalCarrito.textContent = formatoPrecio(0);
        return;
    }

    let total = 0;

    articulos.forEach((articulo) => {
        const fila = document.createElement("div");
        fila.className = "fila-carrito";

        const informacion = document.createElement("div");
        informacion.className = "carrito-producto";
        const nombre = document.createElement("strong");
        nombre.textContent = articulo.nombre;
        const subtotal = document.createElement("small");
        subtotal.textContent = `${articulo.cantidad} × ${formatoPrecio(articulo.precio)}`;
        informacion.append(nombre, subtotal);

        const cantidad = document.createElement("span");
        cantidad.className = "cantidad";
        cantidad.textContent = articulo.cantidad;

        const quitar = crearBoton("Quitar uno", "boton boton-secundario boton-pequeno", () => quitarDelCarrito(articulo.id));
        quitar.setAttribute("aria-label", `Quitar una unidad de ${articulo.nombre}`);

        fila.append(informacion, cantidad, quitar);
        listaCarrito.append(fila);
        total += Number(articulo.precio) * Number(articulo.cantidad);
    });

    totalCarrito.textContent = formatoPrecio(total);
}

async function enviarAccionCarrito(accion, id = null) {
    await pedirJson(rutaCarrito, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accion, id })
    });
    await cargarCarrito();
}

async function agregarAlCarrito(id) {
    try {
        await enviarAccionCarrito("agregar", id);
        mostrarMensaje("Producto agregado al carrito.");
    } catch (error) {
        mostrarMensaje(error.message);
    }
}

async function quitarDelCarrito(id) {
    try {
        await enviarAccionCarrito("quitar", id);
        mostrarMensaje("Se quitó una unidad del carrito.");
    } catch (error) {
        mostrarMensaje(error.message);
    }
}

function empezarEdicion(producto) {
    campoId.value = producto.id;
    campoNombre.value = producto.nombre;
    campoDescripcion.value = producto.descripcion;
    campoPrecio.value = producto.precio;
    botonGuardar.textContent = "Guardar cambios";
    botonCancelar.hidden = false;
    formularioProducto.scrollIntoView({ behavior: "smooth" });
}

function limpiarFormulario() {
    formularioProducto.reset();
    campoId.value = "";
    botonGuardar.textContent = "Agregar producto";
    botonCancelar.hidden = true;
}

async function eliminarProducto(id) {
    if (!window.confirm("¿Seguro que quieres eliminar este producto?")) {
        return;
    }

    try {
        await pedirJson(`${rutaProductos}eliminar.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });
        await cargarProductos();
        await cargarCarrito();
        mostrarMensaje("Producto eliminado.");
    } catch (error) {
        mostrarMensaje(error.message);
    }
}

formularioProducto.addEventListener("submit", async (evento) => {
    evento.preventDefault();

    const editando = campoId.value !== "";
    const datos = {
        nombre: campoNombre.value.trim(),
        descripcion: campoDescripcion.value.trim(),
        precio: Number(campoPrecio.value)
    };

    if (editando) {
        datos.id = Number(campoId.value);
    }

    const archivo = editando ? "actualizar.php" : "crear.php";

    try {
        await pedirJson(`${rutaProductos}${archivo}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });
        limpiarFormulario();
        await cargarProductos();
        mostrarMensaje(editando ? "Producto actualizado." : "Producto agregado.");
    } catch (error) {
        mostrarMensaje(error.message);
    }
});

botonCancelar.addEventListener("click", limpiarFormulario);

document.getElementById("vaciar-carrito").addEventListener("click", async () => {
    try {
        await enviarAccionCarrito("vaciar");
        mostrarMensaje("El carrito quedó vacío.");
    } catch (error) {
        mostrarMensaje(error.message);
    }
});

// Carga ambas secciones al abrir la página; los errores quedan visibles para el usuario.
async function iniciarPagina() {
    try {
        await Promise.all([cargarProductos(), cargarCarrito()]);
    } catch (error) {
        mostrarMensaje(error.message);
        listaProductos.textContent = "No se pudo cargar el menú. Revisa la conexión y la base de datos.";
    }
}

iniciarPagina();
