-- ==================================================================
-- DDL (DATA DEFINITION LANGUAGE) - DEFINICION DE ESTRUCTURA
-- ==================================================================

-- Elimina la base de datos existente para asegurar una ejecución limpia
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
    CANTIDAD_INGREDIENTE DECIMAL(10,4), -- Corregido para manejar cantidades de receta
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
    STOCK_ACTUAL INT DEFAULT 0, -- Columna añadida para el stock real
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


-- ==================================================================
-- NUEVAS TABLAS PARA EL ESQUEMA DE RECETAS NORMALIZADO Y ALTER TABLE PRODUCTOR
-- ==================================================================

-- SENTENCIA ALTER TABLE SOLICITADA COMENTADA
-- Si la tabla Productos ya existía y necesitaba añadir el campo STOCK_ACTUAL:
-- ALTER TABLE Productos ADD COLUMN STOCK_ACTUAL INT DEFAULT 0;


-- Tabla NUEVA: UNIDADES_MEDIDA (Catalogo de unidades)
CREATE TABLE UNIDADES_MEDIDA (
    ID_UNIDAD INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_UNIDAD VARCHAR(50) NOT NULL,
    ABREVIATURA_UNIDAD VARCHAR(10) NOT NULL
);
-- NUEVA TABLA EJECUTAR SENTENCIA
-- Tabla NUEVA: RECETAS (Maestra - Define la receta por producto)
CREATE TABLE RECETAS (
    ID_RECETA INT PRIMARY KEY AUTO_INCREMENT,
    ID_PRODUCTO INT NOT NULL,
    NOMBRE_RECETA VARCHAR(255) NOT NULL,
    CONSTRAINT UK_PRODUCTO_RECETA UNIQUE (ID_PRODUCTO),
    CONSTRAINT FK_RECETA_PRODUCTO_MAESTRA
        FOREIGN KEY (ID_PRODUCTO) REFERENCES Productos (ID_PRODUCTO) ON DELETE CASCADE ON UPDATE CASCADE
);
-- NUEVA TABLA EJECUTAR SENTENCIA
-- Tabla NUEVA: RECETAS_DETALLE (Detalle - Ingredientes y cantidades)
CREATE TABLE RECETAS_DETALLE (
    ID_DETALLE INT PRIMARY KEY AUTO_INCREMENT,
    ID_RECETA INT NOT NULL,
    ID_INGREDIENTE INT NOT NULL,
    CANTIDAD_REQUERIDA DECIMAL(10,4) NOT NULL, 
    ID_UNIDAD INT NOT NULL,

    CONSTRAINT UK_INGREDIENTE_EN_RECETA UNIQUE (ID_RECETA, ID_INGREDIENTE),
    CONSTRAINT FK_RECETA_DETALLE
        FOREIGN KEY (ID_RECETA) REFERENCES RECETAS (ID_RECETA) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FK_INGREDIENTE_DETALLE
        FOREIGN KEY (ID_INGREDIENTE) REFERENCES Ingredientes (ID_INGREDIENTE) ON UPDATE CASCADE,
    CONSTRAINT FK_UNIDAD_DETALLE
        FOREIGN KEY (ID_UNIDAD) REFERENCES UNIDADES_MEDIDA (ID_UNIDAD) ON UPDATE CASCADE ON DELETE RESTRICT
);
-- NUEVA TABLA EJECUTAR SENTENCIA
-- Vista NUEVA: VIEW_RECETAS_COMPLETA (Facilita la consulta de toda la receta)
CREATE OR REPLACE VIEW VIEW_RECETAS_COMPLETA AS
SELECT
    P.ID_PRODUCTO,
    P.NOMBRE_PRODUCTO,
    R.ID_RECETA,
    R.NOMBRE_RECETA,
    I.ID_INGREDIENTE,
    I.NOMBRE_INGREDIENTE,
    RD.CANTIDAD_REQUERIDA,
    UM.ABREVIATURA_UNIDAD AS UNIDAD_MEDIDA,
    UM.NOMBRE_UNIDAD AS NOMBRE_UNIDAD_COMPLETA
FROM
    PRODUCTOS P
INNER JOIN RECETAS R 
    ON P.ID_PRODUCTO = R.ID_PRODUCTO
