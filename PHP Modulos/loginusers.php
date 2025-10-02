<?php
require_once __DIR__ . '/controllers/authcontroller/AuthController.php';

// Crear instancia del controlador de autenticación
$authController = new AuthController();

$mensaje = "";

// Verificar si ya hay una sesión activa
if ($authController->verificarSesionActiva()) {
    // Redirigir al dashboard correspondiente
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $rol = $_SESSION['usuario_rol'] ?? 'CLIENTE';
    
    switch(strtoupper($rol)) {
        case 'ADMINISTRADOR':
        case 'ADMIN':
            header("Location: views/authviews/dashboardAdmin.php");
            exit();
        case 'EMPLEADO':
            header("Location: views/authviews/dashboardEmpleado.php");
            exit();
        case 'CLIENTE':
        default:
            header("Location: views/authviews/dashboardCliente.php");
            exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $mensaje = "<p style='color:red;'>Por favor, complete todos los campos.</p>";
    } else {
        // Crear instancia del servicio de autenticación
        require_once __DIR__ . '/services/authservices/AuthService.php';
        $authService = new AuthService();
        
        // Intentar autenticación
        $resultadoAuth = $authService->autenticarUsuario($username, $password);
        
        if ($resultadoAuth['success']) {
            // Login exitoso - iniciar sesión
            $authService->iniciarSesion($resultadoAuth['data']);
            
            // Obtener rol y redirigir usando el servicio
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $rol = $_SESSION['usuario_rol'] ?? 'CLIENTE';
            
            // Debug temporal para verificar el rol
            error_log("DEBUG - Rol detectado: $rol");
            
            // Redirección directa basada en el rol
            if (strtoupper($rol) === 'ADMINISTRADOR' || strtoupper($rol) === 'ADMIN') {
                header("Location: views/authviews/dashboardAdmin.php");
            } elseif (strtoupper($rol) === 'EMPLEADO') {
                header("Location: views/authviews/dashboardEmpleado.php");
            } else {
                // Cliente o cualquier otro rol va al homepage
                header("Location: templates/Homepage.php");
            }
            exit();
        } else {
            $errorMsg = $resultadoAuth['data']['mensaje'] ?? 'Error de autenticación';
            $mensaje = "<p style='color:red;'>Error: " . htmlspecialchars($errorMsg) . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Autenticación JWT</title>
</head>
<body>
    <div>
        <h1>Iniciar Sesión</h1>
        
        <!-- Mostrar mensajes de error o éxito -->
        <?php if (!empty($mensaje)): ?>
            <div>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <!-- Mensaje de registro exitoso -->
        <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
            <div>
                <p style="color:green;">¡Registro completado exitosamente! Ahora puedes iniciar sesión con tus credenciales.</p>
            </div>
        <?php endif; ?>
        
        <!-- Formulario de login -->
        <form method="POST" action="">
            <div>
                <label for="username">Usuario o Email:</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    placeholder="Ingrese su usuario o email"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
            </div>
            <br>
            <div>
                <label for="password">Contraseña:</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="Ingrese su contraseña"
                >
            </div>
            <br>
            <div>
                <button type="submit">Iniciar Sesión</button>
            </div>
        </form>
        
        <!-- Información adicional -->
        <div>
            <h3>Credenciales de prueba:</h3>
            <p><strong>Administrador:</strong> admin / password</p>
            <p><strong>Nota:</strong> Este sistema utiliza autenticación JWT del backend</p>
        </div>
        
        <!-- Enlaces de navegación -->
        <div>
            <a href="../index.php">Volver al Menú Principal</a>
        </div>
        
        <!-- Enlace al registro -->
        <div>
            <p>¿Aún no tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>