<?php
header('Content-Type: application/json');
require_once 'conexion.php';

try {
    // Consulta para obtener proveedores activos
    $consulta = "SELECT 
        ID_PROVEEDOR as id,
        NOMBRE_PROV as nombre
    FROM Proveedores
    WHERE ACTIVO_PROV = 1
    ORDER BY NOMBRE_PROV ASC";
    
    $resultado = $conexion->query($consulta);
    
    if ($resultado) {
        $proveedores = [];
        while ($fila = $resultado->fetch_assoc()) {
            $proveedores[] = [
                'id' => $fila['id'],
                'nombre' => $fila['nombre']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'proveedores' => $proveedores
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