INNER JOIN RECETAS_DETALLE RD 
    ON R.ID_RECETA = RD.ID_RECETA
INNER JOIN INGREDIENTES I 
    ON RD.ID_INGREDIENTE = I.ID_INGREDIENTE
INNER JOIN UNIDADES_MEDIDA UM 
    ON RD.ID_UNIDAD = UM.ID_UNIDAD
ORDER BY
    P.ID_PRODUCTO, RD.ID_DETALLE;

-- ==================================================================
-- TABLAS RESTANTES
-- ==================================================================

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
-- DML (DATA MANIPULATION LANGUAGE) - INSERCION DE DATOS
-- ==================================================================
START TRANSACTION;

-- Insercion en tablas base (sin dependencias fuertes)
INSERT INTO Clientes (NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI, CONTRASENA_CLI) VALUES
('Ana Perez', '3101234567', 'ana.p@mail.com', HashPassword('cliente123')), 
('Luis Gomez', '3209876543', 'luis.g@mail.com', HashPassword('cliente456')),
('Maria Rodriguez', '3001122334', 'maria.r@mail.com', HashPassword('cliente789')),
('Damian Cliente', '3001234567', 'damian@cliente.com', HashPassword('123456'));

INSERT INTO Empleados (NOMBRE_EMPLEADO, EMAIL_EMPLEADO, CONTRASENA_EMPLEADO) VALUES
('Andres Alkaeda', 'andres@panaderia.com', HashPassword('empleado123')),
('Damian Avila', 'damian@panaderia.com', HashPassword('empleado123')),
('Brayan Jimenez', 'brayan@panaderia.com', HashPassword('empleado123')),
('Ana Goyeneche', 'ana@panaderia.com', HashPassword('empleado123')),
('Sharyt Zamora', 'sharyt@panaderia.com', HashPassword('empleado123')),
('Carlos Mendoza', 'carlos@panaderia.com', HashPassword('empleado123')),
('Sofia Rodriguez', 'sofia@panaderia.com', HashPassword('empleado123')),
('Miguel Torres', 'miguel@panaderia.com', HashPassword('empleado123')),
('Valentina Castro', 'valentina@panaderia.com', HashPassword('empleado123')),
('Diego Herrera', 'diego@panaderia.com', HashPassword('empleado123')),
('Camila Vargas', 'camila@panaderia.com', HashPassword('empleado123')),
('Alejandro Morales', 'alejandro@panaderia.com', HashPassword('empleado123')),
('Isabella Gutierrez', 'isabella@panaderia.com', HashPassword('empleado123')),
('Sebastian Ramirez', 'sebastian@panaderia.com', HashPassword('empleado123')),
('Natalia Delgado', 'natalia@panaderia.com', HashPassword('empleado123'));

INSERT INTO Administradores (NOMBRE_ADMIN, TELEFONO_ADMIN, EMAIL_ADMIN, CONTRASENA_ADMIN) VALUES
('Admin Principal', '3005550101', 'admin@panaderia.com', HashPassword('admin123')),
('Admin Secundario', '3005550102', 'admin2@panaderia.com', HashPassword('admin123'));

-- Proveedores (TODOS 8 para satisfacer FK de Ingredientes)
INSERT INTO Proveedores (ID_PROVEEDOR, NOMBRE_PROV, TELEFONO_PROV, EMAIL_PROV) VALUES
(1,'Harina Dorada','3001234567','ventas@harinadorada.com'),
(2,'Dulce Granero','3007654321','pedidos@dulcegranero.com'),
(3,'El Horno Magico S.A.S.','3009876543','contacto@hornoimagico.com'),
(4,'Masa Maestra Distribuciones','3005555555','info@masamaestra.com');

INSERT INTO Proveedores (ID_PROVEEDOR, NOMBRE_PROV) VALUES 
(5, 'Insumos Panaderos del Sol'), (6, 'La Esencia del Pan'), 
(7, 'Proveedora Integral del Panadero'), (8, 'Alimentos para Hornear Cia. Ltda.');


