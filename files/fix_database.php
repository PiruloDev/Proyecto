<?php
include 'conexion.php';

echo "Verificando y corrigiendo estructura de base de datos...\n\n";

try {
    // 1. Verificar y agregar columna ACTIVO en Productos
    $result = $conexion->query("SHOW COLUMNS FROM Productos LIKE 'ACTIVO'");
    
    if ($result->num_rows == 0) {
        echo "Agregando columna ACTIVO a Productos...\n";
        $conexion->query("ALTER TABLE Productos ADD COLUMN ACTIVO BOOLEAN DEFAULT TRUE");
        echo "✅ Columna ACTIVO agregada a Productos\n";
    } else {
        echo "✅ Columna ACTIVO ya existe en Productos\n";
    }
    
    // 2. Verificar ACTIVO_CLI en Clientes
    $result = $conexion->query("SHOW COLUMNS FROM Clientes LIKE 'ACTIVO_CLI'");
    
    if ($result->num_rows == 0) {
        echo "Agregando columna ACTIVO_CLI a Clientes...\n";
        $conexion->query("ALTER TABLE Clientes ADD COLUMN ACTIVO_CLI BOOLEAN DEFAULT TRUE");
        echo "✅ Columna ACTIVO_CLI agregada a Clientes\n";
    } else {
        echo "✅ Columna ACTIVO_CLI ya existe en Clientes\n";
    }
    
    // 3. Actualizar productos existentes
    $conexion->query("UPDATE Productos SET ACTIVO = 1 WHERE ACTIVO IS NULL");
    $conexion->query("UPDATE Clientes SET ACTIVO_CLI = 1 WHERE ACTIVO_CLI IS NULL");
    
    echo "\n🎉 Base de datos actualizada correctamente!\n";
    echo "Ahora puedes usar:\n";
    echo "- registro_cliente.php\n";
    echo "- productostabla.php\n";
    echo "- Todos los archivos de gestión\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
