DROP DATABASE IF EXISTS weirdo_comics;
CREATE DATABASE weirdo_comics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE weirdo_comics;
SET NAMES utf8mb4;

-- =====================================================================
-- 1. TABLAS
-- =====================================================================

-- 1. Rol
CREATE TABLE rol (
    id_rol             INT AUTO_INCREMENT PRIMARY KEY,
    nombre             VARCHAR(30)  NOT NULL UNIQUE,
    descripcion        VARCHAR(150) NULL,
    descuento_maximo   DECIMAL(5,2) NOT NULL DEFAULT 0,
    CHECK (descuento_maximo BETWEEN 0 AND 100)
);

-- 2. Usuario
CREATE TABLE usuario (
    id_usuario         INT AUTO_INCREMENT PRIMARY KEY,
    id_rol             INT          NOT NULL,
    nombre             VARCHAR(60)  NOT NULL,
    apellidos          VARCHAR(80)  NOT NULL,
    nombre_usuario     VARCHAR(30)  NOT NULL UNIQUE,
    correo             VARCHAR(100) NOT NULL UNIQUE,
    contrasena         VARCHAR(255) NOT NULL,
    estado             ENUM('Activo','Inactivo','Bloqueado') NOT NULL DEFAULT 'Activo',
    intentos_fallidos  INT          NOT NULL DEFAULT 0,
    bloqueado_hasta    DATETIME     NULL,
    fecha_registro     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
);

-- 3. Producto (datos comunes de cómics y artículos)
CREATE TABLE producto (
    id_producto        INT AUTO_INCREMENT PRIMARY KEY,
    codigo             VARCHAR(30)  NOT NULL UNIQUE,          -- SKU / ISBN / código de barras
    nombre             VARCHAR(120) NOT NULL,
    tipo               ENUM('Comic','Articulo') NOT NULL,
    precio_compra      DECIMAL(10,2) NOT NULL DEFAULT 0,
    precio_venta       DECIMAL(10,2) NOT NULL,
    imagen             VARCHAR(255) NULL,
    estado             ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
    fecha_registro     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (precio_venta > 0),                                   -- RN-05
    CHECK (precio_compra >= 0)
);

-- 4. Comic (especialización de producto)
CREATE TABLE comic (
    id_producto        INT PRIMARY KEY,
    editorial          VARCHAR(80)  NOT NULL,
    autor              VARCHAR(100) NOT NULL,
    volumen            INT          NULL,
    anio_publicacion   SMALLINT     NULL,
    categoria          VARCHAR(50)  NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
);

-- 5. Articulo (especialización de producto)
CREATE TABLE articulo (
    id_producto        INT PRIMARY KEY,
    descripcion        VARCHAR(255) NOT NULL,
    categoria          VARCHAR(50)  NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
);

-- 6. Inventario
CREATE TABLE inventario (
    id_inventario       INT AUTO_INCREMENT PRIMARY KEY,
    id_producto         INT NOT NULL UNIQUE,
    existencia          INT NOT NULL DEFAULT 0,
    stock_minimo        INT NOT NULL DEFAULT 5,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
    CHECK (existencia >= 0),                                    -- RN-07
    CHECK (stock_minimo >= 0)
);

-- 7. Movimiento_Inventario (cantidad con signo: + entra, - sale)
CREATE TABLE movimiento_inventario (
    id_movimiento      INT AUTO_INCREMENT PRIMARY KEY,
    id_producto        INT NOT NULL,
    id_usuario         INT NOT NULL,
    tipo               ENUM('Entrada','Salida','Ajuste','Venta','Cancelacion') NOT NULL,
    cantidad           INT NOT NULL,
    motivo             VARCHAR(200) NULL,
    referencia         VARCHAR(30)  NULL,                       -- folio de ticket o pedido (P-n)
    fecha_hora         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
    FOREIGN KEY (id_usuario)  REFERENCES usuario(id_usuario),
    CHECK (cantidad <> 0)
);

-- 8. Proveedor
CREATE TABLE proveedor (
    id_proveedor           INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa         VARCHAR(100) NOT NULL,
    contacto               VARCHAR(80)  NOT NULL,
    telefono               VARCHAR(20)  NOT NULL,
    correo                 VARCHAR(100) NULL,
    direccion              VARCHAR(200) NULL,
    condiciones_comerciales TEXT        NULL,
    estado                 ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
);

-- 9. Proveedor_Producto (relación M:N)
CREATE TABLE proveedor_producto (
    id_proveedor       INT NOT NULL,
    id_producto        INT NOT NULL,
    costo_referencia   DECIMAL(10,2) NULL,
    PRIMARY KEY (id_proveedor, id_producto),
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor),
    FOREIGN KEY (id_producto)  REFERENCES producto(id_producto)
);

-- 10. Pedido (el total se calcula en v_pedido_resumen)
CREATE TABLE pedido (
    id_pedido          INT AUTO_INCREMENT PRIMARY KEY,
    id_proveedor       INT NOT NULL,
    id_usuario         INT NOT NULL,
    fecha_pedido       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado             ENUM('Pendiente','Recibido parcial','Recibido completo','Cancelado')
                       NOT NULL DEFAULT 'Pendiente',
    fecha_recepcion    DATETIME NULL,
    observaciones      TEXT NULL,
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor),
    FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)
);

