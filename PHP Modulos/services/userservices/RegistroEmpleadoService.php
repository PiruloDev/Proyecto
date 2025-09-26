<?php
require_once __DIR__ . '/../../config/configUser.php';

class RegistroEmpleadoService { 
    public function getCreacionEndpoint(string $tipo): string {
        return match ($tipo) {
            'empleado' => endpointCreacion::API_CREAR_EMPLEADO,
            default => throw new InvalidArgumentException("Tipo desconocido: $tipo"),
        };
    }
    
    public function registrarEmpleado(array $datosEmpleado): array {
        $url = $this->getCreacionEndpoint('empleado');
        
        $data_json = json_encode($datosEmpleado);
        
        $proceso = curl_init($url);
        curl_setopt_array($proceso, [
            CURLOPT_CUSTOMREQUEST => "POST",
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
      
        // La API devuelve texto plano, no JSON
        $is_success = ($http_code === 200 || $http_code === 201) && 
                     (trim($response) === "Nuevo Empleado creado exitosamente");
        
        return [
            'success' => $is_success,
            'http_code' => $http_code,
            'data' => trim($response),
            'error' => !$is_success ? "HTTP Error: $http_code - $response" : null,
        ];
    }
}
?>