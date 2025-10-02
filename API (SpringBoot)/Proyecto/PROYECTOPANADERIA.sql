-- ==================================================================
-- DDL (DATA DEFINITION LANGUAGE) - DEFINICIÓN DE ESTRUCTURA
-- ==================================================================

DROP DATABASE IF EXISTS ProyectoPanaderia;
SET SQL_SAFE_UPDATES = 0;
CREATE DATABASE ProyectoPanaderia;
USE ProyectoPanaderia;

-- Tabla: Clientes
CREATE TABLE Clientes (
    ID_CLIENTE INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_CLI VARCHAR(100) NOT NULL, 
    TELEFONO_CLI VARCHAR(20),
    ACTIVO_CLI BOOLEAN DEFAULT TRUE,    
    EMAIL_CLI VARCHAR(100),
    CONTRASEÑA_CLI VARCHAR(255),
    FECHA_ULTIMA_MODIFICACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla: Empleados
CREATE TABLE Empleados (
    ID_EMPLEADO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_EMPLEADO VARCHAR(100) NOT NULL,
    EMAIL_EMPLEADO VARCHAR(100),
    ACTIVO_EMPLEADO BOOLEAN DEFAULT TRUE,
    CONTRASEÑA_EMPLEADO VARCHAR(255),
    FECHA_REGISTRO TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FECHA_ULTIMA_MODIFICACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla: Administradores
CREATE TABLE Administradores (
    ID_ADMIN INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ADMIN VARCHAR(100) NOT NULL,    
    TELEFONO_ADMIN VARCHAR(20),            
    EMAIL_ADMIN VARCHAR(100),
    CONTRASEÑA_ADMIN VARCHAR(255)
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

-- Tabla: Pedidos_Proveedores
CREATE TABLE Pedidos_Proveedores (
    ID_PEDIDO_PROV INT PRIMARY KEY AUTO_INCREMENT,
    ID_PROVEEDOR INT NOT NULL,
    NUMERO_PEDIDO INT NOT NULL,
    FECHA_PEDIDO DATE NOT NULL,
    ESTADO_PEDIDO VARCHAR(50) DEFAULT 'Pendiente',
    CONSTRAINT FK_PEDIDO_PROVEEDOR
        FOREIGN KEY (ID_PROVEEDOR) REFERENCES Proveedores(ID_PROVEEDOR) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabla: Categoria_Productos
CREATE TABLE Categoria_Productos (
    ID_CATEGORIA_PRODUCTO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_CATEGORIAPRODUCTO VARCHAR(100) NOT NULL
);

-- Tabla: Estado_Pedidos
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

-- Función: HashPassword
DELIMITER //
CREATE FUNCTION HashPassword(password VARCHAR(255)) 
RETURNS VARCHAR(255)
READS SQL DATA
DETERMINISTIC
BEGIN
    RETURN SHA2(password, 256);
END//
DELIMITER ;

-- ==================================================================
-- DML (DATA MANIPULATION LANGUAGE) - INSERCIÓN DE DATOS
-- ==================================================================

-- Clientes
INSERT INTO Clientes (NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI) VALUES
('Ana Pérez', '3101234567', 'ana.p@mail.com'),
('Luis Gómez', '3209876543', 'luis.g@mail.com'),
('Maria Rodriguez', '3001122334', 'maria.r@mail.com');

-- Empleados
INSERT INTO Empleados (NOMBRE_EMPLEADO) VALUES
('Andres Alkaeda'),('Damian Avila'),('Brayan Jimenez'),('Ana Goyeneche'),
('Sharyt Zamora'),('Carlos Mendoza'),('Sofia Rodriguez'),('Miguel Torres'),
('Valentina Castro'),('Diego Herrera'),('Camila Vargas'),('Alejandro Morales'),
('Isabella Gutierrez'),('Sebastian Ramirez'),('Natalia Delgado');

-- Administradores
INSERT INTO Administradores (NOMBRE_ADMIN, TELEFONO_ADMIN, EMAIL_ADMIN) VALUES
('Admin Uno', '3005550101', 'admin1@store.com');

-- Proveedores
INSERT INTO Proveedores (ID_PROVEEDOR, NOMBRE_PROV, TELEFONO_PROV, EMAIL_PROV) VALUES
(1,'Harina Dorada','3001234567','ventas@harinadorada.com'),
(2,'Dulce Granero','3007654321','pedidos@dulcegranero.com'),
(3,'El Horno Mágico S.A.S.','3009876543','contacto@hornoimagico.com'),
(4,'Masa Maestra Distribuciones','3005555555','info@masamaestra.com');

-- Categorías de productos
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

-- Productos (con categorías corregidas)
INSERT INTO Productos (ID_ADMIN, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, DESCRIPCION_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, FECHA_INGRESO_PRODUCTO, TIPO_PRODUCTO_MARCA) VALUES
-- Tortas Tres Leches (1)
(1,1,'Postre de Tres Leches','Clásico postre colombiano',7,7500.00,'2025-07-08','2025-07-04','Propio'),

-- Tortas Milyway (2)
(1,2,'Torta de Chocolate Pequeña','Deliciosa torta de chocolate',5,18000.00,'2025-07-07','2025-07-04','Propio'),
(1,2,'Cheesecake de Frutos Rojos','Cheesecake cremoso',6,25000.00,'2025-07-09','2025-07-04','Propio'),
(1,2,'Milhoja de Arequipe','Milhoja rellena de arequipe',12,6000.00,'2025-07-08','2025-07-04','Propio'),

-- Tortas por Encargo (3)
(1,3,'Brazo de Reina','Bizcocho enrollado con dulce de leche',10,9500.00,'2025-07-09','2025-07-04','Propio'),
(1,3,'Ponqué de Naranja (Porción)','Porción individual con glaseado cítrico',15,3000.00,'2025-07-08','2025-07-04','Propio'),

-- Pan Grande (4)
(1,4,'Pan Campesino Grande','Pan artesanal',8,5500.00,'2025-07-08','2025-07-04','Propio'),
(1,4,'Baguette Clásica','Pan francés tradicional',25,2800.00,'2025-07-06','2025-07-05','Propio'),
(1,4,'Pan Artesanal de Masa Madre','Fermentación larga',7,8000.00,'2025-07-07','2025-07-05','Propio'),
(1,4,'Mogolla Chicharrona',NULL,15,3500.00,'2025-07-06','2025-07-05','Propio'),

-- Pan Pequeño (5)
(1,5,'Pan Tajado Integral','Pan de molde integral',15,4200.00,'2025-07-02','2025-07-01','Propio'),
(1,5,'Pan Blanco de Molde',NULL,20,3900.00,'2025-07-02','2025-07-01','Propio'),
(1,5,'Pan de Bono Pequeño',NULL,30,1500.00,'2025-07-06','2025-07-05','Propio'),
(1,5,'Croissant de Almendras',NULL,18,3500.00,'2025-07-06','2025-07-05','Propio'),
(1,5,'Pan Blandito',NULL,28,2500.00,'2025-07-07','2025-07-05','Propio'),
(1,5,'Pan de Hamburguesa',NULL,30,4500.00,'2025-07-10','2025-07-02','Propio'),

-- Postres (6)
(1,6,'Jugo de Naranja Natural','Jugo 100% natural',10,4500.00,'2025-07-05','2025-07-04','Postobón'),
(1,6,'Avena La Lechera (500ml)',NULL,18,5800.00,'2025-08-20','2025-07-03','Nestlé'),
(1,6,'Brownie con Nuez','Brownie húmedo y delicioso',40,1800.00,'2025-07-08','2025-07-05','Propio'),
(1,6,'Muffin de Arándanos','Muffin esponjoso',15,3000.00,'2025-07-07','2025-07-04','Propio'),

-- Galletas (7)
(1,7,'Galleta de Tres Ojos','Galleta tradicional',20,2500.00,'2025-11-01','2025-07-01','Propio'),
(1,7,'Bizcochos de Achira',NULL,15,4000.00,'2025-12-01','2025-07-01','Propio'),
(1,7,'Galletas Surtidas de Mantequilla','Variedad de galletas caseras',25,3200.00,'2025-12-30','2025-07-01','Propio'),
(1,7,'Galletas de Avena y Pasas','Con avena integral y pasas',22,2700.00,'2026-01-01','2025-07-01','Propio'),

-- Tamales (8)
(1,8,'Tamales Tolimenses','Tradicionales tamales',10,3800.00,'2025-09-15','2025-07-01','Propio'),
(1,8,'Empanadas de Carne (unidad)','Empanada frita con carne',20,2000.00,'2025-07-06','2025-07-05','Propio'),

-- Yogures (9)
(1,9,'Yogurt Fresa Litro','Yogurt cremoso con trozos de fresa',12,6000.00,'2025-07-30','2025-07-03','Alpina'),
(1,9,'Kumiss Natural',NULL,10,4900.00,'2025-07-25','2025-07-03','Alquería');