-- 11. Detalle_Pedido
CREATE TABLE detalle_pedido (
    id_detalle_pedido   INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido           INT NOT NULL,
    id_producto         INT NOT NULL,
    cantidad_solicitada INT NOT NULL,
    cantidad_recibida   INT NOT NULL DEFAULT 0,
    costo_unitario      DECIMAL(10,2) NOT NULL,
    subtotal            DECIMAL(12,2) AS (cantidad_solicitada * costo_unitario) VIRTUAL,
    UNIQUE (id_pedido, id_producto),
    FOREIGN KEY (id_pedido)   REFERENCES pedido(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
    CHECK (cantidad_solicitada > 0),
    CHECK (cantidad_recibida >= 0),
    CHECK (cantidad_recibida <= cantidad_solicitada),
    CHECK (costo_unitario >= 0)
);

-- 12. Venta (subtotal y total se calculan en v_venta_resumen)
CREATE TABLE venta (
    id_venta             INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario           INT NOT NULL,                          -- vendedor
    id_usuario_autoriza  INT NULL,                              -- admin que autoriza descuento (RN-13)
    fecha_hora           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cliente              VARCHAR(100) NULL,
    descuento_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 0,
    iva_porcentaje       DECIMAL(5,2) NOT NULL DEFAULT 16.00,
    metodo_pago          ENUM('Efectivo','Tarjeta','Transferencia') NOT NULL,
    estado               ENUM('Vigente','Cancelada') NOT NULL DEFAULT 'Vigente',
    FOREIGN KEY (id_usuario)          REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_usuario_autoriza) REFERENCES usuario(id_usuario),
    CHECK (descuento_porcentaje BETWEEN 0 AND 100),
    CHECK (iva_porcentaje BETWEEN 0 AND 100)
);

-- 13. Detalle_Venta
CREATE TABLE detalle_venta (
    id_detalle_venta   INT AUTO_INCREMENT PRIMARY KEY,
    id_venta           INT NOT NULL,
    id_producto        INT NOT NULL,
    cantidad           INT NOT NULL,
    precio_unitario    DECIMAL(10,2) NOT NULL,                  -- precio vigente al momento de la venta
    subtotal           DECIMAL(12,2) AS (cantidad * precio_unitario) VIRTUAL,
    UNIQUE (id_venta, id_producto),
    FOREIGN KEY (id_venta)    REFERENCES venta(id_venta),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
    CHECK (cantidad > 0),
    CHECK (precio_unitario > 0)
);

-- 14. Ticket
CREATE TABLE ticket (
    id_ticket          INT AUTO_INCREMENT PRIMARY KEY,
    id_venta           INT NOT NULL UNIQUE,
    folio              VARCHAR(20) NOT NULL UNIQUE,             -- RN-14: único y consecutivo
    fecha_emision      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado             ENUM('Vigente','Cancelado') NOT NULL DEFAULT 'Vigente',
    id_usuario_cancela INT NULL,
    motivo_cancelacion VARCHAR(200) NULL,
    fecha_cancelacion  DATETIME NULL,
    FOREIGN KEY (id_venta)           REFERENCES venta(id_venta),
    FOREIGN KEY (id_usuario_cancela) REFERENCES usuario(id_usuario)
);

-- 15. Bitacora
CREATE TABLE bitacora (
    id_bitacora        INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario         INT NULL,
    modulo             VARCHAR(30) NOT NULL,
    evento             VARCHAR(60) NOT NULL,
    detalle            VARCHAR(255) NULL,
    fecha_hora         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL
);

-- =====================================================================
-- 2. VISTAS (datos derivados, para mantener 3FN)
-- =====================================================================

-- Subtotal y descuento por venta
CREATE VIEW v_venta_base AS
SELECT v.id_venta, v.id_usuario, v.id_usuario_autoriza, v.fecha_hora, v.cliente,
       v.descuento_porcentaje, v.iva_porcentaje, v.metodo_pago, v.estado,
       SUM(dv.subtotal) AS subtotal,
       ROUND(SUM(dv.subtotal) * v.descuento_porcentaje / 100, 2) AS descuento_monto
FROM venta v
INNER JOIN detalle_venta dv ON dv.id_venta = v.id_venta
GROUP BY v.id_venta, v.id_usuario, v.id_usuario_autoriza, v.fecha_hora, v.cliente,
         v.descuento_porcentaje, v.iva_porcentaje, v.metodo_pago, v.estado;

-- Venta con IVA y total
CREATE VIEW v_venta_resumen AS
SELECT b.*,
       (b.subtotal - b.descuento_monto) AS base_gravable,
       ROUND((b.subtotal - b.descuento_monto) * b.iva_porcentaje / 100, 2) AS iva_monto,
       (b.subtotal - b.descuento_monto)
         + ROUND((b.subtotal - b.descuento_monto) * b.iva_porcentaje / 100, 2) AS total
FROM v_venta_base b;

-- Pedido con proveedor y costo total
CREATE VIEW v_pedido_resumen AS
SELECT pe.id_pedido, pe.id_proveedor, pr.nombre_empresa AS proveedor, pe.id_usuario,
       pe.fecha_pedido, pe.estado, pe.fecha_recepcion,
       SUM(d.subtotal) AS total
FROM pedido pe
INNER JOIN proveedor pr ON pr.id_proveedor = pe.id_proveedor
INNER JOIN detalle_pedido d ON d.id_pedido = pe.id_pedido
GROUP BY pe.id_pedido, pe.id_proveedor, pr.nombre_empresa, pe.id_usuario,
         pe.fecha_pedido, pe.estado, pe.fecha_recepcion;

-- Pantalla Inventario: estado de stock (RN-09) y valor del inventario
CREATE VIEW v_inventario_estado AS
SELECT p.id_producto, p.codigo, p.nombre, p.tipo, p.estado AS estado_producto,
       i.existencia, i.stock_minimo,
       CASE WHEN i.existencia = 0 THEN 'Agotado'
            WHEN i.existencia <= i.stock_minimo THEN 'Stock bajo'
            ELSE 'Disponible' END AS estado_stock,
       (i.existencia * p.precio_compra) AS valor_inventario
