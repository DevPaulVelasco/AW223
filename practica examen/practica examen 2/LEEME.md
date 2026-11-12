# Menú de restaurante en PHP

Proyecto de ejemplo para practicar un CRUD básico de productos y un carrito.
HTML, CSS y JavaScript están en archivos separados de PHP. PHP solo se encarga
de conectar con MySQL y responder a las peticiones del navegador en formato JSON.

## Requisitos

- XAMPP con Apache, PHP y MySQL (o MariaDB).
- phpMyAdmin, que se incluye normalmente con XAMPP.

## Preparar la base de datos

1. Inicia Apache y MySQL desde el panel de XAMPP.
2. Abre `http://localhost/phpmyadmin`.
3. Selecciona la pestaña **Importar**, elige `base_datos.sql` y ejecuta la importación.
4. Si tu usuario, contraseña o nombre de base de datos son distintos, edita
   `api/conexion.php`. La configuración inicial es `localhost`, usuario `root`,
   contraseña vacía y base de datos `menu_restaurante`.

## Abrir el proyecto

Abre desde el navegador la ruta local del proyecto. En esta carpeta, la ruta es:

`http://localhost/AW223/practica%20examen/practica%20examen%202/index.php`

No abras `index.html` directamente como archivo: la página necesita que Apache
ejecute los archivos PHP.

## Archivos principales

- `index.php`: página principal con marcado HTML; la lógica de la interfaz queda en JavaScript.
- `css/estilos.css`: presentación y estilos.
- `js/app.js`: interacciones y peticiones a la API PHP.
- `api/conexion.php`: conexión a MySQL.
- `api/productos/listar.php`: **Read**, leer productos.
- `api/productos/crear.php`: **Create**, agregar productos.
- `api/productos/actualizar.php`: **Update**, editar productos.
- `api/productos/eliminar.php`: **Delete**, eliminar productos.
- `api/carrito.php`: agregar, quitar una unidad, consultar o vaciar el carrito.

El carrito se guarda en la sesión del navegador y los productos se guardan en
MySQL. Este ejemplo no registra pedidos ni procesa pagos.

El panel CRUD es solo para practicar y no tiene inicio de sesión; no publiques
esta aplicación en Internet sin agregar autenticación y permisos.
