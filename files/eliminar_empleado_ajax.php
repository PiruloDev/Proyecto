<?php
session_start();

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    echo json_encode(['success' => false, 'mensaje' => 'Acceso no autorizado']);
    exit();
}

// Headers de respuesta JSON
header('Content-Type: application/json');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Incluir conexión a la base de datos
require_once 'conexion.php';

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Obtener datos JSON del cuerpo de la petición
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
    if (!$input || !isset($input['id'])) {
        throw new Exception('ID de empleado requerido');
    }
    
    // Validar ID del empleado
    $empleado_id = filter_var($input['id'], FILTER_VALIDATE_INT);
    
    if ($empleado_id === false || $empleado_id <= 0) {
        throw new Exception('ID de empleado inválido');
    }
    
    // Verificar que el empleado existe
    $consulta_verificar = "SELECT ID_EMPLEADO, NOMBRE_EMPLEADO FROM Empleados WHERE ID_EMPLEADO = ?";
    $stmt_verificar = $conexion->prepare($consulta_verificar);
    
    if (!$stmt_verificar) {
        throw new Exception('Error al preparar la consulta de verificación: ' . $conexion->error);
    }
    
    $stmt_verificar->bind_param('i', $empleado_id);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();
    
    if ($resultado_verificar->num_rows === 0) {
        throw new Exception('Empleado no encontrado');
    }
    
    $empleado = $resultado_verificar->fetch_assoc();
    $stmt_verificar->close();
    
    // Verificar si el empleado es administrador (prevenir eliminación accidental)
    $consulta_admin = "SELECT ID_ADMIN FROM Administradores WHERE EMAIL_ADMIN = (SELECT EMAIL_EMPLEADO FROM Empleados WHERE ID_EMPLEADO = ?)";
    $stmt_admin = $conexion->prepare($consulta_admin);
    
    if ($stmt_admin) {
        $stmt_admin->bind_param('i', $empleado_id);
        $stmt_admin->execute();
        $resultado_admin = $stmt_admin->get_result();
        
        if ($resultado_admin->num_rows > 0) {
            throw new Exception('No se puede eliminar un empleado que también es administrador');
        }
        
        $stmt_admin->close();
    }
    
    // Eliminar el empleado
    $consulta_eliminar = "DELETE FROM Empleados WHERE ID_EMPLEADO = ?";
    $stmt_eliminar = $conexion->prepare($consulta_eliminar);
    
    if (!$stmt_eliminar) {
        throw new Exception('Error al preparar la consulta de eliminación: ' . $conexion->error);
    }
    
    $stmt_eliminar->bind_param('i', $empleado_id);
    
    if ($stmt_eliminar->execute()) {
        if ($stmt_eliminar->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'mensaje' => 'Empleado eliminado exitosamente',
                'empleado' => [
                    'id' => $empleado_id,
                    'nombre' => htmlspecialchars($empleado['NOMBRE_EMPLEADO'])
                ]
            ]);
        } else {
            throw new Exception('No se pudo eliminar el empleado');
        }
    } else {
        throw new Exception('Error al eliminar el empleado: ' . $stmt_eliminar->error);
    }
    
    $stmt_eliminar->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}

// Cerrar conexión
if (isset($conexion)) {
    $conexion->close();
}
?>