FROM producto p
INNER JOIN inventario i ON i.id_producto = p.id_producto;

-- =====================================================================
-- 3. INSERTS A TODAS LAS TABLAS
--    (contraseñas de ejemplo con SHA2; en la app real usar password_hash de PHP)
-- =====================================================================

INSERT INTO rol (nombre, descripcion, descuento_maximo) VALUES
('Administrador',        'Supervisa toda la operación y autoriza descuentos y cancelaciones', 100.00),
('Encargado de Almacén', 'Gestiona catálogo, inventario, proveedores y pedidos',                0.00),
('Vendedor',             'Registra ventas de mostrador y genera tickets',                      10.00);

INSERT INTO usuario (id_rol, nombre, apellidos, nombre_usuario, correo, contrasena, estado) VALUES
(1, 'Exal Paul',  'Bolaños Velasco',   'exal.admin',  'exal@weirdocomics.com',   SHA2('Admin123',256), 'Activo'),
(2, 'Dulce',      'Pérez Toribio',     'dulce.alm',   'dulce@weirdocomics.com',  SHA2('Alm12345',256), 'Activo'),
(2, 'Agustín',    'Cañón Rivera',      'agus.alm',    'agustin@weirdocomics.com',SHA2('Alm67890',256), 'Activo'),
(3, 'Karla María','Villagómez Jiménez','karla.ven',   'karla@weirdocomics.com',  SHA2('Ven12345',256), 'Activo'),
(3, 'Gustavo',    'Barrios González',  'gus.ven',     'gustavo@weirdocomics.com',SHA2('Ven67890',256), 'Activo'),
(3, 'Mariana',    'Ortiz Luna',        'mari.ven',    'mariana@weirdocomics.com',SHA2('Ven00000',256), 'Inactivo');

INSERT INTO producto (codigo, nombre, tipo, precio_compra, precio_venta, imagen, estado) VALUES
('CMX-001', 'Batman: Year One',                 'Comic',    180.00, 260.00, 'img/batman_year_one.jpg',  'Activo'),
('CMX-002', 'Spider-Man Vol.1 #45',             'Comic',    120.00, 195.00, 'img/spiderman_45.jpg',     'Activo'),
('CMX-003', 'X-Men: God Loves, Man Kills',      'Comic',    150.00, 230.00, 'img/xmen_glmk.jpg',        'Activo'),
('CMX-004', 'The Incredible Hulk #181',         'Comic',    220.00, 330.00, 'img/hulk_181.jpg',         'Activo'),
('CMX-005', 'Watchmen',                         'Comic',    200.00, 299.00, 'img/watchmen.jpg',         'Activo'),
('CMX-006', 'Justice League: Origin',           'Comic',    140.00, 210.00, 'img/jl_origin.jpg',        'Activo'),
('ART-001', 'Figura Wolverine 15 cm',           'Articulo', 280.00, 450.00, 'img/fig_wolverine.jpg',    'Activo'),
('ART-002', 'Playera Batman talla M',           'Articulo', 120.00, 220.00, 'img/playera_batman.jpg',   'Activo'),
('ART-003', 'Funko Pop Spider-Man',             'Articulo', 190.00, 320.00, 'img/funko_spiderman.jpg',  'Activo'),
('ART-004', 'Taza logo Marvel',                 'Articulo',  60.00, 130.00, 'img/taza_marvel.jpg',      'Activo');

INSERT INTO comic (id_producto, editorial, autor, volumen, anio_publicacion, categoria) VALUES
(1, 'DC Comics',     'Frank Miller',          1, 1987, 'Superhéroes'),
(2, 'Marvel Comics', 'Stan Lee',             45, 1967, 'Superhéroes'),
(3, 'Marvel Comics', 'Chris Claremont',       1, 1982, 'Superhéroes'),
(4, 'Marvel Comics', 'Len Wein',            181, 1974, 'Superhéroes'),
(5, 'DC Comics',     'Alan Moore',            1, 1986, 'Novela gráfica'),
(6, 'DC Comics',     'Geoff Johns',           1, 2012, 'Superhéroes');

INSERT INTO articulo (id_producto, descripcion, categoria) VALUES
(7,  'Figura articulada de Wolverine con accesorios',  'Figura'),
(8,  'Playera de algodón con logo de Batman',          'Playera'),
(9,  'Figura coleccionable Funko Pop de Spider-Man',   'Coleccionable'),
(10, 'Taza de cerámica con logo de Marvel',            'Accesorio');

INSERT INTO inventario (id_producto, existencia, stock_minimo) VALUES
(1, 12, 5), (2, 4, 5), (3, 8, 5), (4, 0, 3), (5, 15, 5),
(6, 6, 5),  (7, 3, 4), (8, 20, 6), (9, 9, 4), (10, 25, 8);

INSERT INTO proveedor (nombre_empresa, contacto, telefono, correo, direccion, condiciones_comerciales, estado) VALUES
('Marvel Importaciones MX',  'Luis Hernández', '4421112233', 'ventas@marvelimp.mx',   'Av. Universidad 100, Querétaro',   'Pago a 30 días, envío gratis en pedidos mayores a $5,000', 'Activo'),
('DC Distribuciones',        'Sofía Ramírez',  '4422223344', 'pedidos@dcdist.mx',      'Calle Reforma 45, Querétaro',      'Pago de contado con 5% de descuento',                      'Activo'),
('Collectibles Querétaro',   'Carlos Pérez',   '4423334455', 'info@collectiblesqro.mx','Blvd. Bernardo Quintana 300, Qro.','Pago a 15 días',                                          'Activo'),
('Importadora Viñetas',      'Marta López',    '4424445566', NULL,                    'Parque Industrial Benito Juárez',  'Sin condiciones vigentes',                                 'Inactivo');