-- Categorias de Productos
INSERT INTO Categoria_Productos (NOMBRE_CATEGORIAPRODUCTO) VALUES
('Tortas Tres Leches'), ('Tortas Milyway'), ('Tortas por Encargo'),
('Pan Grande'), ('Pan Pequeno'), ('Postres'), ('Galletas'), 
('Tamales'), ('Yogures'), ('Pasteles Pollo');

-- Categorias de Ingredientes (TODAS 16 para satisfacer FK de Ingredientes)
INSERT INTO Categoria_Ingredientes (ID_CATEGORIA, NOMBRE_CATEGORIA_INGREDIENTE) VALUES
(1, 'Harinas y Cereales'), (2, 'Lacteos y Derivados'), 
(3, 'Endulzantes y Azucares'), (4, 'Grasas y Aceites'),
(5, 'Esencias y Saborizantes'), (6, 'Frutas y Verduras'),
(7, 'Frutos Secos'), (8, 'Levaduras'), (9, 'Huevos'), (10, 'Chocolate y Cacao'), 
(11, 'Espesantes y Gelificantes'), (12, 'Colorantes Alimentarios'), (13, 'Sal'), 
(14, 'Aditivos y Mejoradores'), (15, 'Semillas'), (16, 'Coberturas y Rellenos');

-- Datos NUEVOS: UNIDADES_MEDIDA
INSERT INTO UNIDADES_MEDIDA (ID_UNIDAD, NOMBRE_UNIDAD, ABREVIATURA_UNIDAD) VALUES
(1, 'Kilogramo', 'kg'), (2, 'Gramo', 'gr'), (3, 'Litro', 'Lts'), 
(4, 'Mililitro', 'ml'), (5, 'Unidad', 'un'), (6, 'Cucharada', 'cda');

-- Productos (Todos los IDs del 1 al 28 para satisfacer FK de Recetas)
INSERT INTO Productos (ID_PRODUCTO, ID_ADMIN, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, DESCRIPCION_PRODUCTO, PRODUCTO_STOCK_MIN, STOCK_ACTUAL, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, FECHA_INGRESO_PRODUCTO, TIPO_PRODUCTO_MARCA) VALUES
(1,1,1,'Postre de Tres Leches','Clasico postre colombiano',7,10,7500.00,'2025-07-08','2025-07-04','Propio'),
(2,1,2,'Torta de Chocolate Pequena','Deliciosa torta de chocolate',5,10,18000.00,'2025-07-07','2025-07-04','Propio'),
(3,1,2,'Cheesecake de Frutos Rojos','Cheesecake cremoso',6,10,25000.00,'2025-07-09','2025-07-04','Propio'),
(4,1,2,'Milhoja de Arequipe','Milhoja rellena de arequipe',12,10,6000.00,'2025-07-08','2025-07-04','Propio'),
(5,1,3,'Brazo de Reina','Bizcocho enrollado con dulce de leche',10,10,9500.00,'2025-07-09','2025-07-04','Propio'),
(6,1,3,'Ponque de Naranja (Porcion)','Porcion individual con glaseado citrico',15,10,3000.00,'2025-07-08','2025-07-04','Propio'),
(7,1,4,'Pan Campesino Grande','Pan artesanal',8,10,5500.00,'2025-07-08','2025-07-04','Propio'),
(8,1,4,'Baguette Clasica','Pan frances tradicional',25,10,2800.00,'2025-07-06','2025-07-05','Propio'),
(9,1,4,'Pan Artesanal de Masa Madre','Fermentacion larga',7,10,8000.00,'2025-07-07','2025-07-05','Propio'),
(10,1,4,'Mogolla Chicharrona',NULL,15,10,3500.00,'2025-07-06','2025-07-05','Propio'),
(11,1,5,'Pan Tajado Integral','Pan de molde integral',15,10,4200.00,'2025-07-02','2025-07-01','Propio'),
(12,1,5,'Pan Blanco de Molde',NULL,20,10,3900.00,'2025-07-02','2025-07-01','Propio'),
(13,1,5,'Pan de Bono Pequeno',NULL,30,10,1500.00,'2025-07-06','2025-07-05','Propio'),
(14,1,5,'Croissant de Almendras',NULL,18,10,3500.00,'2025-07-06','2025-07-05','Propio'),
(15,1,5,'Pan Blandito',NULL,28,10,2500.00,'2025-07-07','2025-07-05','Propio'),
(16,1,5,'Pan de Hamburguesa',NULL,30,10,4500.00,'2025-07-10','2025-07-02','Propio'),
(17,1,6,'Jugo de Naranja Natural','Jugo 100% natural',10,10,4500.00,'2025-07-05','2025-07-04','Postobon'),
(18,1,6,'Avena La Lechera (500ml)',NULL,18,10,5800.00,'2025-08-20','2025-07-03','Nestle'),
(19,1,6,'Brownie con Nuez','Brownie humedo y delicioso',40,10,1800.00,'2025-07-08','2025-07-05','Propio'),
(20,1,6,'Muffin de Arandanos','Muffin esponjoso',15,10,3000.00,'2025-07-07','2025-07-04','Propio'),
(21,1,7,'Galleta de Tres Ojos','Galleta tradicional',20,10,2500.00,'2025-11-01','2025-07-01','Propio'),
(22,1,7,'Bizcochos de Achira',NULL,15,10,4000.00,'2025-12-01','2025-07-01','Propio'),
(23,1,7,'Galletas Surtidas de Mantequilla','Variedad de galletas caseras',25,10,3200.00,'2025-12-30','2025-07-01','Propio'),
(24,1,7,'Galletas de Avena y Pasas','Con avena integral y pasas',22,10,2700.00,'2026-01-01','2025-07-01','Propio'), 
(25,1,8,'Tamales Tolimenses','Tradicionales tamales',10,10,3800.00,'2025-09-15','2025-07-01','Propio'),
(26,1,8,'Empanadas de Carne (unidad)','Empanada frita con carne',20,10,2000.00,'2025-07-06','2025-07-05','Propio'),
(27,1,9,'Yogurt Fresa Litro','Yogurt cremoso con trozos de fresa',12,10,6000.00,'2025-07-30','2025-07-03','Alpina'),
(28,1,9,'Kumiss Natural',NULL,10,10,4900.00,'2025-07-25','2025-07-03','Alqueria');

