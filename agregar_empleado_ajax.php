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
    
    // Obtener y validar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validaciones
    if (empty($nombre)) {
        throw new Exception('El nombre es requerido');
    }
    
    if (empty($email)) {
        throw new Exception('El email es requerido');
    }
    
    if (empty($password)) {
        throw new Exception('La contraseña es requerida');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El email no tiene un formato válido');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }
    
    // Verificar si el email ya existe
    $consulta_verificar = "SELECT ID_EMPLEADO FROM Empleados WHERE EMAIL_EMPLEADO = ? OR NOMBRE_EMPLEADO = ?";
    $stmt_verificar = $conexion->prepare($consulta_verificar);
    
    if (!$stmt_verificar) {
        throw new Exception('Error al preparar la consulta de verificación: ' . $conexion->error);
    }
    
    $stmt_verificar->bind_param('ss', $email, $nombre);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();
    
    if ($resultado_verificar->num_rows > 0) {
        throw new Exception('Ya existe un empleado con este email o nombre');
    }
    
    $stmt_verificar->close();
    
    // Generar salt y hashear la contraseña (compatible con el sistema existente)
    $salt = bin2hex(random_bytes(16)); // Generar salt aleatorio
    $password_hash = hash('sha256', $password . $salt);
    
    // Insertar el nuevo empleado
    $consulta_insertar = "INSERT INTO Empleados (NOMBRE_EMPLEADO, EMAIL_EMPLEADO, CONTRASEÑA_EMPLEADO, SALT_EMPLEADO, ACTIVO_EMPLEADO, FECHA_REGISTRO) 
                          VALUES (?, ?, ?, ?, 1, NOW())";
    
    $stmt_insertar = $conexion->prepare($consulta_insertar);
    
    if (!$stmt_insertar) {
        throw new Exception('Error al preparar la consulta de inserción: ' . $conexion->error);
    }
    
    $stmt_insertar->bind_param('ssss', $nombre, $email, $password_hash, $salt);
    
    if ($stmt_insertar->execute()) {
        $nuevo_id = $conexion->insert_id;
        
        echo json_encode([
            'success' => true,
            'mensaje' => 'Empleado agregado exitosamente',
            'empleado' => [
                'id' => $nuevo_id,
                'nombre' => htmlspecialchars($nombre),
                'email' => htmlspecialchars($email)
            ]
        ]);
    } else {
        throw new Exception('Error al insertar el empleado: ' . $stmt_insertar->error);
    }
    
    $stmt_insertar->close();
    
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
