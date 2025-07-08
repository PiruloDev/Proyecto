<?php
header('Content-Type: application/json');
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
