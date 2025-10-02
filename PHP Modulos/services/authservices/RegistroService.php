<?php
require_once __DIR__ . '/../../config/configAuth.php';

/**
 * Servicio de registro de clientes
 * Maneja la comunicación con el backend para registro de nuevos clientes
 */
class RegistroService {
    
    /**
     * Registra un nuevo cliente en el backend
     * @param array $datosCliente Datos del cliente (nombre, email, telefono, contrasena)
     * @return array Resultado del registro
     */
    public function registrarCliente(array $datosCliente): array {
        $url = 'http://localhost:8080/auth/registro/cliente';
        
        // Validar que los datos requeridos estén presentes
        $camposRequeridos = ['nombre', 'email', 'telefono', 'contrasena'];
        foreach ($camposRequeridos as $campo) {
            if (empty($datosCliente[$campo])) {
                return [
                    'http_code' => 400,
                    'data' => ['mensaje' => "El campo $campo es requerido"],
                    'success' => false
                ];
            }
        }
        
        $proceso = curl_init();
        curl_setopt($proceso, CURLOPT_URL, $url);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_POST, true);
        curl_setopt($proceso, CURLOPT_POSTFIELDS, json_encode($datosCliente));
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
            'success' => $http_code === 200 || $http_code === 201
        ];
    }
    
    /**
     * Valida el formato del email
     * @param string $email Email a validar
     * @return bool True si el email es válido
     */
    public function validarEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida el formato del teléfono
     * @param string $telefono Teléfono a validar
     * @return bool True si el teléfono es válido
     */
    public function validarTelefono(string $telefono): bool {
        // Permite números, espacios, guiones y paréntesis
        return preg_match('/^[\d\s\-\(\)]+$/', $telefono) && strlen($telefono) >= 7;
    }
    
    /**
     * Valida la fortaleza de la contraseña
     * @param string $contrasena Contraseña a validar
     * @return bool True si la contraseña es válida
     */
    public function validarContrasena(string $contrasena): bool {
        // Mínimo 6 caracteres
        return strlen($contrasena) >= 6;
    }
}
?>