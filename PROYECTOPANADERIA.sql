DROP DATABASE IF EXISTS ProyectoPanaderia;
CREATE DATABASE ProyectoPanaderia;
USE ProyectoPanaderia;

-- 1. CLIENTES
CREATE TABLE Clientes (
    ID_CLIENTE INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_CLI VARCHAR(100) NOT NULL, 
    TELEFONO_CLI VARCHAR(20),       
    EMAIL_CLI VARCHAR(100)           
);

-- 2. EMPLEADOS
CREATE TABLE Empleados (
    ID_EMPLEADO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_EMPLEADO VARCHAR(100) NOT NULL
);

-- 3. ADMINISTRADORES 
CREATE TABLE Administradores (
    ID_ADMIN INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ADMIN VARCHAR(100) NOT NULL,    
    TELEFONO_ADMIN VARCHAR(20),            
    EMAIL_ADMIN VARCHAR(100),             
    CONTRASEÑA_ADMIN VARCHAR(255) NOT NULL 
);

-- 4. PROVEEDORES (Corrigiendo tipos de datos)
CREATE TABLE Proveedores (
    ID_PROVEEDOR INT PRIMARY KEY AUTO_INCREMENT,
    NUMERO_PEDIDO INT NOT NULL,          
    NOMBRE_PROV VARCHAR(100) NOT NULL,    
    TELEFONO_PROV VARCHAR(20) NOT NULL,    
    EMAIL_PROV VARCHAR(100) NOT NULL,
    FECHA_PEDIDO DATE NOT NULL
);

-- 5. CATEGORÍA DE PRODUCTOS
CREATE TABLE Categoria_Productos (
    ID_CATEGORIA_PRODUCTO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_CATEGORIAPRODUCTO VARCHAR(100) NOT NULL
);

-- 6. ESTADO DE PEDIDOS
CREATE TABLE Estado_Pedidos (
    ID_ESTADO_PEDIDO INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE_ESTADO VARCHAR(50)
);

-- 7. CATEGORÍA DE INGREDIENTES
CREATE TABLE Categoria_Ingredientes (
    ID_CATEGORIA INT PRIMARY KEY AUTO_INCREMENT, 
    NOMBRE_CATEGORIA_INGREDIENTE VARCHAR(100) NOT NULL
);

-- 8. INGREDIENTES
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
        FOREIGN KEY (ID_CATEGORIA) REFERENCES Categoria_Ingredientes(ID_CATEGORIA) ON UPDATE CASCADE
);

-- 9. PRODUCTOS
CREATE TABLE Productos (
    ID_PRODUCTO INT PRIMARY KEY AUTO_INCREMENT,
    ID_ADMIN INT,
    ID_CATEGORIA_PRODUCTO INT,
    ID_PROVEEDOR INT,
    NOMBRE_PRODUCTO VARCHAR(100) NOT NULL,   
    PRODUCTO_STOCK_MIN INT NOT NULL,
    PRECIO_PRODUCTO DECIMAL(10,2) NOT NULL,    
    FECHA_VENCIMIENTO_PRODUCTO DATE NOT NULL,
    FECHA_INGRESO_PRODUCTO DATE NOT NULL,
    TIPO_PRODUCTO_MARCA VARCHAR(100) NOT NULL, 
    CONSTRAINT FK_CATEGORIA_PRODUCTO
        FOREIGN KEY (ID_CATEGORIA_PRODUCTO) REFERENCES Categoria_Productos(ID_CATEGORIA_PRODUCTO) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_ADMIN_PRODUCTO
        FOREIGN KEY (ID_ADMIN) REFERENCES Administradores(ID_ADMIN) ON UPDATE CASCADE,
    CONSTRAINT FK_PROVEEDOR_PRODUCTO
        FOREIGN KEY (ID_PROVEEDOR) REFERENCES Proveedores(ID_PROVEEDOR) ON UPDATE CASCADE
);

