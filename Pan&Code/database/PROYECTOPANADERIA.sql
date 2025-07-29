-- ==================================================================
-- PROYECTO PANADERÍA - BASE DE DATOS
-- ==================================================================
-- Autor: Sistema de Gestión de Panadería
-- Fecha: 2025
-- Descripción: Base de datos completa para gestión de panadería
-- Versión: 2.1 - Incluye sistema de logs y actualización de estructura
-- ==================================================================

-- CARACTERÍSTICAS PRINCIPALES:
-- - Gestión completa de productos con descripción opcional
-- - Sistema de pedidos con detalles de productos
-- - Control de inventario y stock mínimo
-- - Gestión de empleados, clientes y administradores
-- - Sistema de logs para auditoría de cambios en productos
-- - Triggers automáticos para actualización de totales
-- - Funciones de utilidad para encriptación y generación de códigos
-- - Estructura optimizada para dashboard de empleados y administradores

-- ESTRUCTURA DE TABLAS PRINCIPALES:
-- - Productos: Incluye DESCRIPCION_PRODUCTO opcional
-- - Detalle_Pedidos: Sin columnas de imagen, solo datos esenciales
-- - Empleados: Con control de estado activo/inactivo
-- - Pedidos: Con estados y totales automáticos
-- - Sistema de logs: Para auditoría de cambios

-- INSTRUCCIONES DE INSTALACIÓN:
-- 
-- OPCIÓN 1: INSTALACIÓN NUEVA (Base de datos desde cero)
-- 1. Ejecutar todo el script completo
-- 2. La base de datos se creará con todas las tablas y datos
--
-- OPCIÓN 2: ACTUALIZACIÓN DE BASE EXISTENTE
-- 1. Ejecutar solo las secciones de "MIGRACIONES Y ACTUALIZACIONES"
-- 2. Verificar que los cambios se aplicaron correctamente
-- 3. Los datos existentes se mantendrán intactos
--
-- ==================================================================

-- Configuración inicial
DROP DATABASE IF EXISTS ProyectoPanaderia;
SET SQL_SAFE_UPDATES = 0;
CREATE DATABASE ProyectoPanaderia;
USE ProyectoPanaderia;

-- ==================================================================
-- DDL (DATA DEFINITION LANGUAGE) - DEFINICIÓN DE ESTRUCTURA
-- ==================================================================

-- ==================================================================
-- CREACIÓN DE TABLAS PRINCIPALES
-- ==================================================================

-- Tabla: Clientes - Almacena información de los clientes
CREATE TABLE Clientes (
    ID_CLIENTE INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_CLI VARCHAR(100) NOT NULL, 
    TELEFONO_CLI VARCHAR(20),
    ACTIVO_CLI BOOLEAN DEFAULT TRUE,    
    EMAIL_CLI VARCHAR(100),
    CONTRASEÑA_CLI VARCHAR(255),
    SALT_CLI VARCHAR(32)
);

