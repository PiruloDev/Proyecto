<?php
session_start();
include 'conexion.php';

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Obtener y validar los datos del formulario
    $tipo_usuario = trim($_POST['tipo_usuario']);
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    
    // Validaciones básicas
    if (empty($tipo_usuario) || empty($usuario) || empty($password)) {
        header("Location: login.php?error=missing_data");
        exit();
    }
    
    // Validar tipo de usuario
    if (!in_array($tipo_usuario, ['admin', 'empleado', 'cliente'])) {
        header("Location: login.php?error=invalid_user_type");
        exit();
    }
    
    try {
        $consulta = "";
        $campo_usuario = "";
        $tabla = "";
        $id_campo = "";
        $nombre_campo = "";
        
        // Determinar la tabla y campos según el tipo de usuario
        switch ($tipo_usuario) {
            case 'admin':
                $tabla = "Administradores";
                $campo_usuario = "NOMBRE_ADMIN";
                $id_campo = "ID_ADMIN";
                $nombre_campo = "NOMBRE_ADMIN";
                $consulta = "SELECT ID_ADMIN, NOMBRE_ADMIN, CONTRASEÑA_ADMIN, SALT_ADMIN, EMAIL_ADMIN, TELEFONO_ADMIN 
                FROM Administradores 
                WHERE (NOMBRE_ADMIN = :usuario OR EMAIL_ADMIN = :usuario)";
                break;
                
            case 'empleado':
                $tabla = "Empleados";
                $campo_usuario = "NOMBRE_EMPLEADO";
                $id_campo = "ID_EMPLEADO";
                $nombre_campo = "NOMBRE_EMPLEADO";
                $consulta = "SELECT ID_EMPLEADO, NOMBRE_EMPLEADO, CONTRASEÑA_EMPLEADO, SALT_EMPLEADO, ACTIVO_EMPLEADO
                FROM Empleados 
                WHERE NOMBRE_EMPLEADO = :usuario AND ACTIVO_EMPLEADO = 1";
                break;
                
            case 'cliente':
                $tabla = "Clientes";
                $campo_usuario = "EMAIL_CLI";
                $id_campo = "ID_CLIENTE";
                $nombre_campo = "NOMBRE_CLI";
                $consulta = "SELECT ID_CLIENTE, NOMBRE_CLI, EMAIL_CLI, CONTRASEÑA_CLI, SALT_CLI, TELEFONO_CLI, ACTIVO_CLI
                FROM Clientes 
                WHERE (EMAIL_CLI = :usuario OR NOMBRE_CLI = :usuario) AND ACTIVO_CLI = 1";
                break;
        }
        
        // Preparar y ejecutar la consulta
        $stmt = $pdo_conexion->prepare($consulta);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();
        
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Log para debug
        error_log("Login Debug - Tipo: $tipo_usuario, Usuario buscado: $usuario, Usuario encontrado: " . ($user_data ? "SÍ" : "NO"));
        
        if ($user_data) {
            // Usuario encontrado, verificar contraseña
            $password_hash = "";
            $salt = "";
            
            switch ($tipo_usuario) {
                case 'admin':
                    $password_hash = $user_data['CONTRASEÑA_ADMIN'];
                    $salt = $user_data['SALT_ADMIN'];
                    break;
                case 'empleado':
                    $password_hash = $user_data['CONTRASEÑA_EMPLEADO'];
                    $salt = $user_data['SALT_EMPLEADO'];
                    break;
                case 'cliente':
                    $password_hash = $user_data['CONTRASEÑA_CLI'];
                    $salt = $user_data['SALT_CLI'];
                    break;
            }
            
            // Verificar la contraseña
            $password_valid = false;
            
            // Log para debug de verificación de contraseña
            error_log("Verificando contraseña - Salt: " . ($salt ? "SÍ" : "NO") . ", Hash almacenado: " . ($password_hash ? "SÍ" : "NO"));
            
            if (!empty($salt) && !empty($password_hash)) {
                // Verificar con hash SHA256 + salt (sistema actual)
                $hash_to_check = hash('sha256', $password . $salt);
                error_log("Hash generado para verificación: " . substr($hash_to_check, 0, 20) . "...");
                error_log("Hash almacenado en BD: " . substr($password_hash, 0, 20) . "...");
                
                if ($hash_to_check === $password_hash) {
                    $password_valid = true;
                    error_log("Contraseña válida con hash SHA256");
                }
            } elseif (!empty($password_hash)) {
                // Verificar contraseña en texto plano (compatibilidad)
                if ($password === $password_hash) {
                    $password_valid = true;
                    error_log("Contraseña válida en texto plano");
                }
            }
            
            error_log("Resultado verificación de contraseña: " . ($password_valid ? "VÁLIDA" : "INVÁLIDA"));
            
            if ($password_valid) {
                // Contraseña correcta, crear sesión
                $_SESSION['usuario_logueado'] = true;
                $_SESSION['usuario_tipo'] = $tipo_usuario;
                $_SESSION['usuario_id'] = $user_data[$id_campo];
                $_SESSION['usuario_nombre'] = $user_data[$nombre_campo];
                
                // Información adicional según el tipo de usuario
                if ($tipo_usuario === 'admin') {
                    $_SESSION['usuario_email'] = $user_data['EMAIL_ADMIN'] ?? '';
                    $_SESSION['usuario_telefono'] = $user_data['TELEFONO_ADMIN'] ?? '';
                } elseif ($tipo_usuario === 'cliente') {
                    $_SESSION['usuario_email'] = $user_data['EMAIL_CLI'];
                    $_SESSION['usuario_telefono'] = $user_data['TELEFONO_CLI'] ?? '';
                }
                
                // Log del login exitoso
                error_log("Login exitoso - Tipo: $tipo_usuario, Usuario: " . $user_data[$nombre_campo] . ", IP: " . $_SERVER['REMOTE_ADDR']);
                
                // Redirigir según el tipo de usuario
                switch ($tipo_usuario) {
                    case 'admin':
                        header("Location: dashboard_admin.php");
                        break;
                    case 'empleado':
                        header("Location: dashboard_empleado.php");
                        break;
                    case 'cliente':
                        header("Location: dashboard_cliente.php");
                        break;
                }
                exit();
                
            } else {
                // Contraseña incorrecta
                header("Location: login.php?error=invalid_credentials");
                exit();
            }
            
        } else {
            // Usuario no encontrado
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
        
    } catch (PDOException $e) {
        // Error en la base de datos
        error_log("Error de login (PDO): " . $e->getMessage() . " - Usuario: $usuario, Tipo: $tipo_usuario");
        header("Location: login.php?error=database_error&detail=" . urlencode($e->getMessage()));
        exit();
    } catch (Exception $e) {
        // Error general
        error_log("Error de login (General): " . $e->getMessage() . " - Usuario: $usuario, Tipo: $tipo_usuario");
        header("Location: login.php?error=database_error&detail=" . urlencode($e->getMessage()));
        exit();
    }
    
} else {
    // Acceso directo sin POST
    header("Location: login.php");
    exit();
}
?>
