<?php
session_start();
header('Content-Type: application/json');

// Simple test without authentication for debugging
require_once 'conexion.php';

try {
    // Test connection
    if (!$conexion) {
        throw new Exception("No connection to database");
    }
    
    // Test basic queries
    $tests = [];
    
    // Test 1: Check if Pedidos table exists
    $result = $conexion->query("SHOW TABLES LIKE 'Pedidos'");
    $tests['pedidos_table_exists'] = $result && $result->num_rows > 0;
    
    // Test 2: Check if Estado_Pedidos table exists
    $result = $conexion->query("SHOW TABLES LIKE 'Estado_Pedidos'");
    $tests['estado_pedidos_table_exists'] = $result && $result->num_rows > 0;
    
    // Test 3: Check if Productos table exists
    $result = $conexion->query("SHOW TABLES LIKE 'Productos'");
    $tests['productos_table_exists'] = $result && $result->num_rows > 0;
    
    // Test 4: Check if Usuario table exists
    $result = $conexion->query("SHOW TABLES LIKE 'Usuario'");
    $tests['usuario_table_exists'] = $result && $result->num_rows > 0;
    
    // Test 5: Check Pedidos structure
    if ($tests['pedidos_table_exists']) {
        $result = $conexion->query("DESCRIBE Pedidos");
        $pedidos_columns = [];
        while ($row = $result->fetch_assoc()) {
            $pedidos_columns[] = $row['Field'];
        }
        $tests['pedidos_columns'] = $pedidos_columns;
    }
    
    // Test 6: Check Productos structure
    if ($tests['productos_table_exists']) {
        $result = $conexion->query("DESCRIBE Productos");
        $productos_columns = [];
        while ($row = $result->fetch_assoc()) {
            $productos_columns[] = $row['Field'];
        }
        $tests['productos_columns'] = $productos_columns;
    }
    
    // Test 7: Count records
    if ($tests['pedidos_table_exists']) {
        $result = $conexion->query("SELECT COUNT(*) as count FROM Pedidos");
        $row = $result->fetch_assoc();
        $tests['pedidos_count'] = $row['count'];
    }
    
    if ($tests['productos_table_exists']) {
        $result = $conexion->query("SELECT COUNT(*) as count FROM Productos");
        $row = $result->fetch_assoc();
        $tests['productos_count'] = $row['count'];
    }
    
    echo json_encode([
        'success' => true,
        'tests' => $tests,
        'session_info' => [
            'usuario_logueado' => $_SESSION['usuario_logueado'] ?? 'not_set',
            'usuario_tipo' => $_SESSION['usuario_tipo'] ?? 'not_set',
            'usuario_id' => $_SESSION['usuario_id'] ?? 'not_set'
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
