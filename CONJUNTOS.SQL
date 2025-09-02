DROP DATABASE IF EXISTS CONJUNTOS;
CREATE DATABASE CONJUNTOS;
USE CONJUNTOS;

-- ================================
-- ADMINISTRADOR
-- ================================
CREATE TABLE ADMINISTRADOR (
    ID_ADMINISTRADOR INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ADMINISTRADOR VARCHAR(50) NOT NULL,
    CORREO_ADMINISTRADOR VARCHAR(50),
    TELEFONO_ADMINISTRADOR VARCHAR(50)
);

-- ================================
-- RESIDENTES
-- ================================
CREATE TABLE RESIDENTES (
    ID_RESIDENTES INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_RESIDENTE VARCHAR(50) NOT NULL,
    TELEFONO_RESIDENTE VARCHAR(20),
    CASA_RESIDENTES VARCHAR(20) NOT NULL,
    CORREO_RESIDENTE VARCHAR(50),
    CONTRASEÑA_RESIDENTES VARCHAR(50) NOT NULL
);

-- ================================
-- TABLA ESTADOS AYUDA
-- ================================
CREATE TABLE ESTADO_AYUDA (
    ID_ESTADO_AYUDA INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ESTADO VARCHAR(50) NOT NULL
);

-- AYUDAS (con FK a ESTADO_AYUDA)
CREATE TABLE AYUDAS (
    ID_AYUDAS INT PRIMARY KEY AUTO_INCREMENT,
    DESCRIPCION_AYUDA VARCHAR(250),
    ID_ESTADO_AYUDA INT,
    FOREIGN KEY (ID_ESTADO_AYUDA) REFERENCES ESTADO_AYUDA(ID_ESTADO_AYUDA)
);

-- ================================
-- ALERTAS
-- ================================
CREATE TABLE ALERTAS (
    ID_ALERTAS INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ALERTA VARCHAR(50), 
    DESCRIPCION_ALERTA VARCHAR(50)
);

-- ================================
-- TABLA ESTADOS COMPRA
-- ================================
CREATE TABLE ESTADO_COMPRA (
    ID_ESTADO_COMPRA INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ESTADO VARCHAR(50) NOT NULL
);

-- COMPRAS (con FK a ESTADO_COMPRA)
CREATE TABLE COMPRAS ( 
    ID_COMPRAS INT PRIMARY KEY AUTO_INCREMENT,
    TIPO_COMPRA VARCHAR(50),
    PRECIO_COMPRA INT,
    ID_ESTADO_COMPRA INT,
    FOREIGN KEY (ID_ESTADO_COMPRA) REFERENCES ESTADO_COMPRA(ID_ESTADO_COMPRA)
);

-- ================================
-- TABLA ESTADOS REUNIONES
-- ================================
CREATE TABLE ESTADO_REUNION (
    ID_ESTADO_REUNION INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ESTADO VARCHAR(50) NOT NULL
);

-- REUNIONES (con FK a ESTADO_REUNION)
CREATE TABLE REUNIONES (
    ID_REUNIONES INT PRIMARY KEY AUTO_INCREMENT, 
    FECHAHORA_REUNIONES DATETIME, 
    MOTIVO_REUNIONES VARCHAR(50),
    ID_ESTADO_REUNION INT,
    FOREIGN KEY (ID_ESTADO_REUNION) REFERENCES ESTADO_REUNION(ID_ESTADO_REUNION)
);

-- ================================
-- REPORTES (relacionado con ADMINISTRADOR)
-- ================================
CREATE TABLE REPORTES (
    ID_REPORTES INT PRIMARY KEY AUTO_INCREMENT,
    ID_ADMINISTRADOR INT,
    TIPO_REPORTE VARCHAR(60),
    FECHAHORA_REPORTES DATETIME,
    DESCRIPCION_REPORTE VARCHAR(50), 
    FOREIGN KEY (ID_ADMINISTRADOR) REFERENCES ADMINISTRADOR(ID_ADMINISTRADOR)
);

-- ================================
-- TABLA ESTADOS FERIAS
-- ================================
CREATE TABLE ESTADO_FERIA (
    ID_ESTADO_FERIA INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ESTADO VARCHAR(50) NOT NULL
);

-- FERIAS (con FK a ADMINISTRADOR y ESTADO_FERIA)
CREATE TABLE FERIAS (
    ID_FERIAS INT PRIMARY KEY AUTO_INCREMENT,
    ID_ADMINISTRADOR INT, 
    FECHAHORA_FERIAS DATETIME,
    DESCRIPCION_FERIAS VARCHAR(50),
    ID_ESTADO_FERIA INT,
    FOREIGN KEY (ID_ADMINISTRADOR) REFERENCES ADMINISTRADOR(ID_ADMINISTRADOR),
    FOREIGN KEY (ID_ESTADO_FERIA) REFERENCES ESTADO_FERIA(ID_ESTADO_FERIA)
);

-- sople

-- ================================
-- ADMINISTRADOR (1 registro)
-- ================================
INSERT INTO ADMINISTRADOR (NOMBRE_ADMINISTRADOR, CORREO_ADMINISTRADOR, TELEFONO_ADMINISTRADOR)
VALUES ('Carlos Pérez', 'carlos@conjuntos.com', '3001234567');