-- 10. PEDIDOS
CREATE TABLE Pedidos (
    ID_PEDIDO INT PRIMARY KEY AUTO_INCREMENT,
    ID_CLIENTE INT,
    ID_EMPLEADO INT,
    ID_ESTADO_PEDIDO INT,
    FECHA_INGRESO DATETIME NOT NULL,
    FECHA_ENTREGA DATETIME NOT NULL,
    TOTAL_PRODUCTO DECIMAL(10,2) NOT NULL,     -- Cambiado de INT a DECIMAL
    CONSTRAINT FK_CLIENTE_PEDIDO
        FOREIGN KEY (ID_CLIENTE) REFERENCES Clientes(ID_CLIENTE) ON UPDATE CASCADE,
    CONSTRAINT FK_EMPLEADO_PEDIDO
        FOREIGN KEY (ID_EMPLEADO) REFERENCES Empleados(ID_EMPLEADO) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT FK_ESTADO_PEDIDO_PEDIDO
        FOREIGN KEY (ID_ESTADO_PEDIDO) REFERENCES Estado_Pedidos(ID_ESTADO_PEDIDO)
);

-- 11. ÓRDENES DE SALIDA
CREATE TABLE Ordenes_Salida (
    ID_FACTURA INT PRIMARY KEY AUTO_INCREMENT,
    ID_CLIENTE INT,
    ID_PRODUCTO INT,
    ID_PEDIDO INT,
    FECHA_FACTURACION DATETIME NOT NULL,
    CONSTRAINT FK_ORDENSALIDA_CLIENTE
        FOREIGN KEY (ID_CLIENTE) REFERENCES Clientes(ID_CLIENTE) ON UPDATE CASCADE,
    CONSTRAINT FK_ORDENSALIDA_PRODUCTO
        FOREIGN KEY (ID_PRODUCTO) REFERENCES Productos(ID_PRODUCTO) ON UPDATE CASCADE,
    CONSTRAINT FK_ORDENSALIDA_PEDIDO
        FOREIGN KEY (ID_PEDIDO) REFERENCES Pedidos(ID_PEDIDO) ON UPDATE CASCADE
);


