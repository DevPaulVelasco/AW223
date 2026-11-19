SET NAMES utf8mb4;
DROP DATABASE IF EXISTS banco_tiempo_apak;
CREATE DATABASE banco_tiempo_apak CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE banco_tiempo_apak;

-- Tabla de roles de usuario
CREATE TABLE rol (
    id_rol      INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150) NULL
);

-- Catálogo de niveles de habilidad para normalizar texto libre
CREATE TABLE nivel_habilidad (
    id_nivel INT AUTO_INCREMENT PRIMARY KEY,
    nombre   VARCHAR(30) NOT NULL UNIQUE
);

-- Catálogo de categorías de servicios
CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(50) NOT NULL UNIQUE,
    descripcion  VARCHAR(200) NULL
);

-- Almacena la información de perfil de los estudiantes y administradores
CREATE TABLE usuario (
    id_usuario            INT AUTO_INCREMENT PRIMARY KEY,
    id_rol                INT NOT NULL,
    nombre                VARCHAR(80) NOT NULL,
    edad                  TINYINT UNSIGNED NOT NULL,
    carrera               VARCHAR(100) NOT NULL,
    correo                VARCHAR(100) NOT NULL UNIQUE,
    password_hash         VARCHAR(255) NOT NULL,
    descripcion_sobre_mi  TEXT NULL,
    credito               TINYINT NOT NULL DEFAULT 1 CHECK (credito BETWEEN 0 AND 1),
    fecha_registro        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
);

-- Opcional: Solicitudes de recuperación de contraseña con token seguro hasheado
CREATE TABLE recuperacion_contrasena (
    id_recuperacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario      INT NOT NULL,
    token_hash      VARCHAR(255) NOT NULL UNIQUE,
    vencimiento     DATETIME NOT NULL,
    fecha_uso       DATETIME NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- Definición general de habilidades disponibles en la plataforma
CREATE TABLE habilidad (
    id_habilidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(60) NOT NULL UNIQUE,
    descripcion  VARCHAR(200) NULL
);

-- Relación Muchos a Muchos: Un usuario tiene varias habilidades con un nivel
CREATE TABLE usuario_habilidad (
    id_usuario   INT NOT NULL,
    id_habilidad INT NOT NULL,
    id_nivel     INT NOT NULL,
    PRIMARY KEY (id_usuario, id_habilidad),
    FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_habilidad) REFERENCES habilidad(id_habilidad) ON DELETE CASCADE,
    FOREIGN KEY (id_nivel)     REFERENCES nivel_habilidad(id_nivel)
);

-- Servicios publicados por los usuarios exclusivamente en línea de una hora de duración
CREATE TABLE servicio (
    id_servicio          INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario           INT NOT NULL,
    id_categoria         INT NOT NULL,
    titulo               VARCHAR(100) NOT NULL,
    descripcion          TEXT NOT NULL,
    fecha_disponibilidad DATE NOT NULL,
    hora_disponibilidad  TIME NOT NULL,
    duracion_horas       TINYINT NOT NULL DEFAULT 1 CHECK (duracion_horas = 1),
    modalidad            VARCHAR(30) NOT NULL DEFAULT 'En línea',
    estado               ENUM('Activo', 'Suspendido') NOT NULL DEFAULT 'Activo',
    FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);

-- Relación Muchos a Muchos para servicios favoritos de los usuarios
CREATE TABLE favorito (
    id_usuario     INT NOT NULL,
    id_servicio    INT NOT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_servicio),
    FOREIGN KEY (id_usuario)  REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_servicio) REFERENCES servicio(id_servicio) ON DELETE CASCADE
);

-- Solicitudes de intercambio 1 a 1 entre usuario solicitante y servicios implicados
CREATE TABLE solicitud (
    id_solicitud           INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario_solicitante INT NOT NULL,
    id_servicio_deseado    INT NOT NULL,
    id_servicio_ofrecido   INT NOT NULL,
    estado                 ENUM('Pendiente', 'Aceptada', 'Rechazada', 'Cancelada') NOT NULL DEFAULT 'Pendiente',
    fecha_creacion         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario_solicitante) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_servicio_deseado)    REFERENCES servicio(id_servicio),
    FOREIGN KEY (id_servicio_ofrecido)   REFERENCES servicio(id_servicio)
);

-- Intercambio formalizado a partir de una solicitud aceptada
CREATE TABLE intercambio (
    id_intercambio INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud   INT NOT NULL UNIQUE,
    estado         ENUM('Pendiente', 'Completado', 'Cancelado') NOT NULL DEFAULT 'Pendiente',
    fecha_inicio   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_fin      DATETIME NULL,
    FOREIGN KEY (id_solicitud) REFERENCES solicitud(id_solicitud)
);

-- Registro de movimientos de créditos entradas y salidas asociadas a intercambios
CREATE TABLE movimiento_credito (
    id_movimiento  INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario     INT NOT NULL,
    id_intercambio INT NULL,
    tipo           ENUM('Entrada', 'Salida') NOT NULL,
    cantidad       TINYINT NOT NULL DEFAULT 1 CHECK (cantidad = 1),
    estado         VARCHAR(30) NOT NULL DEFAULT 'Completado',
    fecha          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario)     REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_intercambio) REFERENCES intercambio(id_intercambio)
);

