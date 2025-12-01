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
    // Consulta para obtener categorías de ingredientes
    $consulta = "SELECT 
        ID_CATEGORIA as id,
        NOMBRE_CATEGORIA_INGREDIENTE as nombre
    FROM Categoria_Ingredientes
    ORDER BY NOMBRE_CATEGORIA_INGREDIENTE ASC";
    
    $resultado = $conexion->query($consulta);
    
    if ($resultado) {
        $categorias = [];
        while ($fila = $resultado->fetch_assoc()) {
            $categorias[] = [
                'id' => $fila['id'],
                'nombre' => $fila['nombre']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'categorias' => $categorias
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Error en la consulta: ' . $conexion->error
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error del servidor: ' . $e->getMessage()
    ]);
}

$conexion->close();
?>