-- Insercion de Ingredientes BASE (IDs explícitos 1-20)
INSERT INTO Ingredientes (ID_INGREDIENTE, ID_PROVEEDOR, ID_CATEGORIA, NOMBRE_INGREDIENTE, CANTIDAD_INGREDIENTE, FECHA_VENCIMIENTO, REFERENCIA_INGREDIENTE, FECHA_ENTREGA_INGREDIENTE) VALUES
(1, 2, 1, 'Harina de Trigo', 100.0000, '2025-12-20', 'HAR-TRG-05', '2025-07-01'),
(2, 1, 2, 'Leche Entera UHT', 30.0000, '2025-08-01', 'LECH-ENT-1L', '2025-07-01'),
(3, 3, 3, 'Azucar Blanca', 70.0000, '2026-01-30', 'AZUC-BLN-KG', '2025-07-01'),
(4, 4, 4, 'Mantequilla sin Sal', 25.0000, '2025-09-15', 'MANT-SS-KG', '2025-07-01'),
(5, 5, 9, 'Huevos Grandes', 120.0000, '2025-07-25', 'HUEV-GR-DZ', '2025-07-01'),
(6, 6, 10, 'Chocolate Semi-Amargo (Gotas)', 15.0000, '2026-03-10', 'CHOC-SM-KG', '2025-07-01'),
(7, 7, 8, 'Levadura Fresca', 5.0000, '2025-07-10', 'LEV-FRES-GR', '2025-07-01'),
(8, 1, 3, 'Azucar Moreno', 10.0000, '2026-02-20', 'AZUC-MRN-KG', '2025-07-01'),
(9, 2, 1, 'Harina Integral', 50.0000, '2025-11-01', 'HAR-INT-02', '2025-07-01'),
(10, 3, 4, 'Aceite Vegetal', 20.0000, '2026-05-01', 'ACET-VEG-LT', '2025-07-01'),
(11, 4, 10, 'Cacao en Polvo', 8.0000, '2026-04-15', 'CACAO-POL-KG', '2025-07-01'),
(12, 5, 6, 'Manzanas Verdes (Kg)', 10.0000, '2025-07-12', 'MANZ-VRD-KG', '2025-07-01'),
(13, 6, 7, 'Nueces Picadas', 5.0000, '2025-10-01', 'NUEZ-PIC-KG', '2025-07-01'),
(14, 7, 5, 'Esencia de Vainilla', 2.0000, '2027-01-01', 'ESEN-VN-LT', '2025-07-01'),
(15, 8, 13, 'Sal Fina', 2.0000, '2028-01-01', 'SAL-FIN-KG', '2025-07-01'),
(16, 1, 2, 'Crema de Leche', 5.0000, '2025-08-05', 'CREM-LECH-LT', '2025-07-01'),
(17, 2, 11, 'Gelatina sin Sabor', 1.0000, '2026-09-01', 'GEL-SS-KG', '2025-07-01'),
(18, 3, 12, 'Colorante Alimentario Rojo', 0.5000, '2027-03-01', 'COLR-ROJ-ML', '2025-07-01'),
(19, 4, 15, 'Semillas de Sesamo', 3.0000, '2026-06-01', 'SEM-SES-KG', '2025-07-01'),
(20, 5, 16, 'Dulce de Leche', 10.0000, '2025-11-15', 'DDL-KG', '2025-07-01');
-- CREACION
-- Insercion del Ingrediente NUEVO
INSERT INTO Ingredientes (ID_PROVEEDOR, ID_CATEGORIA, NOMBRE_INGREDIENTE, CANTIDAD_INGREDIENTE, FECHA_VENCIMIENTO) 
VALUES (1, 1, 'Avena en Hojuelas', 50.0000, '2026-10-01');
SET @ID_AVENA = LAST_INSERT_ID(); 