-- Tabla: Empleados - Almacena información de los empleados
CREATE TABLE Empleados (
    ID_EMPLEADO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_EMPLEADO VARCHAR(100) NOT NULL,
    EMAIL_EMPLEADO VARCHAR(100) NOT NULL,
    ACTIVO_EMPLEADO BOOLEAN DEFAULT TRUE,
    CONTRASEÑA_EMPLEADO VARCHAR(255),
    SALT_EMPLEADO VARCHAR(32),
    FECHA_REGISTRO TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: Administradores - Almacena información de los administradores 
CREATE TABLE Administradores (
    ID_ADMIN INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ADMIN VARCHAR(100) NOT NULL,    
    TELEFONO_ADMIN VARCHAR(20),            
    EMAIL_ADMIN VARCHAR(100),
    CONTRASEÑA_ADMIN VARCHAR(255),
    SALT_ADMIN VARCHAR(32)
);

-- Tabla: Proveedores - Almacena información de los proveedores
CREATE TABLE Proveedores (
    ID_PROVEEDOR INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_PROV VARCHAR(100) NOT NULL,    
    TELEFONO_PROV VARCHAR(20) NOT NULL,
    ACTIVO_PROV BOOLEAN DEFAULT TRUE,
    EMAIL_PROV VARCHAR(100) NOT NULL,
    DIRECCION_PROV VARCHAR(200)
);

-- Tabla: Pedidos_Proveedores - Almacena pedidos realizados a proveedores
CREATE TABLE Pedidos_Proveedores (
    ID_PEDIDO_PROV INT PRIMARY KEY AUTO_INCREMENT,
    ID_PROVEEDOR INT NOT NULL,
    NUMERO_PEDIDO INT NOT NULL,
    FECHA_PEDIDO DATE NOT NULL,
    ESTADO_PEDIDO VARCHAR(50) DEFAULT 'Pendiente',
    CONSTRAINT FK_PEDIDO_PROVEEDOR
        FOREIGN KEY (ID_PROVEEDOR) REFERENCES Proveedores(ID_PROVEEDOR) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Categoria_Productos - Almacena categorías de productos
CREATE TABLE Categoria_Productos (
    ID_CATEGORIA_PRODUCTO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_CATEGORIAPRODUCTO VARCHAR(100) NOT NULL
);

-- Tabla: Estado_Pedidos - Almacena estados de pedidos
CREATE TABLE Estado_Pedidos (
    ID_ESTADO_PEDIDO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ESTADO VARCHAR(50)
);

-- Tabla: Categoria_Ingredientes - Almacena categorías de ingredientes
CREATE TABLE Categoria_Ingredientes (
    ID_CATEGORIA INT PRIMARY KEY AUTO_INCREMENT, 
    NOMBRE_CATEGORIA_INGREDIENTE VARCHAR(100) NOT NULL
);

-- Tabla: Ingredientes - Almacena ingredientes utilizados en productos
CREATE TABLE Ingredientes (
    ID_INGREDIENTE INT PRIMARY KEY AUTO_INCREMENT,
    ID_PROVEEDOR INT,
    ID_CATEGORIA INT,
    NOMBRE_INGREDIENTE VARCHAR(100) NOT NULL,
    CANTIDAD_INGREDIENTE INT NOT NULL, 
    FECHA_VENCIMIENTO DATE NOT NULL,
    REFERENCIA_INGREDIENTE VARCHAR(100) NOT NULL,
    FECHA_ENTREGA_INGREDIENTE DATE NOT NULL,
    CONSTRAINT FK_PROVEEDOR_INGREDIENTE
        FOREIGN KEY (ID_PROVEEDOR) REFERENCES Proveedores(ID_PROVEEDOR) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_CATEGORIA_INGREDIENTE
        FOREIGN KEY (ID_CATEGORIA) REFERENCES Categoria_Ingredientes(ID_CATEGORIA) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Productos - Almacena productos de la panadería
CREATE TABLE Productos (
    ID_PRODUCTO INT PRIMARY KEY AUTO_INCREMENT,
    ID_ADMIN INT,
    ID_CATEGORIA_PRODUCTO INT,
    NOMBRE_PRODUCTO VARCHAR(100) NOT NULL,   
    DESCRIPCION_PRODUCTO TEXT,
    PRODUCTO_STOCK_MIN INT NOT NULL,
    PRECIO_PRODUCTO DECIMAL(10,2) NOT NULL,    
    FECHA_VENCIMIENTO_PRODUCTO DATE NOT NULL,
    FECHA_INGRESO_PRODUCTO DATE NOT NULL,
    TIPO_PRODUCTO_MARCA VARCHAR(100) NOT NULL,
    ACTIVO BOOLEAN DEFAULT TRUE,
    FECHA_ULTIMA_MODIFICACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT FK_CATEGORIA_PRODUCTO
        FOREIGN KEY (ID_CATEGORIA_PRODUCTO) REFERENCES Categoria_Productos(ID_CATEGORIA_PRODUCTO) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_ADMIN_PRODUCTO
        FOREIGN KEY (ID_ADMIN) REFERENCES Administradores(ID_ADMIN) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Pedidos - Almacena pedidos realizados por clientes
CREATE TABLE Pedidos (
    ID_PEDIDO INT PRIMARY KEY AUTO_INCREMENT,
    ID_CLIENTE INT,
    ID_EMPLEADO INT,
    ID_ESTADO_PEDIDO INT,
    FECHA_INGRESO DATETIME NOT NULL,
    FECHA_ENTREGA DATETIME NOT NULL,
    TOTAL_PRODUCTO DECIMAL(10,2) NOT NULL,
    CONSTRAINT FK_CLIENTE_PEDIDO
        FOREIGN KEY (ID_CLIENTE) REFERENCES Clientes(ID_CLIENTE) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_EMPLEADO_PEDIDO
        FOREIGN KEY (ID_EMPLEADO) REFERENCES Empleados(ID_EMPLEADO) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_ESTADO_PEDIDO_PEDIDO
        FOREIGN KEY (ID_ESTADO_PEDIDO) REFERENCES Estado_Pedidos(ID_ESTADO_PEDIDO) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Detalle_Pedidos - Almacena detalles de productos por pedido
CREATE TABLE Detalle_Pedidos (
    ID_DETALLE INT PRIMARY KEY AUTO_INCREMENT,
    ID_PEDIDO INT NOT NULL,
    ID_PRODUCTO INT NOT NULL,
    CANTIDAD_PRODUCTO INT NOT NULL,
    PRECIO_UNITARIO DECIMAL(10,2) NOT NULL,
    SUBTOTAL DECIMAL(10,2) NOT NULL,
    CONSTRAINT FK_DETALLE_PEDIDO
        FOREIGN KEY (ID_PEDIDO) REFERENCES Pedidos(ID_PEDIDO) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_DETALLE_PRODUCTO
        FOREIGN KEY (ID_PRODUCTO) REFERENCES Productos(ID_PRODUCTO) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Ordenes_Salida - Almacena facturas de pedidos
CREATE TABLE Ordenes_Salida (
    ID_FACTURA INT PRIMARY KEY AUTO_INCREMENT,
    ID_CLIENTE INT,
    ID_PEDIDO INT,
    FECHA_FACTURACION DATETIME NOT NULL,
    TOTAL_FACTURA DECIMAL(10,2) NOT NULL,
    CONSTRAINT FK_ORDENSALIDA_CLIENTE
        FOREIGN KEY (ID_CLIENTE) REFERENCES Clientes(ID_CLIENTE) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_ORDENSALIDA_PEDIDO
        FOREIGN KEY (ID_PEDIDO) REFERENCES Pedidos(ID_PEDIDO) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- ==================================================================
-- TABLA DE LOGS PARA CAMBIOS EN PRODUCTOS
-- ==================================================================

-- Tabla para registrar cambios en productos (logs de auditoría)
CREATE TABLE productos_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    tipo_cambio ENUM('precio', 'stock', 'activacion', 'desactivacion') NOT NULL,
    valor_anterior VARCHAR(50),
    valor_nuevo VARCHAR(50),
    usuario_id INT,
    usuario_tipo ENUM('admin', 'empleado') DEFAULT 'admin',
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_usuario VARCHAR(45),
    INDEX idx_producto_logs_fecha (fecha_cambio),
    INDEX idx_producto_logs_producto (producto_id),
    INDEX idx_producto_logs_tipo (tipo_cambio),
    CONSTRAINT FK_PRODUCTO_LOG
        FOREIGN KEY (producto_id) REFERENCES Productos(ID_PRODUCTO) ON DELETE CASCADE
);

-- Trigger para actualizar FECHA_ULTIMA_MODIFICACION automáticamente
DELIMITER //
CREATE TRIGGER tr_actualizar_fecha_producto 
    BEFORE UPDATE ON Productos
    FOR EACH ROW
BEGIN
    SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
END//
DELIMITER ;

-- ==================================================================
-- FUNCIONES DE UTILIDAD
-- ==================================================================

-- Función: GenerateSalt - Genera salt aleatorio para encriptación
DELIMITER //

CREATE FUNCTION GenerateSalt() 
RETURNS VARCHAR(32)
READS SQL DATA
DETERMINISTIC
BEGIN
    RETURN SUBSTRING(MD5(CONCAT(RAND(), NOW(), CONNECTION_ID())), 1, 32);
END//

DELIMITER ;

-- Función: HashPassword - Hashea contraseñas con salt
DELIMITER //

CREATE FUNCTION HashPassword(password VARCHAR(255), salt VARCHAR(32)) 
RETURNS VARCHAR(255)
READS SQL DATA
DETERMINISTIC
BEGIN
    RETURN SHA2(CONCAT(password, salt), 256);
END//

DELIMITER ;

-- Función: GenerarCodigoProducto - Genera códigos de producto por categoría
DELIMITER //
CREATE FUNCTION GenerarCodigoProducto(categoria_id INT, producto_id INT) 
RETURNS VARCHAR(50)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE prefijo VARCHAR(10);
    
    -- Obtener prefijo según categoría
    SELECT CASE 
        WHEN categoria_id = 1 THEN 'TL'  -- Tortas Tres Leches
        WHEN categoria_id = 2 THEN 'TM'  -- Tortas Milkyway
        WHEN categoria_id = 3 THEN 'TE'  -- Tortas Encargo
        WHEN categoria_id = 4 THEN 'PG'  -- Pan Grande
        WHEN categoria_id = 5 THEN 'PP'  -- Pan Pequeño
        WHEN categoria_id = 6 THEN 'PS'  -- Postres
        WHEN categoria_id = 7 THEN 'GA'  -- Galletas
        WHEN categoria_id = 8 THEN 'TA'  -- Tamales
        WHEN categoria_id = 9 THEN 'YO'  -- Yogures
        WHEN categoria_id = 10 THEN 'PC' -- Pasteles Pollo
        WHEN categoria_id = 11 THEN 'BE' -- Bebidas
        WHEN categoria_id = 12 THEN 'PI' -- Panadería Integral
        ELSE 'PR'
    END INTO prefijo;
    
    RETURN CONCAT(prefijo, '-', LPAD(producto_id, 4, '0'));
END//
DELIMITER ;

-- Función: GenerarNumeroPedido - Genera números de pedido automáticos
DELIMITER //
CREATE FUNCTION GenerarNumeroPedido(pedido_id INT) 
RETURNS VARCHAR(50)
READS SQL DATA
DETERMINISTIC
BEGIN
    RETURN CONCAT('PED-', YEAR(NOW()), '-', LPAD(pedido_id, 6, '0'));
END//
DELIMITER ;

-- ==================================================================
-- TRIGGERS
-- ==================================================================

-- Trigger: Actualizar total del pedido cuando se inserta detalle
DELIMITER //

CREATE TRIGGER tr_actualizar_total_pedido
AFTER INSERT ON Detalle_Pedidos
FOR EACH ROW
BEGIN
    UPDATE Pedidos 
    SET TOTAL_PRODUCTO = (
        SELECT SUM(SUBTOTAL) 
        FROM Detalle_Pedidos 
        WHERE ID_PEDIDO = NEW.ID_PEDIDO
    )
    WHERE ID_PEDIDO = NEW.ID_PEDIDO;
END//

DELIMITER ;

-- Trigger: Actualizar total del pedido cuando se modifica detalle
DELIMITER //

CREATE TRIGGER tr_actualizar_total_pedido_update
AFTER UPDATE ON Detalle_Pedidos
FOR EACH ROW
BEGIN
    UPDATE Pedidos 
    SET TOTAL_PRODUCTO = (
        SELECT SUM(SUBTOTAL) 
        FROM Detalle_Pedidos 
        WHERE ID_PEDIDO = NEW.ID_PEDIDO
    )
    WHERE ID_PEDIDO = NEW.ID_PEDIDO;
END//

DELIMITER ;

-- ==================================================================
-- PROCEDIMIENTOS ALMACENADOS
-- ==================================================================

-- Procedimiento: sp_productos_por_categoria - Busca productos por categoría
DELIMITER //

CREATE PROCEDURE sp_productos_por_categoria(
    IN nombre_categoria VARCHAR(100)
)
BEGIN
    -- Buscar productos por nombre de categoría
    SELECT 
        p.ID_PRODUCTO,
        p.NOMBRE_PRODUCTO,
        p.PRECIO_PRODUCTO,
        p.PRODUCTO_STOCK_MIN,
        p.TIPO_PRODUCTO_MARCA,
        cp.NOMBRE_CATEGORIAPRODUCTO as CATEGORIA
    FROM Productos p
    INNER JOIN Categoria_Productos cp ON p.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO
    WHERE cp.NOMBRE_CATEGORIAPRODUCTO LIKE CONCAT('%', nombre_categoria, '%')
    ORDER BY p.NOMBRE_PRODUCTO;
    
    -- Mostrar el total de productos encontrados
    SELECT COUNT(*) as TOTAL_PRODUCTOS_ENCONTRADOS
    FROM Productos p
    INNER JOIN Categoria_Productos cp ON p.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO
    WHERE cp.NOMBRE_CATEGORIAPRODUCTO LIKE CONCAT('%', nombre_categoria, '%');
END//

DELIMITER ;

-- Procedimiento: sp_crear_pedido - Crear un nuevo pedido
DELIMITER //

CREATE PROCEDURE sp_crear_pedido(
    IN p_id_cliente INT,
    IN p_id_empleado INT,
    IN p_fecha_entrega DATETIME
)
BEGIN
    DECLARE nuevo_id_pedido INT;
    
    -- Insertar el pedido
    INSERT INTO Pedidos (ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO)
    VALUES (p_id_cliente, p_id_empleado, 1, NOW(), p_fecha_entrega, 0.00);
    
    -- Obtener el ID del pedido recién creado
    SET nuevo_id_pedido = LAST_INSERT_ID();
    
    -- Devolver el ID del nuevo pedido
    SELECT nuevo_id_pedido as NUEVO_ID_PEDIDO;
END//

DELIMITER ;

-- ==================================================================
-- DML (DATA MANIPULATION LANGUAGE) - INSERCIÓN DE DATOS
-- ==================================================================

-- ==================================================================
-- INSERCIÓN DE DATOS BASE
-- ==================================================================

-- Inserción en tabla: Clientes
INSERT INTO Clientes (NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI) VALUES
('Ana Pérez', '3101234567', 'ana.p@mail.com'),
('Luis Gómez', '3209876543', 'luis.g@mail.com'),
('Maria Rodriguez', '3001122334', 'maria.r@mail.com');

-- Inserción en tabla: Empleados 
INSERT INTO Empleados (NOMBRE_EMPLEADO) VALUES
('Andres Alkaeda'),
('Damian Avila'),
('Brayan Jimenez'),
('Ana Goyeneche'),
('Sharyt Zamora');

-- Inserción en tabla: Administradores 
INSERT INTO Administradores (NOMBRE_ADMIN, TELEFONO_ADMIN, EMAIL_ADMIN) VALUES
('Admin Uno', '3005550101', 'admin1@store.com');

-- Inserción en tabla: Proveedores
INSERT INTO `proveedores` (`ID_PROVEEDOR`, `NOMBRE_PROV`) VALUES
('1', 'Harina Dorada'),
('2', 'Dulce Granero'),
('3', 'El Horno Mágico S.A.S.'),
('4', 'Masa Maestra Distribuciones'),
('5', 'Insumos Panaderos del Sol'),
('6', 'La Esencia del Pan'),
('7', 'Proveedora Integral del Panadero'),
('8', 'Alimentos para Hornear Cía. Ltda.');

-- Inserción en tabla: Pedidos_Proveedores
INSERT INTO Pedidos_Proveedores (ID_PROVEEDOR, NUMERO_PEDIDO, FECHA_PEDIDO, ESTADO_PEDIDO) VALUES
(1, 1001, '2024-01-15', 'Entregado'),
(2, 1002, '2024-02-10', 'Entregado'),
(3, 1003, '2024-03-05', 'Entregado');

-- Inserción en tabla: Categoria_Productos
INSERT INTO Categoria_Productos (NOMBRE_CATEGORIAPRODUCTO) VALUES
('Tortas Tres Leches'),
('Tortas Milyway'),
('Tortas por Encargo'),
('Pan Grande'),
('Pan Pequeño'),
('Postres'),
('Galletas'),
('Tamales'),
('Yogures'),
('Pasteles Pollo');

-- Inserción en tabla: Estado_Pedidos 
INSERT INTO Estado_Pedidos (NOMBRE_ESTADO) VALUES
('Pendiente'),
('En Preparación'),
('Listo para Entrega'),
('Entregado'),
('Cancelado');

-- Inserción en tabla: Categoria_Ingredientes
INSERT INTO `categoria_ingredientes` (`ID_CATEGORIA`, `NOMBRE_CATEGORIA_INGREDIENTE`) VALUES
('1', 'Harinas'),
('2', 'Lácteos y Derivados'),
('3', 'Azúcares y Endulzantes'),
('4', 'Grasas'),
('5', 'Esencias'),
('6', 'Fruta'),
('7', 'Frutos Secos'),
('8', 'Levaduras'),
('9', 'Huevos'),
('10', 'Chocolate y Cacao'),
('11', 'Espesantes y Gelificantes'),
('12', 'Colorantes Alimentarios'),
('13', 'Sal'),
('14', 'Aditivos y Mejoradores'),
('15', 'Semillas'),
('16', 'Coberturas y Rellenos');

-- Inserción en tabla: Ingredientes 
INSERT INTO `ingredientes` (`ID_INGREDIENTE`, `ID_PROVEEDOR`, `ID_CATEGORIA`, `NOMBRE_INGREDIENTE`, `CANTIDAD_INGREDIENTE`, `FECHA_VENCIMIENTO`, `REFERENCIA_INGREDIENTE`) VALUES
('1', '2', '1', 'Harina de Trigo', '100', '2025-12-20', 'HAR-TRG-05'),
('2', '1', '2', 'Leche Entera UHT', '30', '2025-08-01', 'LECH-ENT-1L'),
('3', '3', '3', 'Azúcar Blanca', '70', '2026-01-30', 'AZUC-BLN-KG'),
('4', '4', '4', 'Mantequilla sin Sal', '25', '2025-09-15', 'MANT-SS-KG'),
('5', '5', '9', 'Huevos Grandes', '120', '2025-07-25', 'HUEV-GR-DZ'),
('6', '6', '10', 'Chocolate Semi-Amargo (Gotas)', '15', '2026-03-10', 'CHOC-SM-KG'),
('7', '7', '8', 'Levadura Fresca', '5', '2025-07-10', 'LEV-FRES-GR'),
('8', '1', '3', 'Azúcar Moreno', '10', '2026-02-20', 'AZUC-MRN-KG'),
('9', '2', '1', 'Harina Integral', '50', '2025-11-01', 'HAR-INT-02'),
('10', '3', '4', 'Aceite Vegetal', '20', '2026-05-01', 'ACET-VEG-LT'),
('11', '4', '10', 'Cacao en Polvo', '8', '2026-04-15', 'CACAO-POL-KG'),
('12', '5', '6', 'Manzanas Verdes (Kg)', '10', '2025-07-12', 'MANZ-VRD-KG'),
('13', '6', '7', 'Nueces Picadas', '5', '2025-10-01', 'NUEZ-PIC-KG'),
('14', '7', '5', 'Esencia de Vainilla', '2', '2027-01-01', 'ESEN-VN-LT'),
('15', '8', '13', 'Sal Fina', '2', '2028-01-01', 'SAL-FIN-KG'),
('16', '1', '2', 'Crema de Leche', '5', '2025-08-05', 'CREM-LECH-LT'),
('17', '2', '11', 'Gelatina sin Sabor', '1', '2026-09-01', 'GEL-SS-KG'),
('18', '3', '12', 'Colorante Alimentario Rojo', '0.5', '2027-03-01', 'COLR-ROJ-ML'),
('19', '4', '15', 'Semillas de Sésamo', '3', '2026-06-01', 'SEM-SES-KG'),
('20', '5', '16', 'Dulce de Leche', '10', '2025-11-15', 'DDL-KG');

-- Inserción en tabla: Productos 
INSERT INTO `productos` (`ID_PRODUCTO`, `ID_ADMIN`, `ID_CATEGORIA_PRODUCTO`, `NOMBRE_PRODUCTO`, `DESCRIPCION_PRODUCTO`, `PRODUCTO_STOCK_MIN`, `PRECIO_PRODUCTO`, `FECHA_VENCIMIENTO_PRODUCTO`, `FECHA_INGRESO_PRODUCTO`, `TIPO_PRODUCTO_MARCA`) VALUES
('1', '1', '8', 'Tamales Tolimenses', 'Tradicionales tamales envueltos en hoja de plátano, con masa de maíz y relleno de carne de cerdo y pollo', '10', '3800.00', '2025-09-15', '2025-07-01', 'Propio'),
('2', '1', '4', 'Pan Tajado Integral', 'Pan de molde integral, ideal para desayunos saludables, rico en fibra', '15', '4200.00', '2025-07-02', '2025-07-01', 'Propio'),
('3', '1', '7', 'Yogurt Fresa Litro', 'Yogurt cremoso con trozos de fresa natural, sin conservantes artificiales', '12', '6000.00', '2025-07-30', '2025-07-03', 'Alpina'),
('4', '1', '5', 'Galleta de Tres Ojos', 'Galleta tradicional colombiana con tres círculos de dulce, crujiente y deliciosa', '20', '2500.00', '2025-11-01', '2025-07-01', 'Propio'),
('5', '1', '1', 'Pan Campesino Grande', 'Pan artesanal de corteza dorada y miga suave, ideal para acompañar comidas', '8', '5500.00', '2025-07-08', '2025-07-04', 'Propio'),
('6', '1', '3', 'Torta de Chocolate Pequeña', 'Deliciosa torta de chocolate húmeda con cobertura de chocolate, perfecta para ocasiones especiales', '5', '18000.00', '2025-07-07', '2025-07-04', 'Propio'),
('7', '1', '2', 'Croissant de Almendras', NULL, '18', '3500.00', '2025-07-06', '2025-07-05', 'Propio'),
('8', '1', '1', 'Baguette Clásica', 'Pan francés tradicional con corteza crujiente y miga aireada', '25', '2800.00', '2025-07-06', '2025-07-05', 'Propio'),
('9', '1', '5', 'Bizcochos de Achira', NULL, '15', '4000.00', '2025-12-01', '2025-07-01', 'Propio'),
('10', '1', '6', 'Jugo de Naranja Natural (500ml)', 'Jugo 100% natural exprimido de naranjas frescas, sin azúcar añadido', '10', '4500.00', '2025-07-05', '2025-07-04', 'Postobón'),
('11', '1', '7', 'Postre de Tres Leches', 'Clásico postre colombiano empapado en tres tipos de leche, suave y cremoso', '7', '7500.00', '2025-07-08', '2025-07-04', 'Propio'),
('12', '1', '4', 'Pan Blanco de Molde', NULL, '20', '3900.00', '2025-07-02', '2025-07-01', 'Propio'),
('13', '1', '3', 'Muffin de Arándanos', 'Muffin esponjoso con arándanos frescos, perfecto para el desayuno o merienda', '15', '3000.00', '2025-07-07', '2025-07-04', 'Propio'),
('14', '1', '2', 'Pan de Bono Pequeño', NULL, '30', '1500.00', '2025-07-06', '2025-07-05', 'Propio'),
('15', '1', '8', 'Empanadas de Carne (unidad)', 'Empanada frita rellena de carne molida sazonada con especias tradicionales', '20', '2000.00', '2025-07-06', '2025-07-05', 'Propio'),
('16', '1', '3', 'Brazo de Reina', 'Bizcocho enrollado relleno de dulce de leche y cubierto con coco rallado', '10', '9500.00', '2025-07-09', '2025-07-04', 'Propio'),
('17', '1', '1', 'Pan Trenza Integral', NULL, '12', '4800.00', '2025-07-07', '2025-07-03', 'Propio'),
('18', '1', '5', 'Galletas Surtidas de Mantequilla', 'Variedad de galletas caseras de mantequilla con diferentes formas y sabores', '25', '3200.00', '2025-12-30', '2025-07-01', 'Propio'),
('19', '1', '7', 'Avena La Lechera (500ml)', NULL, '18', '5800.00', '2025-08-20', '2025-07-03', 'Nestlé'),
('20', '1', '9', 'Ponqué de Naranja (Porción)', 'Porción individual de ponqué de naranja con glaseado cítrico', '15', '3000.00', '2025-07-08', '2025-07-04', 'Propio'),
('21', '1', '10', 'Pan Artesanal de Masa Madre', 'Pan elaborado con masa madre natural, fermentación larga para mejor digestibilidad', '7', '8000.00', '2025-07-07', '2025-07-05', 'Propio'),
('22', '1', '3', 'Cheesecake de Frutos Rojos', 'Cheesecake cremoso con base de galleta y cobertura de frutos rojos frescos', '6', '25000.00', '2025-07-09', '2025-07-04', 'Propio'),
('23', '1', '4', 'Pan de Hamburguesa', NULL, '30', '4500.00', '2025-07-10', '2025-07-02', 'Propio'),
('24', '1', '5', 'Galletas de Avena y Pasas', 'Galletas nutritivas con avena integral y pasas, sin azúcar refinado', '22', '2700.00', '2026-01-01', '2025-07-01', 'Propio'),
('25', '1', '7', 'Kumiss Natural', NULL, '10', '4900.00', '2025-07-25', '2025-07-03', 'Alquería'),
('26', '1', '9', 'Brownie con Nuez', 'Brownie de chocolate intenso con trozos de nuez, húmedo y delicioso', '40', '1800.00', '2025-07-08', '2025-07-05', 'Propio'),
('27', '1', '1', 'Pan Blandito', NULL, '28', '2500.00', '2025-07-07', '2025-07-05', 'Propio'),
('28', '1', '3', 'Milhoja de Arequipe', 'Delicada milhoja rellena de arequipe casero y cubierta con azúcar glass', '12', '6000.00', '2025-07-08', '2025-07-04', 'Propio'),
('29', '1', '2', 'Mogolla Chicharrona', NULL, '15', '3500.00', '2025-07-06', '2025-07-05', 'Propio'),
('30', '1', '8', 'Arequipe (Tarro 500g)', 'Arequipe casero cremoso y dulce, perfecto para postres y acompañamientos', '8', '9000.00', '2026-04-10', '2025-07-01', 'Propio');

-- Inserción en tabla: Pedidos
INSERT INTO Pedidos (ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) VALUES
(1, 1, 2, '2025-06-20 09:00:00', '2025-06-20 15:00:00', 10000.00),
(2, 2, 4, '2025-06-19 10:30:00', '2025-06-19 16:30:00', 7500.00),
(3, 1, 1, '2025-06-21 08:00:00', '2025-06-21 14:00:00', 12500.00);

-- Inserción en tabla: Detalle_Pedidos
INSERT INTO Detalle_Pedidos (ID_PEDIDO, ID_PRODUCTO, CANTIDAD_PRODUCTO, PRECIO_UNITARIO, SUBTOTAL) VALUES
(1, 1, 2, 3500.00, 7000.00),
(1, 3, 1, 6000.00, 6000.00),
(2, 2, 1, 4200.00, 4200.00),
(2, 4, 3, 2500.00, 7500.00),
(3, 1, 3, 3500.00, 10500.00),
(3, 4, 2, 2500.00, 5000.00);

-- Inserción en tabla: Ordenes_Salida
INSERT INTO Ordenes_Salida (ID_CLIENTE, ID_PEDIDO, FECHA_FACTURACION, TOTAL_FACTURA) VALUES
(1, 1, '2025-06-20 15:30:00', 13000.00),
(2, 2, '2025-06-19 17:00:00', 11700.00),
(3, 3, '2025-06-21 14:30:00', 15500.00);

-- ==================================================================
-- DSL (DATA SECURITY LANGUAGE) - SEGURIDAD Y ENCRIPTACIÓN
-- ==================================================================

-- ==================================================================
-- CONFIGURACIÓN DE SEGURIDAD - CONTRASEÑAS
-- ==================================================================

-- Actualizar contraseñas de administradores con encriptación
UPDATE Administradores 
SET SALT_ADMIN = GenerateSalt()
WHERE ID_ADMIN = 1;

UPDATE Administradores 
SET CONTRASEÑA_ADMIN = HashPassword('admin123', SALT_ADMIN)
WHERE ID_ADMIN = 1;

-- Actualizar contraseñas de clientes con encriptación
UPDATE Clientes 
SET SALT_CLI = GenerateSalt()
WHERE ID_CLIENTE IN (1, 2, 3);

UPDATE Clientes 
SET CONTRASEÑA_CLI = HashPassword('cliente123', SALT_CLI)
WHERE ID_CLIENTE = 1;

UPDATE Clientes 
SET CONTRASEÑA_CLI = HashPassword('cliente456', SALT_CLI)
WHERE ID_CLIENTE = 2;

UPDATE Clientes 
SET CONTRASEÑA_CLI = HashPassword('cliente789', SALT_CLI)
WHERE ID_CLIENTE = 3;

-- Actualizar contraseñas de empleados con encriptación
UPDATE Empleados 
SET SALT_EMPLEADO = GenerateSalt()
WHERE ID_EMPLEADO IN (1, 2, 3, 4, 5);

UPDATE Empleados 
SET CONTRASEÑA_EMPLEADO = HashPassword('empleado123', SALT_EMPLEADO)
WHERE ID_EMPLEADO = 1;

UPDATE Empleados 
SET CONTRASEÑA_EMPLEADO = HashPassword('empleado456', SALT_EMPLEADO)
WHERE ID_EMPLEADO = 2;

-- ==================================================================
-- DQL (DATA QUERY LANGUAGE) - CONSULTAS Y VERIFICACIONES
-- ==================================================================

-- ==================================================================
-- CONSULTAS DE VERIFICACIÓN
-- ==================================================================

-- Verificación de registros en todas las tablas
SELECT 'CLIENTES' as Tabla, COUNT(*) as Total_Registros FROM Clientes
UNION ALL
SELECT 'EMPLEADOS', COUNT(*) FROM Empleados
UNION ALL
SELECT 'ADMINISTRADORES', COUNT(*) FROM Administradores
UNION ALL
SELECT 'PROVEEDORES', COUNT(*) FROM Proveedores
UNION ALL
SELECT 'PEDIDOS_PROVEEDORES', COUNT(*) FROM Pedidos_Proveedores
UNION ALL
SELECT 'CATEGORIA_PRODUCTOS', COUNT(*) FROM Categoria_Productos
UNION ALL
SELECT 'ESTADO_PEDIDOS', COUNT(*) FROM Estado_Pedidos
UNION ALL
SELECT 'CATEGORIA_INGREDIENTES', COUNT(*) FROM Categoria_Ingredientes
UNION ALL
SELECT 'INGREDIENTES', COUNT(*) FROM Ingredientes
UNION ALL
SELECT 'PRODUCTOS', COUNT(*) FROM Productos
UNION ALL
SELECT 'PEDIDOS', COUNT(*) FROM Pedidos
UNION ALL
SELECT 'DETALLE_PEDIDOS', COUNT(*) FROM Detalle_Pedidos
UNION ALL
SELECT 'ORDENES_SALIDA', COUNT(*) FROM Ordenes_Salida;

-- Consulta detallada de productos con información completa
SELECT 
    p.ID_PRODUCTO,
    p.NOMBRE_PRODUCTO,
    cp.NOMBRE_CATEGORIAPRODUCTO as CATEGORIA,
    p.PRECIO_PRODUCTO,
    p.PRODUCTO_STOCK_MIN,
    p.TIPO_PRODUCTO_MARCA,
    a.NOMBRE_ADMIN as ADMINISTRADOR,
    p.FECHA_ULTIMA_MODIFICACION
FROM Productos p
INNER JOIN Categoria_Productos cp ON p.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO
INNER JOIN Administradores a ON p.ID_ADMIN = a.ID_ADMIN
ORDER BY p.NOMBRE_PRODUCTO;

-- Consulta detallada de pedidos con información completa
SELECT 
    ped.ID_PEDIDO,
    c.NOMBRE_CLI as CLIENTE,
    e.NOMBRE_EMPLEADO as EMPLEADO,
    ep.NOMBRE_ESTADO as ESTADO,
    ped.FECHA_INGRESO,
    ped.FECHA_ENTREGA,
    p.NOMBRE_PRODUCTO,
    dp.CANTIDAD_PRODUCTO,
    dp.PRECIO_UNITARIO,
    dp.SUBTOTAL
FROM Pedidos ped
INNER JOIN Clientes c ON ped.ID_CLIENTE = c.ID_CLIENTE
INNER JOIN Empleados e ON ped.ID_EMPLEADO = e.ID_EMPLEADO
INNER JOIN Estado_Pedidos ep ON ped.ID_ESTADO_PEDIDO = ep.ID_ESTADO_PEDIDO
INNER JOIN Detalle_Pedidos dp ON ped.ID_PEDIDO = dp.ID_PEDIDO
INNER JOIN Productos p ON dp.ID_PRODUCTO = p.ID_PRODUCTO
ORDER BY ped.ID_PEDIDO, dp.ID_DETALLE;

-- ==================================================================
-- CONSULTAS DE VERIFICACIÓN DE SEGURIDAD
-- ==================================================================

-- Verificación del hasheo de contraseñas de administradores 
SELECT 
    ID_ADMIN,
    NOMBRE_ADMIN,
    SALT_ADMIN,
    CONTRASEÑA_ADMIN,
    LENGTH(CONTRASEÑA_ADMIN) as LONGITUD_HASH
FROM Administradores;

-- Verificación del hasheo de contraseñas de clientes 

SELECT 
    ID_CLIENTE,
    NOMBRE_CLI,
    SALT_CLI,
    CONTRASEÑA_CLI,
    LENGTH(CONTRASEÑA_CLI) as LONGITUD_HASH
FROM Clientes;

-- Verificación del hasheo de contraseñas de empleados 

SELECT 
    ID_EMPLEADO,
    NOMBRE_EMPLEADO,
    SALT_EMPLEADO,
    CONTRASEÑA_EMPLEADO,
    LENGTH(CONTRASEÑA_EMPLEADO) as LONGITUD_HASH
FROM Empleados;

-- Consulta consolidada de todas las contraseñas hasheadas

SELECT 'ADMIN' as TIPO, NOMBRE_ADMIN as NOMBRE, SALT_ADMIN as SALT, CONTRASEÑA_ADMIN as HASH
FROM Administradores
WHERE CONTRASEÑA_ADMIN IS NOT NULL

UNION ALL

SELECT 'CLIENTE' as TIPO, NOMBRE_CLI as NOMBRE, SALT_CLI as SALT, CONTRASEÑA_CLI as HASH
FROM Clientes
WHERE CONTRASEÑA_CLI IS NOT NULL

UNION ALL

SELECT 'EMPLEADO' as TIPO, NOMBRE_EMPLEADO as NOMBRE, SALT_EMPLEADO as SALT, CONTRASEÑA_EMPLEADO as HASH
FROM Empleados
WHERE CONTRASEÑA_EMPLEADO IS NOT NULL;


-- ==================================================================
-- PRUEBAS Y VALIDACIONES
-- ==================================================================

-- Prueba de trigger de actualización de fecha de modificación
UPDATE Productos 
SET PRECIO_PRODUCTO = 3800.00 
WHERE NOMBRE_PRODUCTO = 'Tamales Tolimenses';

-- Verificación de la actualización de fecha de modificación
SELECT 
    NOMBRE_PRODUCTO, 
    PRECIO_PRODUCTO, 
    FECHA_ULTIMA_MODIFICACION
FROM Productos 
WHERE NOMBRE_PRODUCTO = 'Tamales Tolimenses';

-- Pruebas de procedimientos almacenados
CALL sp_productos_por_categoria('Pan');
CALL sp_productos_por_categoria('Torta');
CALL sp_productos_por_categoria('Yogur');

-- Prueba de creación de nuevo pedido
CALL sp_crear_pedido(1, 2, '2025-06-25 16:00:00');

-- ==================================================================
-- VERIFICACIONES FINALES DEL SISTEMA
-- ==================================================================

-- Verificar triggers creados
SHOW TRIGGERS WHERE `Table` IN ('Productos', 'Detalle_Pedidos');

-- Verificar procedimientos almacenados creados
SHOW PROCEDURE STATUS WHERE Name LIKE 'sp_%';

-- Verificar funciones creadas
SHOW FUNCTION STATUS WHERE Name LIKE '%Password%' OR Name LIKE '%Salt%';

-- Verificar estructura de las tablas principales
DESCRIBE Productos;
DESCRIBE Pedidos;
DESCRIBE Detalle_Pedidos;

-- Script para actualizar estados de pedidos
-- Ejecutar solo si es necesario ajustar los estados

-- Verificar estados existentes
SELECT * FROM Estado_Pedidos;

-- Si necesitas ajustar los estados, puedes usar estas consultas:
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'Pendiente' WHERE ID_ESTADO_PEDIDO = 1;
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'En Preparación' WHERE ID_ESTADO_PEDIDO = 2;
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'Listo' WHERE ID_ESTADO_PEDIDO = 3;
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'Entregado' WHERE ID_ESTADO_PEDIDO = 4;
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'Cancelado' WHERE ID_ESTADO_PEDIDO = 5;

-- O insertar si no existen:
-- INSERT INTO Estado_Pedidos (NOMBRE_ESTADO) VALUES 
-- ('Pendiente'),
-- ('En Preparación'),
-- ('Listo'),
-- ('Entregado'),
-- ('Cancelado');

-- ==================================================================
-- MIGRACIONES Y ACTUALIZACIONES DEL SISTEMA
-- ==================================================================

-- ==================================================================
-- MIGRACIÓN: ACTUALIZAR TIPO DE DATO PRECIO_PRODUCTO
-- ==================================================================
-- NOTA: Solo ejecutar si la base de datos ya existe y necesita actualización
-- Descomentar las siguientes líneas para migrar una base de datos existente:

<<cite:sql>>
-- Actualizar tipo de dato de PRECIO_PRODUCTO de INT a DECIMAL
ALTER TABLE Productos MODIFY COLUMN PRECIO_PRODUCTO DECIMAL(10,2) NOT NULL;

-- Verificar la estructura actualizada
DESCRIBE Productos;
<<cite:sql>>

-- ==================================================================
-- VERIFICACIÓN Y CREACIÓN DE TABLA DE LOGS
-- ==================================================================
-- Script para verificar y crear tabla productos_logs si no existe

-- Verificar si la tabla productos_logs existe
SET @table_exists = (
    SELECT COUNT(*) 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'productos_logs'
);

-- Crear tabla productos_logs si no existe (para bases de datos existentes)
SET @sql = IF(@table_exists = 0, 
    'CREATE TABLE productos_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        producto_id INT NOT NULL,
        tipo_cambio ENUM(''precio'', ''stock'', ''activacion'', ''desactivacion'') NOT NULL,
        valor_anterior VARCHAR(50),
        valor_nuevo VARCHAR(50),
        usuario_id INT,
        usuario_tipo ENUM(''admin'', ''empleado'') DEFAULT ''admin'',
        fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        ip_usuario VARCHAR(45),
        INDEX idx_producto_logs_fecha (fecha_cambio),
        INDEX idx_producto_logs_producto (producto_id),
        INDEX idx_producto_logs_tipo (tipo_cambio),
        CONSTRAINT FK_PRODUCTO_LOG
            FOREIGN KEY (producto_id) REFERENCES Productos(ID_PRODUCTO) ON DELETE CASCADE
    )', 
    'SELECT ''Tabla productos_logs ya existe'' as mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==================================================================
-- VERIFICACIÓN Y CREACIÓN DE TRIGGER
-- ==================================================================
-- Script para verificar y crear trigger si no existe

-- Verificar si el trigger existe
SET @trigger_exists = (
    SELECT COUNT(*) 
    FROM information_schema.triggers 
    WHERE trigger_schema = DATABASE() 
    AND trigger_name = 'tr_actualizar_fecha_producto'
);

-- Crear trigger si no existe
SET @sql_trigger = IF(@trigger_exists = 0,
    'CREATE TRIGGER tr_actualizar_fecha_producto 
        BEFORE UPDATE ON Productos
        FOR EACH ROW
    BEGIN
        SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
    END',
    'SELECT ''Trigger tr_actualizar_fecha_producto ya existe'' as mensaje'
);

PREPARE stmt_trigger FROM @sql_trigger;
EXECUTE stmt_trigger;
DEALLOCATE PREPARE stmt_trigger;

-- ==================================================================
-- CONSULTAS DE MANTENIMIENTO Y VERIFICACIÓN
-- ==================================================================

-- Verificar estructura de tabla Productos
SELECT 
    COLUMN_NAME as 'Campo',
    COLUMN_TYPE as 'Tipo',
    IS_NULLABLE as 'Nulo',
    COLUMN_DEFAULT as 'Por_Defecto',
    EXTRA as 'Extra'
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'Productos'
ORDER BY ORDINAL_POSITION;

-- Verificar existencia de tabla productos_logs
SELECT 
    TABLE_NAME as 'Tabla',
    TABLE_ROWS as 'Filas',
    CREATE_TIME as 'Fecha_Creacion'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('Productos', 'productos_logs');

-- Verificar triggers relacionados con Productos
SELECT 
    TRIGGER_NAME as 'Trigger',
    EVENT_MANIPULATION as 'Evento',
    EVENT_OBJECT_TABLE as 'Tabla',
    TRIGGER_BODY as 'Accion'
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = DATABASE() 
AND EVENT_OBJECT_TABLE = 'Productos';

-- Verificar índices de la tabla productos_logs
SELECT 
    INDEX_NAME as 'Indice',
    COLUMN_NAME as 'Columna',
    NON_UNIQUE as 'No_Unico'
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'productos_logs'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;

-- ==================================================================
-- CONSULTAS DE PRUEBA PARA LOGS
-- ==================================================================

-- Consulta para ver logs recientes (ejecutar después de hacer cambios)
-- SELECT * FROM productos_logs ORDER BY fecha_cambio DESC LIMIT 10;

-- Consulta para ver cambios de un producto específico
-- SELECT 
--     pl.*,
--     p.NOMBRE_PRODUCTO
-- FROM productos_logs pl
-- JOIN Productos p ON pl.producto_id = p.ID_PRODUCTO
-- WHERE pl.producto_id = 1
-- ORDER BY pl.fecha_cambio DESC;

-- Consulta para ver estadísticas de cambios
-- SELECT 
--     tipo_cambio,
--     COUNT(*) as total_cambios,
--     DATE(fecha_cambio) as fecha
-- FROM productos_logs 
-- GROUP BY tipo_cambio, DATE(fecha_cambio)
-- ORDER BY fecha DESC, tipo_cambio;

-- ==================================================================
-- SCRIPT DE LIMPIEZA (OPCIONAL)
-- ==================================================================

-- Limpiar logs antiguos (ejecutar solo si es necesario)
-- DELETE FROM productos_logs WHERE fecha_cambio < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Resetear AUTO_INCREMENT de la tabla logs
-- ALTER TABLE productos_logs AUTO_INCREMENT = 1;

-- ============================================================
-- MIGRACIÓN: Agregar columna FECHA_REGISTRO a tabla Empleados
-- ============================================================

-- Verificar y agregar columna FECHA_REGISTRO a la tabla Empleados si no existe
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'Empleados' 
     AND COLUMN_NAME = 'FECHA_REGISTRO') = 0,
    'ALTER TABLE Empleados ADD COLUMN FECHA_REGISTRO TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'SELECT "La columna FECHA_REGISTRO ya existe en la tabla Empleados" as mensaje'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================
-- MIGRACIÓN: Agregar columna DESCRIPCION_PRODUCTO a tabla Productos
-- ============================================================

-- Verificar y agregar columna DESCRIPCION_PRODUCTO a la tabla Productos si no existe
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'Productos' 
     AND COLUMN_NAME = 'DESCRIPCION_PRODUCTO') = 0,
    'ALTER TABLE Productos ADD COLUMN DESCRIPCION_PRODUCTO TEXT AFTER NOMBRE_PRODUCTO',
    'SELECT "La columna DESCRIPCION_PRODUCTO ya existe en la tabla Productos" as mensaje'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==================================================================
-- VERIFICACIÓN DE ESTRUCTURA FINAL
-- ==================================================================

-- Verificar que todas las tablas principales existen
SELECT 
    TABLE_NAME as 'Tabla_Existente'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('Productos', 'Detalle_Pedidos', 'Pedidos', 'Empleados', 'Clientes', 'Administradores')
ORDER BY TABLE_NAME;

-- Verificar estructura de tabla Productos (debe incluir DESCRIPCION_PRODUCTO)
SELECT 
    COLUMN_NAME as 'Columna',
    DATA_TYPE as 'Tipo',
    IS_NULLABLE as 'Permite_NULL'
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'Productos'
ORDER BY ORDINAL_POSITION;

-- Verificar estructura de tabla Detalle_Pedidos (debe tener solo columnas esenciales)
SELECT 
    COLUMN_NAME as 'Columna',
    DATA_TYPE as 'Tipo',
    IS_NULLABLE as 'Permite_NULL'
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'Detalle_Pedidos'
ORDER BY ORDINAL_POSITION;

-- ==================================================================
-- FIN DE ACTUALIZACIONES DEL SISTEMA
-- ==================================================================

COMMIT;
