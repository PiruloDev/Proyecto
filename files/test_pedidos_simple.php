<?php
session_start();
header('Content-Type: application/json');

// Simple endpoint to test pedidos without complex authentication
require_once 'conexion.php';

try {
    // Just test basic query
    $query = "SELECT COUNT(*) as count FROM Pedidos";
    $result = $conexion->query($query);
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        
        // Get a few sample records
        $sample_query = "
            SELECT 
                ID_PEDIDO as id,
                FECHA_INGRESO as fecha_ingreso,
                FECHA_ENTREGA as fecha_entrega,
                TOTAL_PRODUCTO as total,
                ID_ESTADO_PEDIDO as estado
            FROM Pedidos 
            ORDER BY ID_PEDIDO DESC 
            LIMIT 5
        ";
        
        $sample_result = $conexion->query($sample_query);
        $samples = [];
        
        if ($sample_result) {
            while ($row = $sample_result->fetch_assoc()) {
                $samples[] = [
                    'id' => (int)$row['id'],
                    'cliente' => 'Cliente Test',
                    'empleado' => 'Empleado Test',
                    'fecha_ingreso' => $row['fecha_ingreso'],
                    'fecha_entrega' => $row['fecha_entrega'],
                    'total' => (float)$row['total'],
                    'estado' => (int)$row['estado'],
                    'estado_nombre' => 'Estado ' . $row['estado']
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'total_pedidos' => $count,
            'pedidos' => $samples,
            'total' => count($samples)
        ]);
    } else {
        throw new Exception("Error en consulta: " . $conexion->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>
