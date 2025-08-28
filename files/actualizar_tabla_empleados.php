<?php
// Archivo para verificar y actualizar la estructura de la tabla Empleados
require_once 'conexion.php';

echo "<h2>🔧 Verificación y Actualización de Tabla Empleados</h2>";

// Verificar conexión
if ($conexion->connect_error) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $conexion->connect_error . "</p>";
    exit();
}

echo "<p style='color: green;'>✅ Conexión exitosa a la base de datos</p>";

try {
    // Verificar estructura actual
    $query = "DESCRIBE Empleados";
    $result = $conexion->query($query);
    
    $campos_existentes = [];
    echo "<h3>📋 Estructura actual de la tabla Empleados:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $campos_existentes[] = $row['Field'];
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar si existen los campos necesarios
    $campos_requeridos = ['EMAIL_EMPLEADO', 'CONTRASEÑA_EMPLEADO', 'SALT_EMPLEADO'];
    $campos_faltantes = [];
    
    foreach ($campos_requeridos as $campo) {
        if (!in_array($campo, $campos_existentes)) {
            $campos_faltantes[] = $campo;
        }
    }
    
    if (empty($campos_faltantes)) {
        echo "<p style='color: green;'>✅ Todos los campos requeridos están presentes</p>";
    } else {
        echo "<h3>⚠️ Campos faltantes encontrados:</h3>";
        echo "<ul>";
        foreach ($campos_faltantes as $campo) {
            echo "<li style='color: orange;'>$campo</li>";
        }
        echo "</ul>";
        
        echo "<h3>🔨 Agregando campos faltantes...</h3>";
        
        // Agregar campos faltantes
        if (in_array('EMAIL_EMPLEADO', $campos_faltantes)) {
            $alter_query = "ALTER TABLE Empleados ADD COLUMN EMAIL_EMPLEADO VARCHAR(100) UNIQUE";
            if ($conexion->query($alter_query)) {
                echo "<p style='color: green;'>✅ Campo EMAIL_EMPLEADO agregado exitosamente</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al agregar EMAIL_EMPLEADO: " . $conexion->error . "</p>";
            }
        }
        
        if (in_array('CONTRASEÑA_EMPLEADO', $campos_faltantes)) {
            $alter_query = "ALTER TABLE Empleados ADD COLUMN CONTRASEÑA_EMPLEADO VARCHAR(255)";
            if ($conexion->query($alter_query)) {
                echo "<p style='color: green;'>✅ Campo CONTRASEÑA_EMPLEADO agregado exitosamente</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al agregar CONTRASEÑA_EMPLEADO: " . $conexion->error . "</p>";
            }
        }
        
        if (in_array('SALT_EMPLEADO', $campos_faltantes)) {
            $alter_query = "ALTER TABLE Empleados ADD COLUMN SALT_EMPLEADO VARCHAR(32)";
            if ($conexion->query($alter_query)) {
                echo "<p style='color: green;'>✅ Campo SALT_EMPLEADO agregado exitosamente</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al agregar SALT_EMPLEADO: " . $conexion->error . "</p>";
            }
        }
        
        // Agregar campo FECHA_REGISTRO si no existe
        if (!in_array('FECHA_REGISTRO', $campos_existentes)) {
            $alter_query = "ALTER TABLE Empleados ADD COLUMN FECHA_REGISTRO TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            if ($conexion->query($alter_query)) {
                echo "<p style='color: green;'>✅ Campo FECHA_REGISTRO agregado exitosamente</p>";
            } else {
                echo "<p style='color: red;'>❌ Error al agregar FECHA_REGISTRO: " . $conexion->error . "</p>";
            }
        }
        
        echo "<h3>📋 Estructura actualizada:</h3>";
        $query = "DESCRIBE Empleados";
        $result = $conexion->query($query);
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$conexion->close();

echo "<hr>";
echo "<p><strong>💡 Información:</strong></p>";
echo "<ul>";
echo "<li>EMAIL_EMPLEADO: Campo único para el email del empleado</li>";
echo "<li>CONTRASEÑA_EMPLEADO: Campo para almacenar la contraseña hasheada con SHA256</li>";
echo "<li>SALT_EMPLEADO: Campo para almacenar el salt usado en el hash</li>";
echo "<li>FECHA_REGISTRO: Campo para registrar cuándo se agregó el empleado</li>";
echo "<li><strong>Sistema de hash:</strong> SHA256 + Salt (compatible con el sistema existente)</li>";
echo "</ul>";
?>