-- Calificaciones y reseñas asociadas a un intercambio completado
CREATE TABLE resena (
    id_resena           INT AUTO_INCREMENT PRIMARY KEY,
    id_intercambio      INT NOT NULL,
    id_usuario_autor    INT NOT NULL,
    id_usuario_receptor INT NOT NULL,
    puntuacion          TINYINT NOT NULL CHECK (puntuacion BETWEEN 1 AND 5),
    comentario          TEXT NULL,
    fecha               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_intercambio, id_usuario_autor),
    FOREIGN KEY (id_intercambio)      REFERENCES intercambio(id_intercambio),
    FOREIGN KEY (id_usuario_autor)    REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_usuario_receptor) REFERENCES usuario(id_usuario)
);

-- Reportes de servicios publicados
CREATE TABLE reporte (
    id_reporte          INT AUTO_INCREMENT PRIMARY KEY,
    id_servicio         INT NOT NULL,
    id_usuario_reporta  INT NOT NULL,
    id_usuario_admin    INT NULL,
    descripcion         TEXT NOT NULL,
    estado              ENUM('Pendiente', 'Revisado', 'Eliminado') NOT NULL DEFAULT 'Pendiente',
    fecha_creacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_revision      DATETIME NULL,
    FOREIGN KEY (id_servicio)        REFERENCES servicio(id_servicio) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario_reporta) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_usuario_admin)   REFERENCES usuario(id_usuario)
);

INSERT INTO rol (nombre, descripcion) VALUES
('Administrador', 'Control total de la plataforma y gestión de reportes'),
('Estudiante', 'Usuario común que ofrece y solicita servicios de tiempo');

INSERT INTO nivel_habilidad (nombre) VALUES ('Básico'), ('Medio'), ('Amplio');

INSERT INTO categoria (nombre, descripcion) VALUES 
('Idiomas', 'Práctica de lenguas extranjeras en línea'),
('Tecnología', 'Asesoría en programación, software y herramientas digitales');

INSERT INTO habilidad (nombre, descripcion) VALUES 
('Conversación en Inglés', 'Fluidez en inglés técnico y cotidiano'),
('Fundamentos de Python', 'Programación básica y estructurada en Python');

INSERT INTO usuario (id_rol, nombre, edad, carrera, correo, password_hash, descripcion_sobre_mi, credito) VALUES
(1, 'Sofía Martínez', 21, 'Ingeniería en Sistemas', 'sofia.admin@apak.edu', '$2y$10$fictitioushashadmin123456789', 'Administradora de la red de tiempo.', 1),
(2, 'Carlos Gómez', 20, 'Licenciatura en Lenguas', 'carlos.gomez@apak.edu', '$2y$10$fictitioushashuserone12345678', 'Apasionado por los idiomas y la cultura.', 1),
(2, 'Lucía Fernández', 22, 'Ciencias de la Computación', 'lucia.fernandez@apak.edu', '$2y$10$fictitioushashusertwo12345678', 'Me encanta programar y enseñar.', 1);

INSERT INTO usuario_habilidad (id_usuario, id_habilidad, id_nivel) VALUES
(2, 1, 3),
(3, 2, 2);

INSERT INTO servicio (id_usuario, id_categoria, titulo, descripcion, fecha_disponibilidad, hora_disponibilidad, duracion_horas, modalidad, estado) VALUES
(2, 1, 'Club de conversación en Inglés', 'Sesión interactiva en línea de una hora para practicar vocabulario.', '2026-06-10', '16:00:00', 1, 'En línea', 'Activo'),
(3, 2, 'Introducción a Python desde cero', 'Asesoría personalizada en línea sobre estructuras básicas.', '2026-06-11', '10:00:00', 1, 'En línea', 'Activo');

INSERT INTO favorito (id_usuario, id_servicio) VALUES (2, 2);

INSERT INTO solicitud (id_usuario_solicitante, id_servicio_deseado, id_servicio_ofrecido, estado) VALUES
(2, 2, 1, 'Pendiente');

SELECT * FROM categoria;
SELECT * FROM servicio WHERE estado = 'Activo';

SELECT u.nombre, u.edad, u.carrera, u.descripcion_sobre_mi, u.credito 
FROM usuario u WHERE u.id_usuario = 2;

SELECT h.nombre AS habilidad, nh.nombre AS nivel 
FROM usuario_habilidad uh
JOIN habilidad h ON h.id_habilidad = uh.id_habilidad
JOIN nivel_habilidad nh ON nh.id_nivel = uh.id_nivel
WHERE uh.id_usuario = 2;

SELECT s.id_servicio, s.titulo, s.descripcion, s.fecha_disponibilidad, s.hora_disponibilidad, 
       c.nombre AS categoria, u.nombre AS proveedor
FROM servicio s
JOIN categoria c ON c.id_categoria = s.id_categoria
JOIN usuario u ON u.id_usuario = s.id_usuario
WHERE s.estado = 'Activo';

SELECT id_solicitud, estado, fecha_creacion FROM solicitud WHERE id_usuario_solicitante = 2;

SELECT i.id_intercambio, i.estado, i.fecha_inicio
FROM intercambio i
JOIN solicitud s ON s.id_solicitud = i.id_solicitud
WHERE s.id_usuario_solicitante = 2 AND i.estado = 'Completado';

SELECT 
    (SELECT COUNT(*) FROM usuario) AS total_usuarios,
    (SELECT COUNT(*) FROM servicio WHERE estado = 'Activo') AS servicios_activos,
    (SELECT COUNT(*) FROM solicitud WHERE estado = 'Pendiente') AS solicitudes_pendientes,
    (SELECT COUNT(*) FROM intercambio WHERE estado = 'Completado') AS intercambios_completados;