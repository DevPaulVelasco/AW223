-- Ejecuta este archivo desde phpMyAdmin para crear la base de datos y la tabla.
CREATE DATABASE IF NOT EXISTS menu_restaurante
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE menu_restaurante;

-- La tabla guarda los productos disponibles en el menú.
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL
);

-- Productos de ejemplo; solo se agregan si la tabla está vacía.
INSERT INTO productos (nombre, descripcion, precio)
SELECT 'Hamburguesa de la casa', 'Carne, queso, lechuga y salsa especial.', 145.00
WHERE NOT EXISTS (SELECT id FROM productos);

INSERT INTO productos (nombre, descripcion, precio)
SELECT 'Ensalada fresca', 'Vegetales de temporada con aderezo de la casa.', 95.00
WHERE (SELECT COUNT(*) FROM productos) = 1;

INSERT INTO productos (nombre, descripcion, precio)
SELECT 'Limonada natural', 'Limón recién exprimido y un toque de hierbabuena.', 38.00
WHERE (SELECT COUNT(*) FROM productos) = 2;
