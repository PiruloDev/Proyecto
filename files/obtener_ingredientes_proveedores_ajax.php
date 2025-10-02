<?php
session_start();

// Headers de seguridad
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Type: application/json; charset=utf-8');

// Verificación de autenticación
if (!isset($_SESSION['usuario_logueado']) || 
    $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || 
    $_SESSION['usuario_tipo'] !== 'admin') {
    
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'No autorizado'
    ]);
    exit();
}

require_once 'conexion.php';

try {
    if (!$conexion) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Consulta para obtener ingredientes con información del proveedor
    $sql = "SELECT 
                i.ID_INGREDIENTE,
                i.NOMBRE_INGREDIENTE,
                i.CANTIDAD_INGREDIENTE as STOCK,
                i.PRECIO_COMPRA,
                i.REFERENCIA_INGREDIENTE,
                i.FECHA_VENCIMIENTO,
                i.FECHA_ENTREGA_INGREDIENTE,
                p.NOMBRE_PROV as PROVEEDOR,
                p.TELEFONO_PROV as TELEFONO_PROVEEDOR,
                p.EMAIL_PROV as EMAIL_PROVEEDOR,
                p.ID_PROVEEDOR,
                c.NOMBRE_CATEGORIA_INGREDIENTE as CATEGORIA,
                c.ID_CATEGORIA
            FROM Ingredientes i
            LEFT JOIN Proveedores p ON i.ID_PROVEEDOR = p.ID_PROVEEDOR
            LEFT JOIN Categoria_Ingredientes c ON i.ID_CATEGORIA = c.ID_CATEGORIA
            ORDER BY i.NOMBRE_INGREDIENTE ASC";
    
    $result = $conexion->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conexion->error);
    }
    
    $ingredientes = [];
    while ($row = $result->fetch_assoc()) {
        $ingredientes[] = array(
            'id' => $row['ID_INGREDIENTE'],
            'nombre' => $row['NOMBRE_INGREDIENTE'],
            'stock' => $row['STOCK'],
            'precio_compra' => $row['PRECIO_COMPRA'],
            'referencia' => $row['REFERENCIA_INGREDIENTE'],
            'fecha_vencimiento' => $row['FECHA_VENCIMIENTO'],
            'fecha_entrega' => $row['FECHA_ENTREGA_INGREDIENTE'],
            'proveedor' => $row['PROVEEDOR'] ?? 'Sin asignar',
            'telefono_proveedor' => $row['TELEFONO_PROVEEDOR'] ?? '',
            'email_proveedor' => $row['EMAIL_PROVEEDOR'] ?? '',
            'categoria' => $row['CATEGORIA'] ?? 'Sin categoría',
            'id_categoria' => $row['ID_CATEGORIA'],
            'id_proveedor' => $row['ID_PROVEEDOR']
        );
    }
    
    $response = array(
        'success' => true,
        'data' => $ingredientes,
        'total' => count($ingredientes)
    );
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    $response = array(
        'success' => false,
        'error' => $e->getMessage()
    );
    echo json_encode($response);
}

$conexion->close();
?>
