<?php
header('Content-Type: application/json');
include 'conexion.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Método no permitido. Use POST.'
    ]);
    exit();
}

// Verificar si se recibió el ID del producto y el estado actual
if (isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['estado'])) {
    $id_producto = $_POST['id'];
    $estado_actual = $_POST['estado'];
    
    // Determinar el nuevo estado (invertir el actual)
    $nuevo_estado = ($estado_actual == 1) ? 0 : 1;
    $accion_texto = ($nuevo_estado == 1) ? "activado" : "desactivado";
    
    try {
        // Obtener información del producto antes del cambio
        $consulta_producto = "SELECT NOMBRE_PRODUCTO, ACTIVO FROM Productos WHERE ID_PRODUCTO = ?";
        $stmt_producto = $conexion->prepare($consulta_producto);
        $stmt_producto->bind_param("i", $id_producto);
        $stmt_producto->execute();
        $resultado = $stmt_producto->get_result();
        $producto = $resultado->fetch_assoc();
        
        if ($producto) {
            $nombre_producto = $producto['NOMBRE_PRODUCTO'];
            
            // Actualizar el estado del producto
            $consulta_update = "UPDATE Productos SET ACTIVO = ? WHERE ID_PRODUCTO = ?";
            $stmt_update = $conexion->prepare($consulta_update);
            $stmt_update->bind_param("ii", $nuevo_estado, $id_producto);
            
            if ($stmt_update->execute()) {
                echo json_encode([
                    'success' => true,
                    'mensaje' => "El producto '{$nombre_producto}' ha sido {$accion_texto} exitosamente."
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'mensaje' => "Error: No se pudo cambiar el estado del producto."
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => "Error: El producto no existe."
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => "Error en la base de datos: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'mensaje' => "Error: Parámetros no válidos."
    ]);
}
?>
