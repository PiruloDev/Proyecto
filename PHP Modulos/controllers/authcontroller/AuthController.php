<?php
require_once __DIR__ . '/../../services/authservices/AuthService.php';

/**
 * Controlador de autenticación JWT
 * Maneja el proceso de login, validación de tokens y redirección por roles
 */
class AuthController {
    private $authService;
    
    public function __construct() {
        $this->authService = new AuthService();
    }
    
    /**
     * Procesa el formulario de login y maneja la autenticación
     */
    public function procesarLogin() {
        $mensaje = "";
        
        // Verificar si ya hay una sesión activa
        if ($this->authService->verificarSesionActiva()) {
            $this->redirigirPorRol();
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            // Validar que los campos no estén vacíos
            if (empty($username) || empty($password)) {
                $mensaje = "<p style='color:red;'>Por favor, complete todos los campos.</p>";
            } else {
                // Intentar autenticación con el backend
                $resultadoAuth = $this->authService->autenticarUsuario($username, $password);
                
                if ($resultadoAuth['success']) {
                    // Login exitoso - iniciar sesión y redirigir
                    $this->authService->iniciarSesion($resultadoAuth['data']);
                    $this->redirigirPorRol();
                    return;
                } else {
                    // Error en la autenticación
                    $errorMsg = $resultadoAuth['data']['mensaje'] ?? 'Error de autenticación';
                    $mensaje = "<p style='color:red;'>Error: " . htmlspecialchars($errorMsg) . "</p>";
                    
                    // Log para debugging
                    error_log("Error de autenticación - Usuario: $username, Código HTTP: " . $resultadoAuth['http_code']);
                }
            }
        }
        
        // Mostrar la vista de login con el mensaje
        require_once __DIR__ . '/../../views/authviews/login.php';
    }
    
    /**
     * Redirige al usuario al dashboard correspondiente según su rol
     */
    private function redirigirPorRol() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $rol = $_SESSION['usuario_rol'] ?? 'CLIENTE';
        $dashboard = $this->authService->obtenerDashboardPorRol($rol);
        
        // Redirigir al dashboard apropiado
        header("Location: $dashboard");
        exit();
    }
    
    /**
     * Cierra la sesión del usuario y redirige al login
     */
    public function cerrarSesion() {
        $this->authService->cerrarSesion();
        header("Location: views/authviews/login.php");
        exit();
    }
    
    /**
     * Verifica si el usuario tiene una sesión válida
     * Útil para proteger páginas que requieren autenticación
     */
    public function verificarAcceso() {
        if (!$this->authService->verificarSesionActiva()) {
            header("Location: ../../loginusers.php");
            exit();
        }
        return true;
    }
    
    /**
     * Método público para verificar sesión activa
     */
    public function verificarSesionActiva() {
        return $this->authService->verificarSesionActiva();
    }
    
    /**
     * Obtiene información del usuario autenticado
     * @return array Datos del usuario en sesión
     */
    public function obtenerUsuarioActual(): array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return [
            'id' => $_SESSION['usuario_id'] ?? '',
            'nombre' => $_SESSION['usuario_nombre'] ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'rol' => $_SESSION['usuario_rol'] ?? 'CLIENTE'
        ];
    }
}
?>