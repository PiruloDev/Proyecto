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
    
    // Consulta para obtener administradores usando prepared statement
    $consulta = "SELECT 
        ID_ADMIN as id,
        NOMBRE_ADMIN as nombre,
        EMAIL_ADMIN as email,
        ACTIVO_ADMIN as activo
        FROM Administradores 
        ORDER BY NOMBRE_ADMIN ASC";
    
    $stmt = $conexion->prepare($consulta);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado) {
        $administradores = [];
        while ($fila = $resultado->fetch_assoc()) {
            $administradores[] = [
                'id' => $fila['id'] ?? 0,
                'nombre' => htmlspecialchars($fila['nombre'] ?? ''),
                'email' => htmlspecialchars($fila['email'] ?? ''),
                'activo' => $fila['activo'] ?? 1
            ];
        }
        
        echo json_encode([
            'success' => true,
            'administradores' => $administradores,
            'total' => count($administradores)
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