INSERT INTO proveedor_producto (id_proveedor, id_producto, costo_referencia) VALUES
(1, 2, 120.00), (1, 3, 150.00), (1, 4, 220.00), (1, 10, 60.00),
(2, 1, 180.00), (2, 5, 200.00), (2, 6, 140.00), (2, 8, 120.00),
(3, 7, 280.00), (3, 9, 190.00), (3, 10, 58.00),
(4, 1, 185.00), (4, 2, 125.00);

INSERT INTO pedido (id_proveedor, id_usuario, fecha_pedido, estado, fecha_recepcion, observaciones) VALUES
(2, 2, '2026-10-05 10:00:00', 'Recibido completo', '2026-10-09 12:30:00', 'Reposición mensual DC'),
(1, 2, '2026-10-20 09:15:00', 'Recibido parcial',  '2026-10-26 11:00:00', 'Faltan Hulk #181 por parte del proveedor'),
(3, 3, '2026-11-03 14:20:00', 'Pendiente',         NULL,                  'Reabasto de coleccionables'),
(1, 2, '2026-10-28 16:40:00', 'Cancelado',         NULL,                  'Cancelado por precio');

INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad_solicitada, cantidad_recibida, costo_unitario) VALUES
(1, 1, 20, 20, 180.00),
(1, 5, 15, 15, 200.00),
(1, 6, 10, 10, 140.00),
(2, 2, 12,  8, 120.00),
(2, 4,  6,  0, 220.00),
(3, 7,  6,  0, 280.00),
(3, 9,  8,  0, 190.00),
(4, 3, 10,  0, 150.00);

INSERT INTO venta (id_usuario, id_usuario_autoriza, fecha_hora, cliente, descuento_porcentaje, iva_porcentaje, metodo_pago, estado) VALUES
(4, NULL, '2026-11-01 11:20:00', NULL,             0.00,  16.00, 'Efectivo',      'Vigente'),
(5, NULL, '2026-11-02 16:45:00', 'Carlos Mendoza', 5.00,  16.00, 'Tarjeta',       'Vigente'),
(4, 1,    '2026-11-03 13:10:00', 'Ana Gutiérrez', 15.00,  16.00, 'Transferencia', 'Vigente'),
(5, NULL, '2026-11-05 18:30:00', NULL,             0.00,  16.00, 'Efectivo',      'Cancelada'),
(4, NULL, '2026-11-07 12:05:00', 'Pedro Sánchez',  0.00,  16.00, 'Tarjeta',       'Vigente');

INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES
(1, 1, 1, 260.00),
(1, 8, 1, 220.00),
(2, 3, 2, 230.00),
(2, 10, 1, 130.00),
(3, 7, 1, 450.00),
(3, 9, 1, 320.00),
(4, 5, 1, 299.00),
(5, 2, 1, 195.00),
(5, 6, 2, 210.00);

INSERT INTO ticket (id_venta, folio, fecha_emision, estado, id_usuario_cancela, motivo_cancelacion, fecha_cancelacion) VALUES
(1, 'TK-000001', '2026-11-01 11:22:00', 'Vigente',   NULL, NULL, NULL),
(2, 'TK-000002', '2026-11-02 16:47:00', 'Vigente',   NULL, NULL, NULL),
(3, 'TK-000003', '2026-11-03 13:12:00', 'Vigente',   NULL, NULL, NULL),
(4, 'TK-000004', '2026-11-05 18:32:00', 'Cancelado', 1, 'Producto capturado por error', '2026-11-05 19:00:00'),
(5, 'TK-000005', '2026-11-07 12:07:00', 'Vigente',   NULL, NULL, NULL);

INSERT INTO movimiento_inventario (id_producto, id_usuario, tipo, cantidad, motivo, referencia, fecha_hora) VALUES
(1, 2, 'Entrada',  20, 'Recepción de pedido',        'P-1',       '2026-10-09 12:30:00'),
(5, 2, 'Entrada',  15, 'Recepción de pedido',        'P-1',       '2026-10-09 12:30:00'),
(6, 2, 'Entrada',  10, 'Recepción de pedido',        'P-1',       '2026-10-09 12:30:00'),
(2, 2, 'Entrada',   8, 'Recepción parcial de pedido','P-2',       '2026-10-26 11:00:00'),
(1, 4, 'Venta',    -1, NULL,                         'TK-000001', '2026-11-01 11:22:00'),
(8, 4, 'Venta',    -1, NULL,                         'TK-000001', '2026-11-01 11:22:00'),
(3, 5, 'Venta',    -2, NULL,                         'TK-000002', '2026-11-02 16:47:00'),
(10,5, 'Venta',    -1, NULL,                         'TK-000002', '2026-11-02 16:47:00'),
(7, 4, 'Venta',    -1, NULL,                         'TK-000003', '2026-11-03 13:12:00'),
(9, 4, 'Venta',    -1, NULL,                         'TK-000003', '2026-11-03 13:12:00'),
(5, 5, 'Venta',    -1, NULL,                         'TK-000004', '2026-11-05 18:32:00'),
(5, 1, 'Cancelacion',1,'Producto capturado por error','TK-000004','2026-11-05 19:00:00'),
(2, 4, 'Venta',    -1, NULL,                         'TK-000005', '2026-11-07 12:07:00'),
(6, 4, 'Venta',    -2, NULL,                         'TK-000005', '2026-11-07 12:07:00'),
(4, 2, 'Ajuste',   -1, 'Ejemplar dañado en bodega',  NULL,        '2026-11-06 10:00:00');