-- ================================
-- RESIDENTES (6 registros)
-- ================================
INSERT INTO RESIDENTES (NOMBRE_RESIDENTE, TELEFONO_RESIDENTE, CASA_RESIDENTES, CORREO_RESIDENTE, CONTRASEÑA_RESIDENTES)
VALUES 
('Ana Gómez', '3111111111', 'Casa 1', 'ana@gmail.com', 'pass123'),
('Luis Torres', '3222222222', 'Casa 2', 'luis@hotmail.com', 'luis456'),
('María López', '3333333333', 'Casa 3', 'maria@yahoo.com', 'maria789'),
('Pedro Ruiz', '3444444444', 'Casa 4', 'pedro@gmail.com', 'pedro321'),
('Sofía Díaz', '3555555555', 'Casa 5', 'sofia@hotmail.com', 'sofia654'),
('Juan Castro', '3666666666', 'Casa 6', 'juan@gmail.com', 'juan987');

-- ================================
-- ESTADOS AYUDA
-- ================================
INSERT INTO ESTADO_AYUDA (NOMBRE_ESTADO)
VALUES ('pendiente'), ('en proceso'), ('solucionado');

-- AYUDAS (6 registros)
INSERT INTO AYUDAS (DESCRIPCION_AYUDA, ID_ESTADO_AYUDA)
VALUES
('Reparación de tubería', 1),   -- pendiente
('Corte de césped', 3),         -- solucionado
('Mantenimiento de ascensor', 1),
('Limpieza de piscina', 3),
('Arreglo de iluminación', 2),  -- en proceso
('Revisión de portón eléctrico', 1);

-- ================================
-- ALERTAS (6 registros)
-- ================================
INSERT INTO ALERTAS (NOMBRE_ALERTA, DESCRIPCION_ALERTA)
VALUES
('Alerta Incendio', 'Humo detectado en Casa 2'),
('Alerta Seguridad', 'Intento de ingreso no autorizado'),
('Alerta Agua', 'Fuga en red principal'),
('Alerta Energía', 'Corte de luz programado'),
('Alerta Mascota', 'Perro extraviado en conjunto'),
('Alerta Ruido', 'Fiesta en Casa 5');

-- ================================
-- ESTADOS COMPRA
-- ================================
INSERT INTO ESTADO_COMPRA (NOMBRE_ESTADO)
VALUES ('pendiente'), ('pagado'), ('cancelado');

-- COMPRAS (6 registros)
INSERT INTO COMPRAS (TIPO_COMPRA, PRECIO_COMPRA, ID_ESTADO_COMPRA)
VALUES
('Pintura', 500000, 2),          -- pagado
('Sillas plásticas', 200000, 1), -- pendiente
('Bombillos LED', 100000, 2),
('Cámara de seguridad', 800000, 1),
('Plantas ornamentales', 150000, 2),
('Equipo de sonido', 600000, 1);

-- ================================
-- ESTADOS REUNIONES
-- ================================
INSERT INTO ESTADO_REUNION (NOMBRE_ESTADO)
VALUES ('programada'), ('realizada'), ('pendiente');

-- REUNIONES (6 registros)
INSERT INTO REUNIONES (FECHAHORA_REUNIONES, MOTIVO_REUNIONES, ID_ESTADO_REUNION)
VALUES
('2025-09-10 18:00:00', 'Revisión de presupuesto', 2), -- realizada
('2025-09-15 19:00:00', 'Elección de nuevo administrador', 1), -- programada
('2025-09-20 17:30:00', 'Mantenimiento general', 2),
('2025-09-25 18:30:00', 'Problemas de seguridad', 1),
('2025-10-01 19:00:00', 'Fiesta de integración', 1),
('2025-10-05 18:00:00', 'Actualización de normas', 3); -- pendiente

-- ================================
-- REPORTES (6 registros, relacionados con ADMINISTRADOR = 1)
-- ================================
INSERT INTO REPORTES (ID_ADMINISTRADOR, TIPO_REPORTE, FECHAHORA_REPORTES, DESCRIPCION_REPORTE)
VALUES
(1, 'Mantenimiento', '2025-09-01 09:00:00', 'Se revisaron ascensores'),
(1, 'Seguridad', '2025-09-02 10:30:00', 'Incidente en portería'),
(1, 'Agua', '2025-09-03 08:45:00', 'Fuga detectada en bloque A'),
(1, 'Energía', '2025-09-04 14:20:00', 'Apagón temporal en bloque B'),
(1, 'Limpieza', '2025-09-05 11:00:00', 'Jornada de limpieza general'),
(1, 'Eventos', '2025-09-06 16:00:00', 'Preparación de feria comunitaria');

-- ================================
-- ESTADOS FERIAS
-- ================================
INSERT INTO ESTADO_FERIA (NOMBRE_ESTADO)
VALUES ('activo'), ('inactivo');

-- FERIAS (6 registros, relacionados con ADMINISTRADOR = 1)
INSERT INTO FERIAS (ID_ADMINISTRADOR, FECHAHORA_FERIAS, DESCRIPCION_FERIAS, ID_ESTADO_FERIA)
VALUES
(1, '2025-09-15 10:00:00', 'Feria de Tecnología', 1),   -- activo
(1, '2025-09-20 09:00:00', 'Feria Gastronómica', 1),
(1, '2025-09-25 11:00:00', 'Feria de Mascotas', 1),
(1, '2025-10-01 15:00:00', 'Feria de Emprendimiento', 1),
(1, '2025-10-05 14:00:00', 'Feria de Arte', 2),         -- inactivo
(1, '2025-10-10 16:00:00', 'Feria de Seguridad Comunitaria', 1);

