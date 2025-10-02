<?php
require_once __DIR__ . '/../../controllers/authcontroller/AuthController.php';

/**
 * Utilidad para verificación de sesiones JWT
 * Incluir este archivo en páginas que requieren autenticación
 */
class SessionGuard {
    private static $authController;
    
    /**
     * Inicializa el guard de sesión
     */
    public static function init() {
        if (self::$authController === null) {
            self::$authController = new AuthController();
        }
    }
    
    /**
     * Verifica que el usuario esté autenticado
     * Redirige al login si no hay sesión válida
     */
    public static function requireAuth() {
        self::init();
        self::$authController->verificarAcceso();
    }
    
    /**
     * Verifica que el usuario tenga el rol requerido
     * @param string|array $rolesPermitidos Rol o array de roles permitidos
     */
    public static function requireRole($rolesPermitidos) {
        self::requireAuth();
        
        $usuario = self::$authController->obtenerUsuarioActual();
        $rolUsuario = strtoupper($usuario['rol']);
        
        if (is_string($rolesPermitidos)) {
            $rolesPermitidos = [strtoupper($rolesPermitidos)];
        } else {
            $rolesPermitidos = array_map('strtoupper', $rolesPermitidos);
        }
        
        if (!in_array($rolUsuario, $rolesPermitidos)) {
            http_response_code(403);
            echo "<h1>Acceso Denegado</h1>";
            echo "<p>No tiene permisos para acceder a esta página.</p>";
            echo "<a href='../../loginusers.php'>Volver al Login</a>";
            exit();
        }
    }
    
    /**
     * Obtiene los datos del usuario actual
     * @return array Datos del usuario autenticado
     */
    public static function getUser() {
        self::init();
        return self::$authController->obtenerUsuarioActual();
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public static function logout() {
        self::init();
        self::$authController->cerrarSesion();
    }
}
?>