-- ==================================================================
-- RECETA PARA PRODUCTO ID 24 (Galletas de Avena y Pasas)
-- ==================================================================

-- 1. Insertar la Receta Maestra (Header)
INSERT INTO RECETAS (ID_PRODUCTO, NOMBRE_RECETA) 
VALUES (24, 'Receta Clasica Avena y Pasas');
SET @ID_RECETA_GALLETAS = LAST_INSERT_ID();

-- 2. Insertar los Detalles (Ingredientes)
-- ID_UNIDAD: 2=gr, 5=un, 6=cda
INSERT INTO RECETAS_DETALLE (ID_RECETA, ID_INGREDIENTE, CANTIDAD_REQUERIDA, ID_UNIDAD) VALUES
(@ID_RECETA_GALLETAS, 3, 200.0000, 2),        -- Azucar Blanca (ID 3, 200 gr)
(@ID_RECETA_GALLETAS, 8, 150.0000, 2),        -- Azucar Moreno (ID 8, 150 gr)
(@ID_RECETA_GALLETAS, 4, 250.0000, 2),        -- Mantequilla sin Sal (ID 4, 250 gr)
(@ID_RECETA_GALLETAS, 15, 1.0000, 6),         -- Sal Fina (ID 15, 1 Cucharada)
(@ID_RECETA_GALLETAS, 14, 1.0000, 6),         -- Esencia de Vainilla (ID 14, 1 Cucharada)
(@ID_RECETA_GALLETAS, 5, 2.0000, 5),          -- Huevos Grandes (ID 5, 2 Unidades)
(@ID_RECETA_GALLETAS, @ID_AVENA, 300.0000, 2), -- Avena en Hojuelas (Nuevo ID, 300 gr)
(@ID_RECETA_GALLETAS, 1, 120.0000, 2),        -- Harina de Trigo (ID 1, 120 gr)
(@ID_RECETA_GALLETAS, 13, 150.0000, 2);       -- Nueces Picadas/Pasas (ID 13, 150 gr)

COMMIT;

-- ==================================================================
-- CONSULTA DE EJEMPLO
-- ==================================================================

SELECT 
    ID_PRODUCTO,
    NOMBRE_PRODUCTO AS Producto,
    NOMBRE_RECETA AS Receta,
    NOMBRE_INGREDIENTE AS Ingrediente,
    CANTIDAD_REQUERIDA AS Cantidad,
    UNIDAD_MEDIDA AS Unidad
FROM 
    VIEW_RECETAS_COMPLETA 
WHERE 
    ID_PRODUCTO = 24;