INSERT INTO bitacora (id_usuario, modulo, evento, detalle, fecha_hora) VALUES
(4,    'Login',      'Inicio de sesión',      'Acceso correcto', '2026-11-01 09:00:00'),
(NULL, 'Login',      'Cuenta inexistente',    'Intento con usuario "admin2"', '2026-11-02 08:55:00'),
(6,    'Login',      'Cuenta bloqueada',      'Tres intentos fallidos consecutivos', '2026-11-04 17:30:00'),
(1,    'Tickets',    'Cancelación de ticket', 'TK-000004: Producto capturado por error', '2026-11-05 19:00:00'),
(2,    'Inventario', 'Ajuste de inventario',  'Hulk #181: -1 (Ejemplar dañado en bodega)', '2026-11-06 10:00:00');

-- =====================================================================
-- 4. CONSULTAS SIMPLES (3)
-- =====================================================================

-- 4.1 Productos activos con precio de venta mayor a $250
SELECT id_producto, codigo, nombre, tipo, precio_venta
FROM producto
WHERE estado = 'Activo' AND precio_venta > 250
ORDER BY precio_venta DESC;

-- 4.2 Usuarios activos
SELECT id_usuario, nombre_usuario, correo, estado
FROM usuario
WHERE estado = 'Activo';

-- 4.3 Pedidos que aún esperan mercancía
SELECT id_pedido, id_proveedor, fecha_pedido, estado
FROM pedido
WHERE estado IN ('Pendiente','Recibido parcial')
ORDER BY fecha_pedido;

-- =====================================================================
-- 5. CONSULTAS CON INNER JOIN (3 + 2 para el dashboard)
-- =====================================================================

-- 5.1 Catálogo de cómics con editorial y existencias
SELECT p.codigo, p.nombre AS titulo, c.editorial, c.volumen, c.anio_publicacion,
       p.precio_compra, p.precio_venta, i.existencia
FROM producto p
INNER JOIN comic c      ON c.id_producto = p.id_producto
INNER JOIN inventario i ON i.id_producto = p.id_producto
WHERE p.estado = 'Activo'
ORDER BY p.nombre;

-- 5.2 Detalle de pedidos con proveedor, producto y lo pendiente de recibir
SELECT pe.id_pedido, pr.nombre_empresa AS proveedor, pe.estado, p.nombre AS producto,
       d.cantidad_solicitada, d.cantidad_recibida,
       (d.cantidad_solicitada - d.cantidad_recibida) AS pendiente, d.subtotal
FROM pedido pe
INNER JOIN proveedor pr     ON pr.id_proveedor = pe.id_proveedor
INNER JOIN detalle_pedido d ON d.id_pedido = pe.id_pedido
INNER JOIN producto p       ON p.id_producto = d.id_producto
ORDER BY pe.id_pedido;

-- 5.3 Historial de ventas con folio, vendedor y partidas
SELECT t.folio, v.fecha_hora, CONCAT(u.nombre, ' ', u.apellidos) AS vendedor,
       p.nombre AS producto, dv.cantidad, dv.precio_unitario, dv.subtotal, t.estado
FROM ticket t
INNER JOIN venta v          ON v.id_venta = t.id_venta
INNER JOIN usuario u        ON u.id_usuario = v.id_usuario
INNER JOIN detalle_venta dv ON dv.id_venta = v.id_venta
INNER JOIN producto p       ON p.id_producto = dv.id_producto
ORDER BY t.folio;

-- 5.4 Dashboard: Top 5 productos más vendidos (solo ventas vigentes, RN-15)
SELECT p.nombre, SUM(dv.cantidad) AS unidades_vendidas, SUM(dv.subtotal) AS total_generado
FROM detalle_venta dv
INNER JOIN venta v    ON v.id_venta = dv.id_venta
INNER JOIN producto p ON p.id_producto = dv.id_producto
WHERE v.estado = 'Vigente'
GROUP BY p.id_producto, p.nombre
ORDER BY unidades_vendidas DESC
LIMIT 5;

-- 5.5 Dashboard: desempeño por vendedor (ventas vigentes)
SELECT CONCAT(u.nombre, ' ', u.apellidos) AS vendedor,
       COUNT(*) AS ventas, SUM(r.total) AS ingresos, ROUND(AVG(r.total), 2) AS ticket_promedio
FROM v_venta_resumen r
INNER JOIN usuario u ON u.id_usuario = r.id_usuario
WHERE r.estado = 'Vigente'
GROUP BY u.id_usuario, u.nombre, u.apellidos;

-- =====================================================================
-- 6. PROCEDIMIENTOS ALMACENADOS
--    En phpMyAdmin: pega este bloque en la pestaña SQL (acepta DELIMITER)
--    o cambia el cuadro "Delimitador" a $$ si marca error.
-- =====================================================================

DELIMITER $$

