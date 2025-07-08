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
    
    // Consulta para obtener clientes usando prepared statement
    $consulta = "SELECT 
        ID_CLIENTE as id,
        NOMBRE_CLI as nombre,
        EMAIL_CLI as email,
        TELEFONO_CLI as telefono,
        COALESCE(ACTIVO_CLI, 1) as activo
        FROM Clientes 
        ORDER BY NOMBRE_CLI ASC";
    
    $stmt = $conexion->prepare($consulta);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado) {
        $clientes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $clientes[] = [
                'id' => $fila['id'] ?? 0,
                'nombre' => htmlspecialchars($fila['nombre'] ?? ''),
                'email' => htmlspecialchars($fila['email'] ?? ''),
                'telefono' => htmlspecialchars($fila['telefono'] ?? ''),
                'activo' => (int)($fila['activo'] ?? 1)
            ];
        }
        
        echo json_encode([
            'success' => true,
            'clientes' => $clientes,
            'total' => count($clientes)
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
