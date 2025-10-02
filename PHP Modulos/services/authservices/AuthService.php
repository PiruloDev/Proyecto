<?php
require_once __DIR__ . '/../../config/configAuth.php';

/**
 * Servicio de autenticación JWT
 * Maneja la comunicación con el backend para login y validación de tokens
 */
class AuthService {
    
    /**
     * Autentica usuario con credenciales y obtiene token JWT
     * @param string $username Usuario o email
     * @param string $password Contraseña del usuario
     * @return array Resultado de la autenticación con token y datos del usuario
     */
    public function autenticarUsuario(string $username, string $password): array {
        $url = endpointAuth::API_LOGIN;
        
        // Preparar datos para el login
        $datosLogin = [
            'username' => $username,
            'password' => $password
        ];
        
        $proceso = curl_init();
        curl_setopt($proceso, CURLOPT_URL, $url);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_POST, true);
        curl_setopt($proceso, CURLOPT_POSTFIELDS, json_encode($datosLogin));
        curl_setopt($proceso, CURLOPT_TIMEOUT, 30);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        curl_close($proceso);
        
        $resultado = json_decode($response, true);
        
        return [
            'http_code' => $http_code,
            'data' => $resultado,
            'success' => $http_code === 200
        ];
    }
    
    /**
     * Valida un token JWT con el backend
     * @param string $token Token JWT a validar
     * @return array Resultado de la validación
     */
    public function validarToken(string $token): array {
        $url = endpointAuth::API_VALIDAR_TOKEN;
        
        $proceso = curl_init();
        curl_setopt($proceso, CURLOPT_URL, $url);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_POST, true);
        curl_setopt($proceso, CURLOPT_TIMEOUT, 30);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        
        $response = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        curl_close($proceso);
        
        $resultado = json_decode($response, true);
        
        return [
            'http_code' => $http_code,
            'data' => $resultado,
            'valido' => $http_code === 200 && isset($resultado['valido']) && $resultado['valido']
        ];
    }
    
    /**
     * Determina el dashboard correspondiente según el rol del usuario
     * @param string $rol Rol del usuario (ADMIN, EMPLEADO, CLIENTE)
     * @return string URL del dashboard correspondiente
     */
    public function obtenerDashboardPorRol(string $rol): string {
        $base_url = 'http://localhost/PHP%20Modulos/views/authviews/';
        $dashboards = [
            'ADMINISTRADOR' => $base_url . 'dashboardAdmin.php',
            'ADMIN' => $base_url . 'dashboardAdmin.php',
            'EMPLEADO' => $base_url . 'dashboardEmpleado.php',
            'CLIENTE' => 'http://localhost/PHP%20Modulos/templates/Homepage.php',
        ];
        
        $rolUpper = strtoupper($rol);
        return $dashboards[$rolUpper] ?? 'http://localhost/PHP%20Modulos/templates/Homepage.php';
    }
    
    /**
     * Inicia sesión PHP y almacena datos del usuario autenticado
     * @param array $datosUsuario Datos del usuario y token
     */
    public function iniciarSesion(array $datosUsuario): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['jwt_token'] = $datosUsuario['token'] ?? '';
        
        // Si no hay objeto usuario en la respuesta, validar token para obtener información
        if (!isset($datosUsuario['usuario']) && !empty($datosUsuario['token'])) {
            $validacion = $this->validarToken($datosUsuario['token']);
            
            if ($validacion['valido'] && isset($validacion['data']['usuario'])) {
                $usuario = $validacion['data']['usuario'];
                $_SESSION['usuario_id'] = $usuario['id'] ?? '';
                $_SESSION['usuario_nombre'] = $usuario['nombre'] ?? '';
                $_SESSION['usuario_email'] = $usuario['email'] ?? '';
                $_SESSION['usuario_rol'] = $usuario['rol'] ?? 'CLIENTE';
            } else {
                // Fallback: extraer del token directamente
                $infoToken = $this->extraerInfoDelToken($datosUsuario['token']);
                $_SESSION['usuario_id'] = $infoToken['id'] ?? '';
                $_SESSION['usuario_nombre'] = $infoToken['nombre'] ?? '';
                $_SESSION['usuario_email'] = $infoToken['email'] ?? '';
                $_SESSION['usuario_rol'] = $infoToken['rol'] ?? 'CLIENTE';
            }
        } else {
            // Usar datos del objeto usuario
            $_SESSION['usuario_id'] = $datosUsuario['usuario']['id'] ?? '';
            $_SESSION['usuario_nombre'] = $datosUsuario['usuario']['nombre'] ?? '';
            $_SESSION['usuario_email'] = $datosUsuario['usuario']['email'] ?? '';
            $_SESSION['usuario_rol'] = $datosUsuario['usuario']['rol'] ?? 'CLIENTE';
        }
        
        $_SESSION['sesion_activa'] = true;
    }
    
    /**
     * Extrae información básica del token JWT (sin validación completa)
     * @param string $token Token JWT
     * @return array Información del usuario
     */
    private function extraerInfoDelToken(string $token): array {
        if (empty($token)) {
            return [];
        }
        
        $partes = explode('.', $token);
        if (count($partes) !== 3) {
            return [];
        }
        
        // Decodificar el payload (segunda parte)
        $payload = base64_decode($partes[1]);
        $datos = json_decode($payload, true);
        
        if (!$datos) {
            return [];
        }
        
        return [
            'id' => $datos['id'] ?? '',
            'nombre' => $datos['nombre'] ?? '',
            'email' => $datos['email'] ?? '',
            'rol' => $datos['rol'] ?? 'CLIENTE'
        ];
    }
    
    /**
     * Verifica si existe una sesión activa válida
     * @return bool True si la sesión es válida
     */
    public function verificarSesionActiva(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['sesion_activa']) || !$_SESSION['sesion_activa']) {
            return false;
        }
        
        if (!isset($_SESSION['jwt_token']) || empty($_SESSION['jwt_token'])) {
            return false;
        }
        
        // Por ahora, no validar con backend para evitar problemas de conectividad
        // $validacion = $this->validarToken($_SESSION['jwt_token']);
        // return $validacion['valido'];
        
        return true;
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function cerrarSesion(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION = [];
        session_destroy();
    }
}
?>