-- ---------------------------------------------------------------------
-- 6.1 Crear encabezado de venta. Valida límite de descuento (RN-13).
--     Devuelve el id de la venta en p_id_venta.
-- ---------------------------------------------------------------------
CREATE PROCEDURE sp_crear_venta(
    IN  p_id_usuario  INT,
    IN  p_metodo_pago VARCHAR(20),
    IN  p_cliente     VARCHAR(100),
    IN  p_descuento   DECIMAL(5,2),
    IN  p_id_autoriza INT,
    OUT p_id_venta    INT
)
BEGIN
    DECLARE v_max   DECIMAL(5,2);
    DECLARE v_admin INT DEFAULT 0;

    SELECT r.descuento_maximo INTO v_max
    FROM usuario u
    INNER JOIN rol r ON r.id_rol = u.id_rol
    WHERE u.id_usuario = p_id_usuario AND u.estado = 'Activo';

    IF v_max IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El usuario no existe o no está activo';
    END IF;
    IF p_descuento < 0 OR p_descuento > 100 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El descuento debe estar entre 0 y 100';
    END IF;

    IF p_descuento > v_max THEN
        SELECT COUNT(*) INTO v_admin
        FROM usuario u
        INNER JOIN rol r ON r.id_rol = u.id_rol
        WHERE u.id_usuario = p_id_autoriza AND r.nombre = 'Administrador' AND u.estado = 'Activo';

        IF v_admin = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El descuento excede el límite del rol y requiere autorización del Administrador';
        END IF;
    END IF;

    INSERT INTO venta (id_usuario, id_usuario_autoriza, cliente, descuento_porcentaje, metodo_pago)
    VALUES (p_id_usuario,
            IF(p_descuento > v_max, p_id_autoriza, NULL),
            p_cliente, p_descuento, p_metodo_pago);

    SET p_id_venta = LAST_INSERT_ID();
END$$

-- ---------------------------------------------------------------------
-- 6.2 Agregar una partida a la venta: valida producto activo y existencia
--     (RN-07, RN-12), toma el precio vigente, descuenta inventario y
--     registra el movimiento. Llamar dentro de una transacción.
-- ---------------------------------------------------------------------
CREATE PROCEDURE sp_agregar_partida_venta(
    IN p_id_venta    INT,
    IN p_id_producto INT,
    IN p_cantidad    INT
)
BEGIN
    DECLARE v_estado      VARCHAR(10);
    DECLARE v_id_usuario  INT;
    DECLARE v_prod_estado VARCHAR(10);
    DECLARE v_precio      DECIMAL(10,2);
    DECLARE v_existencia  INT;

    SELECT estado, id_usuario INTO v_estado, v_id_usuario
    FROM venta WHERE id_venta = p_id_venta;

    IF v_estado IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La venta no existe';
    END IF;
    IF v_estado <> 'Vigente' OR EXISTS (SELECT 1 FROM ticket WHERE id_venta = p_id_venta) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La venta ya fue finalizada o cancelada';
    END IF;
    IF p_cantidad IS NULL OR p_cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad debe ser mayor a cero';
    END IF;

    SELECT p.estado, p.precio_venta, i.existencia
      INTO v_prod_estado, v_precio, v_existencia
    FROM producto p
    INNER JOIN inventario i ON i.id_producto = p.id_producto
    WHERE p.id_producto = p_id_producto
    FOR UPDATE;

    IF v_prod_estado IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto no existe';
    END IF;
    IF v_prod_estado <> 'Activo' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto está inactivo y no puede venderse';
    END IF;
    IF v_existencia < p_cantidad THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Existencia insuficiente para el producto solicitado';
    END IF;
    IF EXISTS (SELECT 1 FROM detalle_venta WHERE id_venta = p_id_venta AND id_producto = p_id_producto) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto ya está en la venta; modifica su cantidad';
    END IF;

    INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario)
    VALUES (p_id_venta, p_id_producto, p_cantidad, v_precio);

    UPDATE inventario SET existencia = existencia - p_cantidad
    WHERE id_producto = p_id_producto;

    INSERT INTO movimiento_inventario (id_producto, id_usuario, tipo, cantidad, referencia)
    VALUES (p_id_producto, v_id_usuario, 'Venta', -p_cantidad, CONCAT('V-', p_id_venta));
END$$

-- ---------------------------------------------------------------------
-- 6.3 Finalizar venta: genera el ticket con folio único y consecutivo
--     (RN-14) y devuelve los datos para imprimirlo.
-- ---------------------------------------------------------------------
CREATE PROCEDURE sp_finalizar_venta(IN p_id_venta INT)
BEGIN
    DECLARE v_estado VARCHAR(10);
    DECLARE v_next   INT;
    DECLARE v_folio  VARCHAR(20);

    SELECT estado INTO v_estado FROM venta WHERE id_venta = p_id_venta;

    IF v_estado IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La venta no existe';
    END IF;
    IF v_estado <> 'Vigente' OR EXISTS (SELECT 1 FROM ticket WHERE id_venta = p_id_venta) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La venta ya tiene ticket o está cancelada';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM detalle_venta WHERE id_venta = p_id_venta) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La venta debe tener al menos un producto';
    END IF;

    SELECT IFNULL(MAX(id_ticket), 0) + 1 INTO v_next FROM ticket;
    SET v_folio = CONCAT('TK-', LPAD(v_next, 6, '0'));

    INSERT INTO ticket (id_venta, folio) VALUES (p_id_venta, v_folio);

    UPDATE movimiento_inventario SET referencia = v_folio
    WHERE tipo = 'Venta' AND referencia = CONCAT('V-', p_id_venta);

    SELECT t.folio, t.fecha_emision,
           CONCAT(u.nombre, ' ', u.apellidos) AS atendio,
           r.metodo_pago, r.subtotal, r.descuento_monto, r.iva_monto, r.total
    FROM ticket t
    INNER JOIN v_venta_resumen r ON r.id_venta = t.id_venta
    INNER JOIN usuario u         ON u.id_usuario = r.id_usuario
    WHERE t.id_venta = p_id_venta;
END$$

