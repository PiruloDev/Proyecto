<?php
/**
 * Script para verificar y crear la tabla de logs si es necesario
 * Ejecutar una sola vez para configurar el sistema
 */

require_once 'conexion.php';

try {
    // Verificar si la tabla productos_logs existe
    $check_table = $conexion->query("SHOW TABLES LIKE 'productos_logs'");
    
    if ($check_table->num_rows == 0) {
        echo "Tabla productos_logs no encontrada. Creando...<br>";
        
        // Crear tabla de logs
        $sql_tabla = "
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
            FOREIGN KEY (producto_id) REFERENCES Productos(ID_PRODUCTO) ON DELETE CASCADE
        )";
        
        if ($conexion->query($sql_tabla)) {
            echo "✅ Tabla productos_logs creada exitosamente<br>";
        } else {
            echo "❌ Error al crear tabla productos_logs: " . $conexion->error . "<br>";
        }
        
        // Crear índices
        $indices = [
            "CREATE INDEX idx_producto_logs_fecha ON productos_logs(fecha_cambio)",
            "CREATE INDEX idx_producto_logs_producto ON productos_logs(producto_id)", 
            "CREATE INDEX idx_producto_logs_tipo ON productos_logs(tipo_cambio)"
        ];
        
        foreach ($indices as $indice) {
            if ($conexion->query($indice)) {
                echo "✅ Índice creado exitosamente<br>";
            } else {
                echo "❌ Error al crear índice: " . $conexion->error . "<br>";
            }
        }
        
    } else {
        echo "✅ Tabla productos_logs ya existe<br>";
    }
    
    // Verificar si el trigger existe
    $check_trigger = $conexion->query("SHOW TRIGGERS LIKE 'actualizar_fecha_producto'");
    
    if ($check_trigger->num_rows == 0) {
        echo "Trigger actualizar_fecha_producto no encontrado. Creando...<br>";
        
        $sql_trigger = "
        CREATE TRIGGER actualizar_fecha_producto 
            BEFORE UPDATE ON Productos
            FOR EACH ROW
        BEGIN
            SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
        END";
        
        if ($conexion->query($sql_trigger)) {
            echo "✅ Trigger actualizar_fecha_producto creado exitosamente<br>";
        } else {
            echo "❌ Error al crear trigger: " . $conexion->error . "<br>";
        }
    } else {
        echo "✅ Trigger actualizar_fecha_producto ya existe<br>";
    }
    
    echo "<br><strong>Configuración completada!</strong><br>";
    echo "<a href='productostabla.php'>Ir a Gestión de Productos</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

$conexion->close();
?>
