<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    // Validaciones del servidor
    $errores = [];
    
    // Verificar campos obligatorios
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        $errores[] = 'empty_fields';
    }
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'invalid_email';
    }
    
    // Validar longitud de contraseña
    if (strlen($password) < 6) {
        $errores[] = 'weak_password';
    }
    
    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $errores[] = 'password_mismatch';
    }
    
    // Si hay errores, redirigir
    if (!empty($errores)) {
        header('Location: registro_cliente.php?error=' . $errores[0]);
        exit();
    }
    
    try {
        // Verificar si ya existe un cliente con ese email
        $stmt = $pdo_conexion->prepare("SELECT ID_CLIENTE FROM Clientes WHERE EMAIL_CLI = ?");
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header('Location: registro_cliente.php?error=email_exists');
            exit();
        }
        
        // Verificar si ya existe un cliente con ese nombre
        $stmt = $pdo_conexion->prepare("SELECT ID_CLIENTE FROM Clientes WHERE NOMBRE_CLI = ?");
        $stmt->bindParam(1, $nombre, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            header('Location: registro_cliente.php?error=name_exists');
            exit();
        }
        
        // Generar salt aleatorio
        $salt = bin2hex(random_bytes(16));
        
        // Hashear la contraseña con salt
        $password_hash = hash('sha256', $password . $salt);
        
        // Insertar el nuevo cliente
        $stmt = $pdo_conexion->prepare("
            INSERT INTO Clientes (NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI, CONTRASEÑA_CLI, SALT_CLI, ACTIVO_CLI) 
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        
        $stmt->bindParam(1, $nombre, PDO::PARAM_STR);
        $stmt->bindParam(2, $email, PDO::PARAM_STR);
        $stmt->bindParam(3, $telefono, PDO::PARAM_STR);
        $stmt->bindParam(4, $password_hash, PDO::PARAM_STR);
        $stmt->bindParam(5, $salt, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            // Registro exitoso
            $nuevo_id = $conexion->lastInsertId();
            
            // Log del registro exitoso
            error_log("Nuevo cliente registrado - ID: $nuevo_id, Nombre: $nombre, Email: $email, IP: " . $_SERVER['REMOTE_ADDR']);
            
            // Redirigir al login con mensaje de éxito
            header('Location: login.php?success=registered');
            exit();
        } else {
            // Error en la inserción
            error_log("Error al registrar cliente");
            header('Location: registro_cliente.php?error=database_error');
            exit();
        }
        
    } catch (Exception $e) {
        // Error general
        error_log("Error en registro de cliente: " . $e->getMessage());
        header('Location: registro_cliente.php?error=database_error');
        exit();
    }
} else {
    // Acceso directo no permitido
    header('Location: registro_cliente.php');
    exit();
}
?>
