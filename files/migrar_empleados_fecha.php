<?php
/**
 * Script de migración para agregar la columna FECHA_REGISTRO a la tabla Empleados
 * Este script debe ejecutarse una sola vez para actualizar la base de datos existente
 */

session_start();

// Verificar que sea un administrador quien ejecute esto
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    die('Acceso no autorizado. Solo los administradores pueden ejecutar este script.');
}

echo "<h2>Migración de Base de Datos - Tabla Empleados</h2>";
echo "<p>Agregando columna FECHA_REGISTRO...</p>";

require_once 'conexion.php';

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Verificar si la columna ya existe
    $check_query = "SHOW COLUMNS FROM Empleados LIKE 'FECHA_REGISTRO'";
    $result = $conexion->query($check_query);
    
    if ($result && $result->num_rows > 0) {
        echo "<div style='color: green; font-weight: bold;'>✓ La columna FECHA_REGISTRO ya existe en la tabla Empleados.</div>";
    } else {
        // Agregar la columna
        $alter_query = "ALTER TABLE Empleados ADD COLUMN FECHA_REGISTRO TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        
        if ($conexion->query($alter_query)) {
            echo "<div style='color: green; font-weight: bold;'>✓ Columna FECHA_REGISTRO agregada exitosamente a la tabla Empleados.</div>";
            
            // Actualizar empleados existentes con fecha actual
            $update_query = "UPDATE Empleados SET FECHA_REGISTRO = NOW() WHERE FECHA_REGISTRO IS NULL";
            if ($conexion->query($update_query)) {
                echo "<div style='color: blue;'>✓ Fechas de registro actualizadas para empleados existentes.</div>";
            }
        } else {
            throw new Exception('Error al agregar la columna: ' . $conexion->error);
        }
    }
    
    // Verificar la estructura final
    echo "<h3>Estructura actual de la tabla Empleados:</h3>";
    $structure_query = "DESCRIBE Empleados";
    $structure_result = $conexion->query($structure_query);
    
    if ($structure_result) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background-color: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th><th>Extra</th></tr>";
        
        while ($row = $structure_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<div style='color: green; font-weight: bold; margin-top: 20px;'>✓ Migración completada exitosamente.</div>";
    echo "<p><a href='dashboard_admin.php'>Volver al Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-weight: bold;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p>Por favor, contacte al administrador del sistema.</p>";
}

// Cerrar conexión
if (isset($conexion)) {
    $conexion->close();
}
?>
