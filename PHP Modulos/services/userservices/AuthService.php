<?php
require_once __DIR__ . '/../../config/configUser.php';

class AuthService {
    
    /**
     * Obtiene el endpoint de autenticación según el tipo de operación
     */
    public function getAuthEndpoint(string $operacion): string {
        return match ($operacion) {
            'login' => 'http://localhost:8080/auth/login',
            'registro' => 'http://localhost:8080/auth/registro/cliente',
            default => throw new InvalidArgumentException("Operación desconocida: $operacion"),
        };
    }
    
    /**
     * Realiza la autenticación del usuario enviando credenciales al backend
     */
    public function autenticarUsuario(array $credenciales): array {
        $url = $this->getAuthEndpoint('login');
        
        // Cambiar el formato de credenciales para que coincida con el backend
        $datosLogin = [
            'username' => $credenciales['email'], // El backend espera 'username'
            'password' => $credenciales['contrasena'] // El backend espera 'password'
        ];
        
        $data_json = json_encode($datosLogin);
        
        // Debug: Log de datos enviados
        error_log("AuthService - Datos enviados: " . $data_json);
        
        $proceso = curl_init($url);
        curl_setopt_array($proceso, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json",
                "Content-Length: " . strlen($data_json)
            ]
        ]);
        
        $response = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($proceso);
        curl_close($proceso);
        
        // Debug: Log de respuesta del login
        error_log("AuthService - HTTP Code: " . $http_code);
        error_log("AuthService - Response: " . $response);
        
        if ($response === false) {
            return [
                'success' => false,
                'http_code' => 0,
                'error' => "Error de conexión: $curl_error",
                'data' => null
            ];
        }
      
        $data = json_decode($response, true);
        
        // Procesar la respuesta del backend y extraer información del token JWT
        if ($http_code === 200 && isset($data['token'])) {
            // Decodificar el JWT para extraer la información del usuario
            $tokenData = $this->decodificarJWT($data['token']);
            
            // Debug: Log del token decodificado
            error_log("AuthService - Token decodificado: " . json_encode($tokenData));
            
            // Preparar los datos del usuario para la sesión
            $userData = [
                'token' => $data['token'],
                'mensaje' => $data['mensaje'] ?? 'Login exitoso',
                'rol' => $tokenData['rol'] ?? $tokenData['role'] ?? 'Cliente', // Probar ambos campos
                'id' => $tokenData['sub'] ?? $tokenData['id'] ?? $tokenData['userId'] ?? null,
                'nombre' => $tokenData['nombre'] ?? $tokenData['name'] ?? $credenciales['email']
            ];
            
            error_log("AuthService - Token data: " . json_encode($tokenData));
            error_log("AuthService - User data preparada: " . json_encode($userData));
            
            return [
                'success' => true,
                'http_code' => $http_code,
                'data' => $userData,
                'error' => null
            ];
        }
        
        error_log("AuthService - Login fallido. HTTP Code: $http_code, Data: " . json_encode($data));
        
        // Manejar diferentes tipos de error del backend
        $errorMessage = 'Credenciales inválidas';
        if (isset($data['mensaje'])) {
            $errorMessage = $data['mensaje'];
        } elseif (isset($data['message'])) {
            $errorMessage = $data['message'];
        } elseif (isset($data['error'])) {
            $errorMessage = $data['error'];
        }
        
        return [
            'success' => false,
            'http_code' => $http_code,
            'data' => $data,
            'error' => $errorMessage
        ];
    }
    
    /**
     * Registra un nuevo cliente enviando datos al backend
     */
    public function registrarCliente(array $datosCliente): array {
        $url = $this->getAuthEndpoint('registro');
        
        $data_json = json_encode($datosCliente);
        
        $proceso = curl_init($url);
        curl_setopt_array($proceso, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json",
                "Content-Length: " . strlen($data_json)
            ]
        ]);
        
        $response = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($proceso);
        curl_close($proceso);
        
        if ($response === false) {
            return [
                'success' => false,
                'http_code' => 0,
                'error' => "Error de conexión: $curl_error",
                'data' => null
            ];
        }
      
        $data = json_decode($response, true);
        
        // Verificar si el registro fue exitoso (HTTP 201 CREATED según los logs)
        if ($http_code === 201 || $http_code === 200) {
            return [
                'success' => true,
                'http_code' => $http_code,
                'data' => $data,
                'error' => null
            ];
        }
        
        // Manejar errores de registro
        $errorMessage = 'Error en el registro';
        if (isset($data['mensaje'])) {
            $errorMessage = $data['mensaje'];
        } elseif (isset($data['message'])) {
            $errorMessage = $data['message'];
        } elseif (isset($data['error'])) {
            $errorMessage = $data['error'];
        }
        
        return [
            'success' => false,
            'http_code' => $http_code,
            'data' => $data,
            'error' => $errorMessage
        ];
    }
    
    /**
     * Inicia una sesión de usuario almacenando el token JWT
     */
    public function iniciarSesion(array $datosUsuario): void {
        session_start();
        $_SESSION['auth_token'] = $datosUsuario['token'] ?? null;
        $_SESSION['user_role'] = $datosUsuario['rol'] ?? null;
        $_SESSION['user_id'] = $datosUsuario['id'] ?? null;
        $_SESSION['user_name'] = $datosUsuario['nombre'] ?? null;
        $_SESSION['authenticated'] = true;
    }
    
    /**
     * Verifica si el usuario está autenticado
     */
    public function estaAutenticado(): bool {
        session_start();
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }
    
    /**
     * Obtiene el rol del usuario autenticado
     */
    public function obtenerRolUsuario(): ?string {
        session_start();
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Obtiene el token del usuario autenticado
     */
    public function obtenerToken(): ?string {
        session_start();
        return $_SESSION['auth_token'] ?? null;
    }
    
    /**
     * Cierra la sesión del usuario destruyendo todos los datos de sesión
     */
    public function cerrarSesion(): void {
        session_start();
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    
    /**
     * Decodifica un token JWT para extraer la información del payload
     * Esta es una implementación básica que extrae datos sin verificar la firma
     */
    private function decodificarJWT(string $token): array {
        $partes = explode('.', $token);
        
        if (count($partes) !== 3) {
            return [];
        }
        
        // Decodificar el payload (segunda parte del JWT)
        $payload = $partes[1];
        
        // Agregar padding si es necesario
        $payload .= str_repeat('=', (4 - strlen($payload) % 4) % 4);
        
        // Decodificar base64url
        $payload = str_replace(['-', '_'], ['+', '/'], $payload);
        $decodificado = base64_decode($payload);
        
        if ($decodificado === false) {
            return [];
        }
        
        $datos = json_decode($decodificado, true);
        
        if (!is_array($datos)) {
            return [];
        }
        
        return $datos;
    }
}
?>