-- ---------------------------------------------------------------------
-- 6.4 Cancelar ticket: solo Administrador y con motivo (RN-14).
--     Restituye existencias, registra movimientos y bitácora.
-- ---------------------------------------------------------------------
CREATE PROCEDURE sp_cancelar_ticket(
    IN p_id_ticket INT,
    IN p_id_admin  INT,
    IN p_motivo    VARCHAR(200)
)
BEGIN
    DECLARE v_id_venta INT;
    DECLARE v_estado   VARCHAR(10);
    DECLARE v_folio    VARCHAR(20);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF NOT EXISTS (SELECT 1 FROM usuario u INNER JOIN rol r ON r.id_rol = u.id_rol
                   WHERE u.id_usuario = p_id_admin AND r.nombre = 'Administrador' AND u.estado = 'Activo') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo un Administrador activo puede cancelar tickets';
    END IF;
    IF p_motivo IS NULL OR TRIM(p_motivo) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Debe registrar el motivo de la cancelación';
    END IF;

    SELECT id_venta, estado, folio INTO v_id_venta, v_estado, v_folio
    FROM ticket WHERE id_ticket = p_id_ticket;

    IF v_estado IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El ticket no existe';
    END IF;
    IF v_estado = 'Cancelado' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El ticket ya está cancelado';
    END IF;

    START TRANSACTION;

    UPDATE inventario i
    INNER JOIN detalle_venta dv ON dv.id_producto = i.id_producto
    SET i.existencia = i.existencia + dv.cantidad
    WHERE dv.id_venta = v_id_venta;

    INSERT INTO movimiento_inventario (id_producto, id_usuario, tipo, cantidad, motivo, referencia)
    SELECT dv.id_producto, p_id_admin, 'Cancelacion', dv.cantidad, p_motivo, v_folio
    FROM detalle_venta dv
    WHERE dv.id_venta = v_id_venta;

    UPDATE ticket
    SET estado = 'Cancelado', id_usuario_cancela = p_id_admin,
        motivo_cancelacion = p_motivo, fecha_cancelacion = NOW()
    WHERE id_ticket = p_id_ticket;

    UPDATE venta SET estado = 'Cancelada' WHERE id_venta = v_id_venta;

    INSERT INTO bitacora (id_usuario, modulo, evento, detalle)
    VALUES (p_id_admin, 'Tickets', 'Cancelación de ticket', CONCAT(v_folio, ': ', p_motivo));

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- 6.5 Recibir una partida de pedido (recepción total o parcial, RN-11).
-- ---------------------------------------------------------------------
CREATE PROCEDURE sp_recibir_partida_pedido(
    IN p_id_detalle_pedido INT,
    IN p_cantidad          INT,
    IN p_id_usuario        INT
)
BEGIN
    DECLARE v_id_pedido   INT;
    DECLARE v_id_producto INT;
    DECLARE v_solicitada  INT;
    DECLARE v_recibida    INT;
    DECLARE v_estado_ped  VARCHAR(20);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT d.id_pedido, d.id_producto, d.cantidad_solicitada, d.cantidad_recibida, pe.estado
      INTO v_id_pedido, v_id_producto, v_solicitada, v_recibida, v_estado_ped
    FROM detalle_pedido d
    INNER JOIN pedido pe ON pe.id_pedido = d.id_pedido
    WHERE d.id_detalle_pedido = p_id_detalle_pedido;

    IF v_id_pedido IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La partida del pedido no existe';
    END IF;
    IF v_estado_ped NOT IN ('Pendiente','Recibido parcial') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El pedido no admite recepciones (cancelado o ya completo)';
    END IF;
    IF p_cantidad IS NULL OR p_cantidad <= 0 OR p_cantidad > (v_solicitada - v_recibida) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cantidad inválida: excede lo pendiente por recibir';
    END IF;

    START TRANSACTION;

    UPDATE detalle_pedido SET cantidad_recibida = cantidad_recibida + p_cantidad
    WHERE id_detalle_pedido = p_id_detalle_pedido;

    UPDATE inventario SET existencia = existencia + p_cantidad
    WHERE id_producto = v_id_producto;

    INSERT INTO movimiento_inventario (id_producto, id_usuario, tipo, cantidad, motivo, referencia)
    VALUES (v_id_producto, p_id_usuario, 'Entrada', p_cantidad, 'Recepción de pedido', CONCAT('P-', v_id_pedido));

    UPDATE pedido
    SET fecha_recepcion = NOW(),
        estado = IF(EXISTS (SELECT 1 FROM detalle_pedido
                            WHERE id_pedido = v_id_pedido AND cantidad_recibida < cantidad_solicitada),
                    'Recibido parcial', 'Recibido completo')
    WHERE id_pedido = v_id_pedido;

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- 6.6 Recibir pedido completo (botón "Ingresar a inv" de la pantalla Pedidos)
-- ---------------------------------------------------------------------
CREATE PROCEDURE sp_recibir_pedido_completo(
    IN p_id_pedido  INT,
    IN p_id_usuario INT
)
BEGIN
    DECLARE v_estado VARCHAR(20);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT estado INTO v_estado FROM pedido WHERE id_pedido = p_id_pedido;

    IF v_estado IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El pedido no existe';
    END IF;
    IF v_estado NOT IN ('Pendiente','Recibido parcial') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El pedido no admite recepciones (cancelado o ya completo)';
    END IF;

    START TRANSACTION;

    INSERT INTO movimiento_inventario (id_producto, id_usuario, tipo, cantidad, motivo, referencia)
    SELECT id_producto, p_id_usuario, 'Entrada', (cantidad_solicitada - cantidad_recibida),
           'Recepción total del pedido', CONCAT('P-', p_id_pedido)
    FROM detalle_pedido
    WHERE id_pedido = p_id_pedido AND cantidad_solicitada > cantidad_recibida;

    UPDATE inventario i
    INNER JOIN detalle_pedido d ON d.id_producto = i.id_producto
    SET i.existencia = i.existencia + (d.cantidad_solicitada - d.cantidad_recibida)
    WHERE d.id_pedido = p_id_pedido;

    UPDATE detalle_pedido SET cantidad_recibida = cantidad_solicitada
    WHERE id_pedido = p_id_pedido;

    UPDATE pedido SET estado = 'Recibido completo', fecha_recepcion = NOW()
    WHERE id_pedido = p_id_pedido;

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- 6.7 Ajuste manual de inventario con motivo obligatorio (RN-08)
-- ---------------------------------------------------------------------
CREATE PROCEDURE sp_ajustar_inventario(
    IN p_id_producto      INT,
    IN p_nueva_existencia INT,
    IN p_id_usuario       INT,
    IN p_motivo           VARCHAR(200)
)
BEGIN
    DECLARE v_actual INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF p_motivo IS NULL OR TRIM(p_motivo) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El motivo del ajuste es obligatorio';
    END IF;
    IF p_nueva_existencia IS NULL OR p_nueva_existencia < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La existencia no puede ser negativa';
    END IF;

    SELECT existencia INTO v_actual FROM inventario WHERE id_producto = p_id_producto;

    IF v_actual IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El producto no tiene registro de inventario';
    END IF;
    IF v_actual = p_nueva_existencia THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La nueva existencia es igual a la actual';
    END IF;

    START TRANSACTION;

    UPDATE inventario SET existencia = p_nueva_existencia WHERE id_producto = p_id_producto;

    INSERT INTO movimiento_inventario (id_producto, id_usuario, tipo, cantidad, motivo)
    VALUES (p_id_producto, p_id_usuario, 'Ajuste', p_nueva_existencia - v_actual, p_motivo);

    INSERT INTO bitacora (id_usuario, modulo, evento, detalle)
    VALUES (p_id_usuario, 'Inventario', 'Ajuste de inventario',
            CONCAT('Producto ', p_id_producto, ': ', v_actual, ' -> ', p_nueva_existencia, ' (', p_motivo, ')'));

    COMMIT;
