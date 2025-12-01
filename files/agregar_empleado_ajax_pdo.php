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

try {
    // Conexión PDO (igual que en el login)
    $dsn = "mysql:host=localhost;dbname=proyectopanaderia;charset=utf8";
    $username = "root";
    $password = "";
    
    $pdo_conexion = new PDO($dsn, $username, $password);
    $pdo_conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener y validar datos del formulario
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_input = trim($_POST['password'] ?? '');
    
    // Validaciones
    if (empty($nombre)) {
        throw new Exception('El nombre es requerido');
    }
    
    if (empty($email)) {
        throw new Exception('El email es requerido');
    }
    
    if (empty($password_input)) {
        throw new Exception('La contraseña es requerida');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El email no tiene un formato válido');
    }
    
    if (strlen($password_input) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }
    
    // Verificar si el email o nombre ya existe
    $consulta_verificar = "SELECT ID_EMPLEADO FROM Empleados WHERE EMAIL_EMPLEADO = :email OR NOMBRE_EMPLEADO = :nombre";
    $stmt_verificar = $pdo_conexion->prepare($consulta_verificar);
    $stmt_verificar->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_verificar->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt_verificar->execute();
    
    if ($stmt_verificar->rowCount() > 0) {
        throw new Exception('Ya existe un empleado con este email o nombre');
    }
    
    // Generar salt y hashear la contraseña (exactamente como en el sistema existente)
    $salt = bin2hex(random_bytes(16));
    $password_hash = hash('sha256', $password_input . $salt);
    
    // Insertar el nuevo empleado
    $consulta_insertar = "INSERT INTO Empleados (NOMBRE_EMPLEADO, EMAIL_EMPLEADO, CONTRASEÑA_EMPLEADO, SALT_EMPLEADO, ACTIVO_EMPLEADO, FECHA_REGISTRO) 
                          VALUES (:nombre, :email, :password_hash, :salt, 1, NOW())";
    
    $stmt_insertar = $pdo_conexion->prepare($consulta_insertar);
    $stmt_insertar->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt_insertar->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt_insertar->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
    $stmt_insertar->bindParam(':salt', $salt, PDO::PARAM_STR);
    
    if ($stmt_insertar->execute()) {
        $nuevo_id = $pdo_conexion->lastInsertId();
        
        // Verificar inmediatamente el login
        $consulta_verificar_login = "SELECT ID_EMPLEADO, NOMBRE_EMPLEADO, CONTRASEÑA_EMPLEADO, SALT_EMPLEADO, ACTIVO_EMPLEADO, EMAIL_EMPLEADO
                                    FROM Empleados 
                                    WHERE (NOMBRE_EMPLEADO = :usuario OR EMAIL_EMPLEADO = :usuario) AND ACTIVO_EMPLEADO = 1";
        
        $stmt_verificar_login = $pdo_conexion->prepare($consulta_verificar_login);
        $stmt_verificar_login->bindParam(':usuario', $email, PDO::PARAM_STR);
        $stmt_verificar_login->execute();
        
        $user_data = $stmt_verificar_login->fetch(PDO::FETCH_ASSOC);
        
        $login_test = false;
        if ($user_data) {
            $hash_verificacion = hash('sha256', $password_input . $user_data['SALT_EMPLEADO']);
            $login_test = ($hash_verificacion === $user_data['CONTRASEÑA_EMPLEADO']);
        }
        
        echo json_encode([
            'success' => true,
            'mensaje' => 'Empleado agregado exitosamente',
            'empleado' => [
                'id' => $nuevo_id,
                'nombre' => htmlspecialchars($nombre),
                'email' => htmlspecialchars($email)
            ],
            'login_test' => $login_test ? 'Login funcionará correctamente' : 'ADVERTENCIA: Puede haber problema con el login'
        ]);
    } else {
        throw new Exception('Error al insertar el empleado');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>
