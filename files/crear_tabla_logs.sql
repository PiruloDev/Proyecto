-- ==================================================================
-- TABLA DE LOGS PARA CAMBIOS EN PRODUCTOS
-- ==================================================================
-- Ejecutar esta consulta para crear la tabla de logs

USE ProyectoPanaderia;

-- Primero verificar y mostrar la estructura de la tabla Productos
DESCRIBE Productos;

-- Crear tabla de logs SIN la restricción de clave foránea primero
DROP TABLE IF EXISTS productos_logs;

CREATE TABLE productos_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    tipo_cambio ENUM('precio', 'stock', 'activacion', 'desactivacion') NOT NULL,
    valor_anterior VARCHAR(50),
    valor_nuevo VARCHAR(50),
    usuario_id INT,
    usuario_tipo ENUM('admin', 'empleado') DEFAULT 'admin',
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_usuario VARCHAR(45)
);

-- Crear índices para mejor rendimiento
CREATE INDEX idx_producto_logs_fecha ON productos_logs(fecha_cambio);
CREATE INDEX idx_producto_logs_producto ON productos_logs(producto_id);
CREATE INDEX idx_producto_logs_tipo ON productos_logs(tipo_cambio);

-- Ahora intentar agregar la clave foránea (opcional, puede fallar si hay problemas de integridad)
-- Si falla, la tabla funcionará igual pero sin restricción de integridad referencial
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE productos_logs 
ADD CONSTRAINT fk_productos_logs_producto 
FOREIGN KEY (producto_id) REFERENCES Productos(ID_PRODUCTO) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS = 1;

-- Verificar que la tabla se creó correctamente
DESCRIBE productos_logs;

-- Trigger para actualizar FECHA_ULTIMA_MODIFICACION en Productos automáticamente
-- Primero eliminar el trigger si existe
DROP TRIGGER IF EXISTS actualizar_fecha_producto;

DELIMITER //
CREATE TRIGGER actualizar_fecha_producto 
    BEFORE UPDATE ON Productos
    FOR EACH ROW
BEGIN
    SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
END//
DELIMITER ;

-- Verificar que el trigger se creó
SHOW TRIGGERS LIKE 'actualizar_fecha_producto';

-- Insertar un registro de prueba para verificar funcionamiento
INSERT INTO productos_logs (producto_id, tipo_cambio, valor_anterior, valor_nuevo, usuario_id, usuario_tipo, ip_usuario) 
VALUES (1, 'precio', '1000', '1200', 1, 'admin', '127.0.0.1');

-- Mostrar el registro insertado
SELECT * FROM productos_logs ORDER BY id DESC LIMIT 1;