-- INSERCIÓN DE DATOS EN EL ORDEN CORRECTO -- 
-- (Tengan en cuenta que SIEMPRE las tablas se deben insertar por TABLA PADRE -> TABLA HIJA -> TABLA DEPENDIENTE --


-- 							ATENCION: LO IDONÉO SERIA QUE A PARTIR DE LA NORMALIZACION PODAMOS HACER LA INSERCION DE DATOS
--  														MAS COMPLETA Y TOTAL
-- 1. Clientes
INSERT INTO Clientes (NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI) VALUES
('Ana Pérez', '3101234567', 'ana.p@mail.com'),
('Luis Gómez', '3209876543', 'luis.g@mail.com'),
('Maria Rodriguez', '3001122334', 'maria.r@mail.com');

-- 2. Empleados 
INSERT INTO Empleados (NOMBRE_EMPLEADO) VALUES
('Andres Alkaeda'),
('Damian Avila'),
('Brayan Jimenez'),
('Ana Goyeneche'),
('Sharyt Zamora');

-- 3. Administradores 
INSERT INTO Administradores (NOMBRE_ADMIN, TELEFONO_ADMIN, EMAIL_ADMIN, CONTRASEÑA_ADMIN) VALUES
('Admin Uno', '3005550101', 'admin1@store.com', 'admin123');

-- 4. Proveedores 
INSERT INTO Proveedores (NUMERO_PEDIDO, NOMBRE_PROV, TELEFONO_PROV, EMAIL_PROV, FECHA_PEDIDO) VALUES
(1, 'ProvExito', '6017778899', 'ventas@provex.com', '2024-01-15'),
(2, 'DistriTodo', '6014445566', 'info@distritodo.co', '2024-02-10'),
(3, 'Suministros ElBuenPan', '6012223300', 'contacto@sumabc.com', '2024-03-05');

-- 5. Categoría de Productos
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

-- 6. Estado de Pedidos 
INSERT INTO Estado_Pedidos (NOMBRE_ESTADO) VALUES
('Pendiente'),
('En Preparación'),
('Listo para Entrega'),
('Entregado'),
('Cancelado');

-- 7. Categoría de Ingredientes
INSERT INTO Categoria_Ingredientes (NOMBRE_CATEGORIA_INGREDIENTE) VALUES
('Huevos'),
('Harinas'),
('Lácteos Base'),
('Endulzantes');

-- 8. Ingredientes 
INSERT INTO Ingredientes (ID_PROVEEDOR, ID_CATEGORIA, NOMBRE_INGREDIENTE, CANTIDAD_INGREDIENTE, FECHA_VENCIMIENTO, REFERENCIA_INGREDIENTE, FECHA_ENTREGA_INGREDIENTE) VALUES
(2, 2, 'Harina de Trigo', 100, '2025-12-20', 'HAR-TRG-05', '2025-05-15'),
(1, 3, 'Leche Entera UHT', 30, '2025-08-01', 'LECH-ENT-1L', '2025-06-05'),
(3, 4, 'Azúcar Blanca', 70, '2026-01-30', 'AZUC-BLN-KG', '2025-05-20');

-- 9. Productos 
INSERT INTO Productos (ID_ADMIN, ID_CATEGORIA_PRODUCTO, ID_PROVEEDOR, NOMBRE_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, FECHA_INGRESO_PRODUCTO, TIPO_PRODUCTO_MARCA) VALUES
(1, 8, 1, 'Tamales Tolimenses', 10, 3500.00, '2025-09-15', '2025-06-10', 'Tamales Don Chucho'),
(1, 4, 2, 'Pan Tajado Integral', 15, 4200.00, '2025-06-25', '2025-06-10', 'Bimbo'),
(1, 9, 1, 'Yogurt Fresa Litro', 12, 6000.00, '2025-07-30', '2025-06-08', 'Alpina'),
(1, 7, 3, 'Galleta de Tres Ojos', 20, 2500.00, '2025-11-01', '2025-06-05', 'Propio');


-- CONSULTAS DE VERIFICACIÓN -- 
-- PD: Se uso un JOIN como consulta -- 
-- Verificamos que los datos se insertaron correctamente --
-- La Syntaxis de abajo se usa para hacer un conteo de todos los datos insertados en cada tabla -- 


--                                  ¡¡¡REPASEN BIEN ESTO POR SI TOCA EXPONER!!!!!


SELECT 'CLIENTES' as Tabla, COUNT(*) as Total_Registros FROM Clientes
UNION ALL
SELECT 'EMPLEADOS', COUNT(*) FROM Empleados
UNION ALL
SELECT 'ADMINISTRADORES', COUNT(*) FROM Administradores
UNION ALL
SELECT 'PROVEEDORES', COUNT(*) FROM Proveedores
UNION ALL
SELECT 'CATEGORIA_PRODUCTOS', COUNT(*) FROM Categoria_Productos
UNION ALL
SELECT 'ESTADO_PEDIDOS', COUNT(*) FROM Estado_Pedidos
UNION ALL
SELECT 'CATEGORIA_INGREDIENTES', COUNT(*) FROM Categoria_Ingredientes
UNION ALL
SELECT 'INGREDIENTES', COUNT(*) FROM Ingredientes
UNION ALL
SELECT 'PRODUCTOS', COUNT(*) FROM Productos;

 -- 1. PRIMER JOIN
SELECT 
    p.ID_PRODUCTO,
    p.NOMBRE_PRODUCTO,
    cp.NOMBRE_CATEGORIAPRODUCTO as CATEGORIA,
    pr.NOMBRE_PROV as PROVEEDOR,
    p.PRECIO_PRODUCTO,
    p.PRODUCTO_STOCK_MIN,
    p.TIPO_PRODUCTO_MARCA
FROM Productos p
INNER JOIN Categoria_Productos cp ON p.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO
INNER JOIN Proveedores pr ON p.ID_PROVEEDOR = pr.ID_PROVEEDOR
INNER JOIN Administradores a ON p.ID_ADMIN = a.ID_ADMIN;


			-- ======================================== -- 
			-- | TRIGGERS Y PROCEDIMIENTOS ALMACENADOS| --
			-- ======================================== --
USE ProyectoPanaderia;
-- TRIGGER : ACTUALIZAR FECHA DE MODIFICACIÓN
-- Se agrega una columna para registrar cuándo se modifica un producto

-- Primero toca agregar la columna a la tabla Productos -- 
ALTER TABLE Productos 
ADD COLUMN FECHA_ULTIMA_MODIFICACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Crear el trigger  -- 
DELIMITER //

CREATE TRIGGER tr_actualizar_fecha_modificacion
BEFORE UPDATE ON Productos
FOR EACH ROW
BEGIN
    -- Simplemente actualiza la fecha de modificación -- 
    SET NEW.FECHA_ULTIMA_MODIFICACION = NOW();
END//

DELIMITER ;


-- PROCEDIMIENTO SIMPLE: BUSCAR PRODUCTOS POR CATEGORÍA -- 
-- Procedimiento que lista productos de una categoría específica -- 
DELIMITER //

CREATE PROCEDURE sp_productos_por_categoria(
    IN nombre_categoria VARCHAR(100)
)
BEGIN
    -- Buscar productos por nombre de categoría -- 
    SELECT 
        p.ID_PRODUCTO,
        p.NOMBRE_PRODUCTO,
        p.PRECIO_PRODUCTO,
        p.PRODUCTO_STOCK_MIN,
        p.TIPO_PRODUCTO_MARCA,
        cp.NOMBRE_CATEGORIAPRODUCTO as CATEGORIA
    FROM Productos p
    INNER JOIN Categoria_Productos cp ON p.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO
    WHERE cp.NOMBRE_CATEGORIAPRODUCTO LIKE CONCAT('%', nombre_categoria, '%') -- Aquí el "%" abarca todas las categorias indiscriminadamente -- 
    ORDER BY p.NOMBRE_PRODUCTO;
    
    -- Este modulo muestra el total de los productos encontrados -- 
    SELECT COUNT(*) as TOTAL_PRODUCTOS_ENCONTRADOS
    FROM Productos p
    INNER JOIN Categoria_Productos cp ON p.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO
    WHERE cp.NOMBRE_CATEGORIAPRODUCTO LIKE CONCAT('%', nombre_categoria, '%');
    
END//

DELIMITER ;


-- ======= APARTADO DE TESTEO :V ====== --
--  Se prueba el trigger
-- Actualizar un producto para ver si cambia la fecha de modificación -- 
UPDATE Productos 
SET PRECIO_PRODUCTO = 3800.00 
WHERE NOMBRE_PRODUCTO = 'Tamales Tolimenses';

-- Mirar si la fecha de modificación se actualizó -- 
SELECT 
    NOMBRE_PRODUCTO, 
    PRECIO_PRODUCTO, 
    FECHA_ULTIMA_MODIFICACION
FROM Productos 
WHERE NOMBRE_PRODUCTO = 'Tamales Tolimenses';

-- Se usa el procedimiento almacenado -- 
-- Buscar todos los productos de pan -- 
CALL sp_productos_por_categoria('Pan');

-- Buscar productos de tortas
CALL sp_productos_por_categoria('Torta');

-- Buscar productos de yogur
CALL sp_productos_por_categoria('Yogur');

-- APARTADO DE COMPROBACION -- 

-- Ver el trigger creado
SHOW TRIGGERS WHERE `Table` = 'Productos';

-- Ver el procedimiento creado
SHOW PROCEDURE STATUS WHERE Name = 'sp_productos_por_categoria';

-- Ver la estructura actualizada de la tabla
DESCRIBE Productos;