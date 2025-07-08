<?php
session_start();

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    echo json_encode(['success' => false, 'mensaje' => 'Acceso no autorizado']);
    exit();
}

// Headers de respuesta JSON
header('Content-Type: application/json');

// Incluir conexión a la base de datos
require_once 'conexion.php';

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Consulta para obtener empleados usando prepared statement
    $consulta = "SELECT 
        ID_EMPLEADO as id,
        NOMBRE_EMPLEADO as nombre,
        EMAIL_EMPLEADO as email,
        COALESCE(ACTIVO_EMPLEADO, 1) as activo
        FROM Empleados 
        ORDER BY NOMBRE_EMPLEADO ASC";
    
    $stmt = $conexion->prepare($consulta);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado) {
        $empleados = [];
        while ($fila = $resultado->fetch_assoc()) {
            $empleados[] = [
                'id' => $fila['id'] ?? 0,
                'nombre' => htmlspecialchars($fila['nombre'] ?? ''),
                'email' => htmlspecialchars($fila['email'] ?? ''),
                'activo' => (int)($fila['activo'] ?? 1)
            ];
        }
        
        echo json_encode([
            'success' => true,
            'empleados' => $empleados,
            'total' => count($empleados)
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