END$$

-- ---------------------------------------------------------------------
-- 6.8 Login fallido: cuenta intentos y bloquea al tercero (RN-02).
--     Cuando el login sea correcto la app debe ejecutar:
--     UPDATE usuario SET intentos_fallidos = 0 WHERE id_usuario = ?;
-- ---------------------------------------------------------------------
CREATE PROCEDURE sp_registrar_intento_fallido(IN p_nombre_usuario VARCHAR(30))
BEGIN
    DECLARE v_id       INT;
    DECLARE v_intentos INT;

    SELECT id_usuario, intentos_fallidos + 1 INTO v_id, v_intentos
    FROM usuario WHERE nombre_usuario = p_nombre_usuario;

    IF v_id IS NULL THEN
        INSERT INTO bitacora (id_usuario, modulo, evento, detalle)
        VALUES (NULL, 'Login', 'Cuenta inexistente', CONCAT('Intento con usuario "', p_nombre_usuario, '"'));
    ELSE
        UPDATE usuario SET intentos_fallidos = v_intentos WHERE id_usuario = v_id;

        IF v_intentos >= 3 THEN
            UPDATE usuario
            SET estado = 'Bloqueado', bloqueado_hasta = NOW() + INTERVAL 15 MINUTE
            WHERE id_usuario = v_id AND estado = 'Activo';

            INSERT INTO bitacora (id_usuario, modulo, evento, detalle)
            VALUES (v_id, 'Login', 'Cuenta bloqueada', 'Tres intentos fallidos consecutivos');
        ELSE
            INSERT INTO bitacora (id_usuario, modulo, evento, detalle)
            VALUES (v_id, 'Login', 'Intento fallido', CONCAT('Intento número ', v_intentos));
        END IF;
    END IF;
END$$

DELIMITER ;

-- =====================================================================
-- 7. PRUEBAS DE LOS PROCEDIMIENTOS (quita el "-- " y ejecuta una por una)
-- =====================================================================

-- Venta completa con ticket (vendedor 4 = Karla, 5% de descuento)
-- START TRANSACTION;
-- CALL sp_crear_venta(4, 'Efectivo', 'Cliente demo', 5, NULL, @id_venta);
-- CALL sp_agregar_partida_venta(@id_venta, 1, 1);
-- CALL sp_agregar_partida_venta(@id_venta, 6, 2);
-- CALL sp_finalizar_venta(@id_venta);
-- COMMIT;

-- Descuento mayor al límite del vendedor SIN autorización (debe marcar error)
-- CALL sp_crear_venta(4, 'Efectivo', NULL, 25, NULL, @x);

-- Descuento mayor al límite CON autorización del admin (id 1)
-- CALL sp_crear_venta(4, 'Tarjeta', NULL, 25, 1, @x);

-- Cancelar el ticket 1 como Administrador
-- CALL sp_cancelar_ticket(1, 1, 'Cliente se arrepintió de la compra');

-- Recibir el pedido 3 completo / recibir 4 piezas de la partida 4
-- CALL sp_recibir_pedido_completo(3, 2);
-- CALL sp_recibir_partida_pedido(4, 4, 2);

-- Ajuste de inventario (Hulk #181 de 0 a 2 piezas)
-- CALL sp_ajustar_inventario(4, 2, 2, 'Se encontraron 2 piezas en bodega');

-- Tres logins fallidos seguidos bloquean la cuenta
-- CALL sp_registrar_intento_fallido('gus.ven');
-- CALL sp_registrar_intento_fallido('gus.ven');
-- CALL sp_registrar_intento_fallido('gus.ven');

-- Vistas para las pantallas
-- SELECT * FROM v_inventario_estado;
-- SELECT * FROM v_pedido_resumen;
-- SELECT * FROM v_venta_resumen;