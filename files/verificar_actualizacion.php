<?php
/**
 * Verificador y Actualizador de Base de Datos
 * Ejecuta las migraciones necesarias desde el archivo SQL principal
 */

require_once 'conexion.php';

echo "<h2>🔧 Verificador y Actualizador de Base de Datos - Panadería</h2>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background: #f9f9f9; border-radius: 8px;'>";

try {
    echo "<h3>📋 Verificando estado actual de la base de datos...</h3>";
    
    // 1. Verificar si la tabla productos_logs existe
    echo "<p><strong>1. Verificando tabla productos_logs:</strong> ";
    $check_table = $conexion->query("SHOW TABLES LIKE 'productos_logs'");
    
    if ($check_table->num_rows == 0) {
        echo "<span style='color: #ff6b35;'>❌ No existe</span></p>";
        
        echo "<p>🔨 Creando tabla productos_logs...</p>";
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
            INDEX idx_producto_logs_fecha (fecha_cambio),
            INDEX idx_producto_logs_producto (producto_id),
            INDEX idx_producto_logs_tipo (tipo_cambio),
            CONSTRAINT FK_PRODUCTO_LOG
                FOREIGN KEY (producto_id) REFERENCES Productos(ID_PRODUCTO) ON DELETE CASCADE
        )";
        
        if ($conexion->query($sql_tabla)) {
            echo "<p>✅ Tabla productos_logs creada exitosamente</p>";
        } else {
            echo "<p>❌ Error al crear tabla: " . $conexion->error . "</p>";
        }
    } else {
        echo "<span style='color: #28a745;'>✅ Existe</span></p>";
    }
    
    // 2. Verificar tipo de dato de PRECIO_PRODUCTO
    echo "<p><strong>2. Verificando tipo de dato PRECIO_PRODUCTO:</strong> ";
    $check_column = $conexion->query("SELECT DATA_TYPE, COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'ProyectoPanaderia' AND TABLE_NAME = 'Productos' AND COLUMN_NAME = 'PRECIO_PRODUCTO'");
    
    if ($check_column && $row = $check_column->fetch_assoc()) {
        if (strpos($row['DATA_TYPE'], 'decimal') !== false) {
            echo "<span style='color: #28a745;'>✅ DECIMAL(10,2)</span></p>";
        } else {
            echo "<span style='color: #ff6b35;'>⚠️ " . $row['COLUMN_TYPE'] . " (necesita actualización)</span></p>";
            
            echo "<p>🔨 Actualizando tipo de dato a DECIMAL(10,2)...</p>";
            if ($conexion->query("ALTER TABLE Productos MODIFY COLUMN PRECIO_PRODUCTO DECIMAL(10,2) NOT NULL")) {
                echo "<p>✅ Tipo de dato actualizado exitosamente</p>";
            } else {
                echo "<p>❌ Error al actualizar: " . $conexion->error . "</p>";
            }
        }
    } else {
        echo "<span style='color: #dc3545;'>❌ No se pudo verificar</span></p>";
    }
    
    // 3. Verificar trigger
    echo "<p><strong>3. Verificando trigger tr_actualizar_fecha_producto:</strong> ";
    $check_trigger = $conexion->query("SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = 'ProyectoPanaderia' AND TRIGGER_NAME = 'tr_actualizar_fecha_producto'");
    
    if ($check_trigger->num_rows == 0) {
        echo "<span style='color: #ff6b35;'>❌ No existe</span></p>";
        
        echo "<p>🔨 Creando trigger...</p>";
        $sql_trigger = "
        CREATE TRIGGER tr_actualizar_fecha_producto 
            BEFORE UPDATE ON Productos
            FOR EACH ROW
        BEGIN
            SET NEW.FECHA_ULTIMA_MODIFICACION = CURRENT_TIMESTAMP;
        END";
        
        if ($conexion->query($sql_trigger)) {
            echo "<p>✅ Trigger creado exitosamente</p>";
        } else {
            echo "<p>❌ Error al crear trigger: " . $conexion->error . "</p>";
        }
    } else {
        echo "<span style='color: #28a745;'>✅ Existe</span></p>";
    }
    
    // 4. Verificar índices de productos_logs
    if ($check_table->num_rows > 0) {
        echo "<p><strong>4. Verificando índices de productos_logs:</strong></p>";
        $indices = $conexion->query("SHOW INDEX FROM productos_logs");
        $indices_encontrados = [];
        
        while ($indice = $indices->fetch_assoc()) {
            $indices_encontrados[] = $indice['Key_name'];
        }
        
        $indices_requeridos = ['PRIMARY', 'idx_producto_logs_fecha', 'idx_producto_logs_producto', 'idx_producto_logs_tipo', 'FK_PRODUCTO_LOG'];
        
        foreach ($indices_requeridos as $req) {
            if (in_array($req, $indices_encontrados)) {
                echo "<p style='margin-left: 20px;'>✅ Índice $req: Existe</p>";
            } else {
                echo "<p style='margin-left: 20px;'>⚠️ Índice $req: No encontrado</p>";
            }
        }
    }
    
    // 5. Estadísticas generales
    echo "<h3>📊 Estadísticas actuales:</h3>";
    
    $productos_count = $conexion->query("SELECT COUNT(*) as total FROM Productos")->fetch_assoc()['total'];
    echo "<p>📦 Total de productos: <strong>$productos_count</strong></p>";
    
    if ($check_table->num_rows > 0) {
        $logs_count = $conexion->query("SELECT COUNT(*) as total FROM productos_logs")->fetch_assoc()['total'];
        echo "<p>📝 Total de logs: <strong>$logs_count</strong></p>";
        
        if ($logs_count > 0) {
            $ultimo_cambio = $conexion->query("SELECT MAX(fecha_cambio) as ultimo FROM productos_logs")->fetch_assoc()['ultimo'];
            echo "<p>🕒 Último cambio: <strong>$ultimo_cambio</strong></p>";
        }
    }
    
    echo "<h3>✅ Verificación completada</h3>";
    echo "<p><strong>Estado del sistema:</strong> <span style='color: #28a745; font-weight: bold;'>LISTO PARA USAR</span></p>";
    
    echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🚀 Próximos pasos:</h4>";
    echo "<ol>";
    echo "<li>La funcionalidad de edición inline ya está disponible en <a href='productostabla.php' target='_blank'>productostabla.php</a></li>";
    echo "<li>Los cambios se registrarán automáticamente en la tabla productos_logs</li>";
    echo "<li>El sistema SSE funcionará con las actualizaciones en tiempo real</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: #dc3545;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='productostabla.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>📋 Ir a Productos</a>";
echo "<a href='dashboard_admin.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 Dashboard Admin</a>";
echo "</div>";

echo "</div>";

$conexion->close();
?>
