<?php
session_start();

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    echo json_encode(['success' => false, 'mensaje' => 'Acceso no autorizado']);
    exit();
}

header('Content-Type: application/json');
require_once 'conexion.php';

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Consulta para obtener ingredientes con información de categoría y proveedor
    $consulta = "SELECT 
        i.ID_INGREDIENTE as id,
        i.NOMBRE_INGREDIENTE as nombre,
        i.CANTIDAD_INGREDIENTE as cantidad,
        i.FECHA_VENCIMIENTO as fecha_vencimiento,
        i.REFERENCIA_INGREDIENTE as referencia,
        i.FECHA_ENTREGA_INGREDIENTE as fecha_entrega,
        c.NOMBRE_CATEGORIA_INGREDIENTE as categoria,
        p.NOMBRE_PROV as proveedor
    FROM Ingredientes i
    LEFT JOIN Categoria_Ingredientes c ON i.ID_CATEGORIA = c.ID_CATEGORIA
    LEFT JOIN Proveedores p ON i.ID_PROVEEDOR = p.ID_PROVEEDOR
    ORDER BY i.ID_INGREDIENTE DESC";
    
    $stmt = $conexion->prepare($consulta);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado) {
        $ingredientes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ingredientes[] = [
                'id' => $fila['id'] ?? 0,
                'nombre' => htmlspecialchars($fila['nombre'] ?? ''),
                'cantidad' => $fila['cantidad'] ?? 0,
                'categoria' => htmlspecialchars($fila['categoria'] ?? ''),
                'proveedor' => htmlspecialchars($fila['proveedor'] ?? ''),
                'fecha_vencimiento' => !empty($fila['fecha_vencimiento']) ? date('d/m/Y', strtotime($fila['fecha_vencimiento'])) : '',
                'referencia' => htmlspecialchars($fila['referencia'] ?? ''),
                'fecha_entrega' => !empty($fila['fecha_entrega']) ? date('d/m/Y', strtotime($fila['fecha_entrega'])) : ''
            ];
        }
        
        echo json_encode([
            'success' => true,
            'ingredientes' => $ingredientes,
            'total' => count($ingredientes)
        ]);
    } else {
        throw new Exception('Error al ejecutar la consulta');
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error del servidor: ' . $e->getMessage()
    ]);
}

// Cerrar conexión
if (isset($conexion)) {
    $conexion->close();
}
?>
