-- Este script se ejecutará automáticamente la primera vez que el contenedor MySQL se inicie.
-- Puedes crear tus tablas aquí.
-- El nombre de la base de datos (ej. mi_base_de_datos) se define en docker-compose.yml (MYSQL_DATABASE)

-- USE mi_base_de_datos; -- Descomenta y ajusta si necesitas seleccionar explícitamente la BD.

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO productos (nombre, descripcion, precio, stock) VALUES
('Laptop Pro X', 'Laptop de alto rendimiento para profesionales.', 1250.99, 50),
('Mouse Gamer Ergonómico', 'Mouse con RGB y botones programables.', 45.50, 150),
('Teclado Mecánico Retroiluminado', 'Teclado mecánico con switches azules.', 85.00, 75),
('Monitor Curvo 27" QHD', 'Monitor para una experiencia visual inmersiva.', 320.00, 30);

-- La tabla 'visitas' se crea y gestiona desde el index.php como ejemplo,
-- pero también podrías definirla aquí si lo prefieres:
/*
CREATE TABLE IF NOT EXISTS visitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_visita TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    info_cliente VARCHAR(255)
);
*/