-- ==================================================================
-- DDL (DATA DEFINITION LANGUAGE) - DEFINICIÓN DE ESTRUCTURA
-- ==================================================================

DROP DATABASE IF EXISTS ProyectoPanaderia;
SET SQL_SAFE_UPDATES = 0;
CREATE DATABASE ProyectoPanaderia;
USE ProyectoPanaderia;
CREATE TABLE Clientes (
    ID_CLIENTE INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_CLI VARCHAR(100) NOT NULL, 
    TELEFONO_CLI VARCHAR(20),
    ACTIVO_CLI BOOLEAN DEFAULT TRUE,    
    EMAIL_CLI VARCHAR(100),
    CONTRASENA_CLI VARCHAR(255),
    FECHA_ULTIMA_MODIFICACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla: Empleados
CREATE TABLE Empleados (
    ID_EMPLEADO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_EMPLEADO VARCHAR(100) NOT NULL,
    EMAIL_EMPLEADO VARCHAR(100),
    ACTIVO_EMPLEADO BOOLEAN DEFAULT TRUE,
    CONTRASENA_EMPLEADO VARCHAR(255),
    FECHA_REGISTRO TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FECHA_ULTIMA_MODIFICACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla: Administradores
CREATE TABLE Administradores (
    ID_ADMIN INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ADMIN VARCHAR(100) NOT NULL,    
    TELEFONO_ADMIN VARCHAR(20),            
    EMAIL_ADMIN VARCHAR(100),
    CONTRASENA_ADMIN VARCHAR(255)
);

-- Tabla: Proveedores
CREATE TABLE Proveedores (
    ID_PROVEEDOR INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_PROV VARCHAR(100) NOT NULL,    
    TELEFONO_PROV VARCHAR(20),
    ACTIVO_PROV BOOLEAN DEFAULT TRUE,
    EMAIL_PROV VARCHAR(100),
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

-- Tabla: Categoria_Ingredientes
CREATE TABLE Categoria_Ingredientes (
    ID_CATEGORIA INT PRIMARY KEY AUTO_INCREMENT, 
    NOMBRE_CATEGORIA_INGREDIENTE VARCHAR(100) NOT NULL
);

-- Tabla: Ingredientes
CREATE TABLE Ingredientes (
    ID_INGREDIENTE INT PRIMARY KEY AUTO_INCREMENT,
    ID_PROVEEDOR INT,
    ID_CATEGORIA INT,
    NOMBRE_INGREDIENTE VARCHAR(100) NOT NULL,
    CANTIDAD_INGREDIENTE INT, 
    FECHA_VENCIMIENTO DATE,
    REFERENCIA_INGREDIENTE VARCHAR(100),
    FECHA_ENTREGA_INGREDIENTE DATE,
    CONSTRAINT FK_PROVEEDOR_INGREDIENTE
        FOREIGN KEY (ID_PROVEEDOR) REFERENCES Proveedores(ID_PROVEEDOR) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_CATEGORIA_INGREDIENTE
        FOREIGN KEY (ID_CATEGORIA) REFERENCES Categoria_Ingredientes(ID_CATEGORIA) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Productos
CREATE TABLE Productos (
    ID_PRODUCTO INT PRIMARY KEY AUTO_INCREMENT,
    ID_ADMIN INT,
    ID_CATEGORIA_PRODUCTO INT,
    NOMBRE_PRODUCTO VARCHAR(100) NOT NULL,   
    DESCRIPCION_PRODUCTO TEXT,
    PRODUCTO_STOCK_MIN INT,
    PRECIO_PRODUCTO DECIMAL(10,2) NOT NULL,    
    FECHA_VENCIMIENTO_PRODUCTO DATE,
    FECHA_INGRESO_PRODUCTO DATE,
    TIPO_PRODUCTO_MARCA VARCHAR(100),
    ACTIVO BOOLEAN DEFAULT TRUE,
    FECHA_ULTIMA_MODIFICACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT FK_CATEGORIA_PRODUCTO
        FOREIGN KEY (ID_CATEGORIA_PRODUCTO) REFERENCES Categoria_Productos(ID_CATEGORIA_PRODUCTO) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_ADMIN_PRODUCTO
        FOREIGN KEY (ID_ADMIN) REFERENCES Administradores(ID_ADMIN) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Pedidos
CREATE TABLE Pedidos (
    ID_PEDIDO INT PRIMARY KEY AUTO_INCREMENT,
    ID_CLIENTE INT,
    ID_EMPLEADO INT,
    ID_ESTADO_PEDIDO INT,
    FECHA_INGRESO DATETIME,
    FECHA_ENTREGA DATETIME,
    TOTAL_PRODUCTO DECIMAL(10,2),
    CONSTRAINT FK_CLIENTE_PEDIDO
        FOREIGN KEY (ID_CLIENTE) REFERENCES Clientes(ID_CLIENTE) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_EMPLEADO_PEDIDO
        FOREIGN KEY (ID_EMPLEADO) REFERENCES Empleados(ID_EMPLEADO) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_ESTADO_PEDIDO_PEDIDO
        FOREIGN KEY (ID_ESTADO_PEDIDO) REFERENCES Estado_Pedidos(ID_ESTADO_PEDIDO) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Detalle_Pedidos
CREATE TABLE Detalle_Pedidos (
    ID_DETALLE INT PRIMARY KEY AUTO_INCREMENT,
    ID_PEDIDO INT NOT NULL,
    ID_PRODUCTO INT NOT NULL,
    CANTIDAD_PRODUCTO INT,
    PRECIO_UNITARIO DECIMAL(10,2),
    SUBTOTAL DECIMAL(10,2),
    CONSTRAINT FK_DETALLE_PEDIDO
        FOREIGN KEY (ID_PEDIDO) REFERENCES Pedidos(ID_PEDIDO) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_DETALLE_PRODUCTO
        FOREIGN KEY (ID_PRODUCTO) REFERENCES Productos(ID_PRODUCTO) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Ordenes_Salida
CREATE TABLE Ordenes_Salida (
    ID_FACTURA INT PRIMARY KEY AUTO_INCREMENT,
    ID_CLIENTE INT,
    ID_PEDIDO INT,
    FECHA_FACTURACION DATETIME,
    TOTAL_FACTURA DECIMAL(10,2),
    CONSTRAINT FK_ORDENSALIDA_CLIENTE
        FOREIGN KEY (ID_CLIENTE) REFERENCES Clientes(ID_CLIENTE) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FK_ORDENSALIDA_PEDIDO
        FOREIGN KEY (ID_PEDIDO) REFERENCES Pedidos(ID_PEDIDO) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Función: HashPassword (simplificada sin salt)
DELIMITER //
CREATE FUNCTION HashPassword(password VARCHAR(255)) 
RETURNS VARCHAR(255)
READS SQL DATA
DETERMINISTIC
BEGIN
    RETURN SHA2(password, 256);
END//
DELIMITER ;

-- Trigger: Actualizar fecha de modificación de productos
DELIMITER //
CREATE TRIGGER tr_actualizar_fecha_producto 
    BEFORE UPDATE ON Productos
    FOR EACH ROW
BEGIN
    SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
END//
DELIMITER ;

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

-- Trigger: Auditoría - Actualizar fecha de modificación de clientes
DELIMITER //
CREATE TRIGGER tr_clientes_fecha_modificacion
    BEFORE UPDATE ON Clientes
    FOR EACH ROW
BEGIN
    SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
END//
DELIMITER ;

-- Trigger: Auditoría - Actualizar fecha de modificación de empleados
DELIMITER //
CREATE TRIGGER tr_empleados_fecha_modificacion
    BEFORE UPDATE ON Empleados
    FOR EACH ROW
BEGIN
    SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
END//
DELIMITER ;

-- Trigger: Validación de email para clientes
DELIMITER //
CREATE TRIGGER tr_clientes_validar_email
    BEFORE INSERT ON Clientes
    FOR EACH ROW
BEGIN
    IF NEW.EMAIL_CLI IS NOT NULL AND NEW.EMAIL_CLI NOT REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email inválido para cliente';
    END IF;
END//
DELIMITER ;

-- Trigger: Validación de email para empleados
DELIMITER //
CREATE TRIGGER tr_empleados_validar_email
    BEFORE INSERT ON Empleados
    FOR EACH ROW
BEGIN
    IF NEW.EMAIL_EMPLEADO IS NOT NULL AND NEW.EMAIL_EMPLEADO NOT REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email inválido para empleado';
    END IF;
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
('Sharyt Zamora'),
('Carlos Mendoza'),
('Sofia Rodriguez'),
('Miguel Torres'),
('Valentina Castro'),
('Diego Herrera'),
('Camila Vargas'),
('Alejandro Morales'),
('Isabella Gutierrez'),
('Sebastian Ramirez'),
('Natalia Delgado');

-- Inserción en tabla: Administradores 
INSERT INTO Administradores (NOMBRE_ADMIN, TELEFONO_ADMIN, EMAIL_ADMIN) VALUES
('Admin Uno', '3005550101', 'admin1@store.com');

-- Inserción en tabla: Proveedores
INSERT INTO Proveedores (ID_PROVEEDOR, NOMBRE_PROV, TELEFONO_PROV, EMAIL_PROV) VALUES
(1, 'Harina Dorada', '3001234567', 'ventas@harinadorada.com'),
(2, 'Dulce Granero', '3007654321', 'pedidos@dulcegranero.com'),
(3, 'El Horno Mágico S.A.S.', '3009876543', 'contacto@hornoimagico.com'),
(4, 'Masa Maestra Distribuciones', '3005555555', 'info@masamaestra.com'),
(5, 'Insumos Panaderos del Sol', '3002222222', 'ventas@insumossol.com'),
(6, 'La Esencia del Pan', '3003333333', 'pedidos@esenciapan.com'),
(7, 'Proveedora Integral del Panadero', '3004444444', 'contacto@provintegral.com'),
(8, 'Alimentos para Hornear Cía. Ltda.', '3006666666', 'ventas@alimentoshornear.com');

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
INSERT INTO Ingredientes (ID_INGREDIENTE, ID_PROVEEDOR, ID_CATEGORIA, NOMBRE_INGREDIENTE, CANTIDAD_INGREDIENTE, FECHA_VENCIMIENTO, REFERENCIA_INGREDIENTE, FECHA_ENTREGA_INGREDIENTE) VALUES
(1, 2, 1, 'Harina de Trigo', 100, '2025-12-20', 'HAR-TRG-05', '2025-07-01'),
(2, 1, 2, 'Leche Entera UHT', 30, '2025-08-01', 'LECH-ENT-1L', '2025-07-01'),
(3, 3, 3, 'Azúcar Blanca', 70, '2026-01-30', 'AZUC-BLN-KG', '2025-07-01'),
(4, 4, 4, 'Mantequilla sin Sal', 25, '2025-09-15', 'MANT-SS-KG', '2025-07-01'),
(5, 5, 9, 'Huevos Grandes', 120, '2025-07-25', 'HUEV-GR-DZ', '2025-07-01'),
(6, 6, 10, 'Chocolate Semi-Amargo (Gotas)', 15, '2026-03-10', 'CHOC-SM-KG', '2025-07-01'),
(7, 7, 8, 'Levadura Fresca', 5, '2025-07-10', 'LEV-FRES-GR', '2025-07-01'),
(8, 1, 3, 'Azúcar Moreno', 10, '2026-02-20', 'AZUC-MRN-KG', '2025-07-01'),
(9, 2, 1, 'Harina Integral', 50, '2025-11-01', 'HAR-INT-02', '2025-07-01'),
(10, 3, 4, 'Aceite Vegetal', 20, '2026-05-01', 'ACET-VEG-LT', '2025-07-01'),
(11, 4, 10, 'Cacao en Polvo', 8, '2026-04-15', 'CACAO-POL-KG', '2025-07-01'),
(12, 5, 6, 'Manzanas Verdes (Kg)', 10, '2025-07-12', 'MANZ-VRD-KG', '2025-07-01'),
(13, 6, 7, 'Nueces Picadas', 5, '2025-10-01', 'NUEZ-PIC-KG', '2025-07-01'),
(14, 7, 5, 'Esencia de Vainilla', 2, '2027-01-01', 'ESEN-VN-LT', '2025-07-01'),
(15, 8, 13, 'Sal Fina', 2, '2028-01-01', 'SAL-FIN-KG', '2025-07-01'),
(16, 1, 2, 'Crema de Leche', 5, '2025-08-05', 'CREM-LECH-LT', '2025-07-01'),
(17, 2, 11, 'Gelatina sin Sabor', 1, '2026-09-01', 'GEL-SS-KG', '2025-07-01'),
(18, 3, 12, 'Colorante Alimentario Rojo', 0.5, '2027-03-01', 'COLR-ROJ-ML', '2025-07-01'),
(19, 4, 15, 'Semillas de Sésamo', 3, '2026-06-01', 'SEM-SES-KG', '2025-07-01'),
(20, 5, 16, 'Dulce de Leche', 10, '2025-11-15', 'DDL-KG', '2025-07-01');

-- Inserción en tabla: Productos 
INSERT INTO Productos (ID_PRODUCTO, ID_ADMIN, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, DESCRIPCION_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, FECHA_INGRESO_PRODUCTO, TIPO_PRODUCTO_MARCA) VALUES
(1, 1, 8, 'Tamales Tolimenses', 'Tradicionales tamales envueltos en hoja de plátano, con masa de maíz y relleno de carne de cerdo y pollo', 10, 3800.00, '2025-09-15', '2025-07-01', 'Propio'),
(2, 1, 4, 'Pan Tajado Integral', 'Pan de molde integral, ideal para desayunos saludables, rico en fibra', 15, 4200.00, '2025-07-02', '2025-07-01', 'Propio'),
(3, 1, 7, 'Yogurt Fresa Litro', 'Yogurt cremoso con trozos de fresa natural, sin conservantes artificiales', 12, 6000.00, '2025-07-30', '2025-07-03', 'Alpina'),
(4, 1, 5, 'Galleta de Tres Ojos', 'Galleta tradicional colombiana con tres círculos de dulce, crujiente y deliciosa', 20, 2500.00, '2025-11-01', '2025-07-01', 'Propio'),
(5, 1, 1, 'Pan Campesino Grande', 'Pan artesanal de corteza dorada y miga suave, ideal para acompañar comidas', 8, 5500.00, '2025-07-08', '2025-07-04', 'Propio'),
(6, 1, 3, 'Torta de Chocolate Pequeña', 'Deliciosa torta de chocolate húmeda con cobertura de chocolate, perfecta para ocasiones especiales', 5, 18000.00, '2025-07-07', '2025-07-04', 'Propio'),
(7, 1, 2, 'Croissant de Almendras', NULL, 18, 3500.00, '2025-07-06', '2025-07-05', 'Propio'),
(8, 1, 1, 'Baguette Clásica', 'Pan francés tradicional con corteza crujiente y miga aireada', 25, 2800.00, '2025-07-06', '2025-07-05', 'Propio'),
(9, 1, 5, 'Bizcochos de Achira', NULL, 15, 4000.00, '2025-12-01', '2025-07-01', 'Propio'),
(10, 1, 6, 'Jugo de Naranja Natural (500ml)', 'Jugo 100% natural exprimido de naranjas frescas, sin azúcar añadido', 10, 4500.00, '2025-07-05', '2025-07-04', 'Postobón'),
(11, 1, 7, 'Postre de Tres Leches', 'Clásico postre colombiano empapado en tres tipos de leche, suave y cremoso', 7, 7500.00, '2025-07-08', '2025-07-04', 'Propio'),
(12, 1, 4, 'Pan Blanco de Molde', NULL, 20, 3900.00, '2025-07-02', '2025-07-01', 'Propio'),
(13, 1, 3, 'Muffin de Arándanos', 'Muffin esponjoso con arándanos frescos, perfecto para el desayuno o merienda', 15, 3000.00, '2025-07-07', '2025-07-04', 'Propio'),
(14, 1, 2, 'Pan de Bono Pequeño', NULL, 30, 1500.00, '2025-07-06', '2025-07-05', 'Propio'),
(15, 1, 8, 'Empanadas de Carne (unidad)', 'Empanada frita rellena de carne molida sazonada con especias tradicionales', 20, 2000.00, '2025-07-06', '2025-07-05', 'Propio'),
(16, 1, 3, 'Brazo de Reina', 'Bizcocho enrollado relleno de dulce de leche y cubierto con coco rallado', 10, 9500.00, '2025-07-09', '2025-07-04', 'Propio'),
(17, 1, 1, 'Pan Trenza Integral', NULL, 12, 4800.00, '2025-07-07', '2025-07-03', 'Propio'),
(18, 1, 5, 'Galletas Surtidas de Mantequilla', 'Variedad de galletas caseras de mantequilla con diferentes formas y sabores', 25, 3200.00, '2025-12-30', '2025-07-01', 'Propio'),
(19, 1, 7, 'Avena La Lechera (500ml)', NULL, 18, 5800.00, '2025-08-20', '2025-07-03', 'Nestlé'),
(20, 1, 9, 'Ponqué de Naranja (Porción)', 'Porción individual de ponqué de naranja con glaseado cítrico', 15, 3000.00, '2025-07-08', '2025-07-04', 'Propio'),
(21, 1, 10, 'Pan Artesanal de Masa Madre', 'Pan elaborado con masa madre natural, fermentación larga para mejor digestibilidad', 7, 8000.00, '2025-07-07', '2025-07-05', 'Propio'),
(22, 1, 3, 'Cheesecake de Frutos Rojos', 'Cheesecake cremoso con base de galleta y cobertura de frutos rojos frescos', 6, 25000.00, '2025-07-09', '2025-07-04', 'Propio'),
(23, 1, 4, 'Pan de Hamburguesa', NULL, 30, 4500.00, '2025-07-10', '2025-07-02', 'Propio'),
(24, 1, 5, 'Galletas de Avena y Pasas', 'Galletas nutritivas con avena integral y pasas, sin azúcar refinado', 22, 2700.00, '2026-01-01', '2025-07-01', 'Propio'),
(25, 1, 7, 'Kumiss Natural', NULL, 10, 4900.00, '2025-07-25', '2025-07-03', 'Alquería'),
(26, 1, 9, 'Brownie con Nuez', 'Brownie de chocolate intenso con trozos de nuez, húmedo y delicioso', 40, 1800.00, '2025-07-08', '2025-07-05', 'Propio'),
(27, 1, 1, 'Pan Blandito', NULL, 28, 2500.00, '2025-07-07', '2025-07-05', 'Propio'),
(28, 1, 3, 'Milhoja de Arequipe', 'Delicada milhoja rellena de arequipe casero y cubierta con azúcar glass', 12, 6000.00, '2025-07-08', '2025-07-04', 'Propio'),
(29, 1, 2, 'Mogolla Chicharrona', NULL, 15, 3500.00, '2025-07-06', '2025-07-05', 'Propio'),
(30, 1, 8, 'Arequipe (Tarro 500g)', 'Arequipe casero cremoso y dulce, perfecto para postres y acompañamientos', 8, 9000.00, '2026-04-10', '2025-07-01', 'Propio');

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
-- DCL (DATA CONTROL LANGUAGE) - SEGURIDAD Y ENCRIPTACIÓN
-- ==================================================================

-- Actualizar contraseñas de administradores con encriptación (sin salt)
UPDATE Administradores 
SET CONTRASENA_ADMIN = HashPassword('admin123')
WHERE ID_ADMIN = 1;

-- Actualizar contraseñas de clientes con encriptación (sin salt)
UPDATE Clientes 
SET CONTRASENA_CLI = HashPassword('cliente123')
WHERE ID_CLIENTE = 1;

UPDATE Clientes 
SET CONTRASENA_CLI = HashPassword('cliente456')
WHERE ID_CLIENTE = 2;

UPDATE Clientes 
SET CONTRASENA_CLI = HashPassword('cliente789')
WHERE ID_CLIENTE = 3;

-- Actualizar contraseñas de empleados con encriptación (sin salt)
UPDATE Empleados 
SET CONTRASENA_EMPLEADO = HashPassword('empleado123')
WHERE ID_EMPLEADO = 1;

UPDATE Empleados 
SET CONTRASENA_EMPLEADO = HashPassword('empleado456')
WHERE ID_EMPLEADO = 2;

COMMIT;