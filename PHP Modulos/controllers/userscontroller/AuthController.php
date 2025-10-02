<?php
require_once __DIR__ . '/../../services/userservices/AuthService.php';

class AuthController {
    private $authService;
    
    public function __construct() {
        $this->authService = new AuthService();
    }
    
    /**
     * Procesa el formulario de login y autentica al usuario
     */
    public function procesarLogin() {
        $mensaje = "";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            
            if (!empty($email) && !empty($contrasena)) {
                $credenciales = [
                    'email' => $email,
                    'contrasena' => $contrasena
                ];
                
                $resultadoAuth = $this->authService->autenticarUsuario($credenciales);
                
                if ($resultadoAuth['success']) {
                    // Extraer datos del token JWT del backend
                    $datosUsuario = $resultadoAuth['data'];
                    
                    // Iniciar sesión con los datos del usuario
                    $this->authService->iniciarSesion($datosUsuario);
                    
                    // Redirigir según el rol del usuario
                    $this->redirigirSegunRol($datosUsuario['rol'] ?? 'Cliente');
                } else {
                    $mensaje = $resultadoAuth['error'] ?? "Credenciales incorrectas. Por favor, verifica tu email y contraseña.";
                }
            } else {
                $mensaje = "Por favor, completa todos los campos.";
            }
        }
        
        // Cargar la vista de login
        require_once __DIR__ . '/../../views/authviews/login.php';
    }
    
    /**
     * Procesa el formulario de registro para nuevos clientes
     */
    public function procesarRegistro() {
        $mensaje = "";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            
            if (!empty($nombre) && !empty($email) && !empty($telefono) && !empty($contrasena)) {
                $datosCliente = [
                    'nombre' => $nombre,
                    'email' => $email,
                    'telefono' => $telefono,
                    'contrasena' => $contrasena
                ];
                
                $resultadoRegistro = $this->authService->registrarCliente($datosCliente);
                
                if ($resultadoRegistro['success']) {
                    // Redirigir al login después del registro exitoso
                    $mensajeExito = urlencode("Registro exitoso. Ya puedes iniciar sesión con tus credenciales.");
                    header("Location: AuthController.php?action=login&mensaje=registro_exitoso&detalle=" . $mensajeExito);
                    exit();
                } else {
                    $mensaje = "Error al registrar el cliente. " . ($resultadoRegistro['error'] ?? 'Error desconocido');
                }
            } else {
                $mensaje = "Todos los campos son requeridos.";
            }
        }
        
        // Cargar la vista de registro
        require_once __DIR__ . '/../../views/authviews/registro.php';
    }
    
    /**
     * Cierra la sesión del usuario y lo redirige
     */
    public function procesarLogout() {
        $rol = $this->authService->obtenerRolUsuario();
        $this->authService->cerrarSesion();
        
        // Redirigir según el contexto del usuario
        if (strtolower($rol ?? '') === 'cliente') {
            header("Location: ../../views/homepage.php");
        } else {
            header("Location: AuthController.php?action=login");
        }
        exit();
    }
    
    /**
     * Redirige al usuario según su rol después del login
     */
    private function redirigirSegunRol(?string $rol): void {
        // Normalizar el rol para manejar diferentes formatos del backend
        $rolNormalizado = strtolower($rol ?? '');
        
        switch ($rolNormalizado) {
            case 'administrador':
                header("Location: ../../views/authviews/dashboard_admin.php");
                break;
            case 'empleado':
                header("Location: ../../views/authviews/dashboard_empleado.php");
                break;
            case 'cliente':
                header("Location: ../../views/homepage.php");
                break;
            default:
                // Agregar debug para ver qué rol se está recibiendo
                error_log("AuthController - Rol no reconocido: '" . ($rol ?? 'null') . "' (normalizado: '" . $rolNormalizado . "')");
                header("Location: /PHP%20Modulos/controllers/userscontroller/AuthController.php?action=login&error=rol_invalido&rol_recibido=" . urlencode($rol ?? 'null'));
                break;
        }
        exit();
    }
    
    /**
     * Verifica si el usuario tiene permisos para acceder a una sección
     */
    public function verificarAcceso(array $rolesPermitidos): bool {
        if (!$this->authService->estaAutenticado()) {
            return false;
        }
        
        $rolUsuario = strtolower($this->authService->obtenerRolUsuario() ?? '');
        $rolesPermitidosNormalizados = array_map('strtolower', $rolesPermitidos);
        
        return in_array($rolUsuario, $rolesPermitidosNormalizados);
    }
    
    /**
     * Middleware para proteger rutas que requieren autenticación
     */
    public function protegerRuta(array $rolesPermitidos = []): void {
        // Debug: Log de verificación de acceso
        error_log("AuthController - Protegiendo ruta. Roles permitidos: " . json_encode($rolesPermitidos));
        
        if (!$this->authService->estaAutenticado()) {
            error_log("AuthController - Usuario no autenticado");
            header("Location: AuthController.php?action=login&error=no_autenticado");
            exit();
        }
        
        $rolUsuario = $this->authService->obtenerRolUsuario();
        error_log("AuthController - Rol del usuario: '" . ($rolUsuario ?? 'null') . "'");
        
        if (!empty($rolesPermitidos) && !$this->verificarAcceso($rolesPermitidos)) {
            error_log("AuthController - Acceso denegado. Rol: '$rolUsuario', Permitidos: " . json_encode($rolesPermitidos));
            header("Location: AuthController.php?action=login&error=sin_permisos&rol_usuario=" . urlencode($rolUsuario ?? 'null'));
            exit();
        }
        
        error_log("AuthController - Acceso concedido para rol: '$rolUsuario'");
    }
}

// Manejo de las acciones del controlador
if (isset($_GET['action'])) {
    $controller = new AuthController();
    
    switch ($_GET['action']) {
        case 'login':
            $controller->procesarLogin();
            break;
        case 'registro':
            $controller->procesarRegistro();
            break;
        case 'logout':
            $controller->procesarLogout();
            break;
        default:
            $controller->procesarLogin();
            break;
    }
} else {
    // Por defecto, mostrar login
    $controller = new AuthController();
    $controller->procesarLogin();
}
?>