<?php 
require_once __DIR__ . '/../../config/configUser.php';

class EliminarEmpleadoService {
    public function getEliminacionEndpoint(string $tipo, int $id): string {
        return match ($tipo) {
            'empleado' => endpointEliminacion::empleado($id),
            default => throw new InvalidArgumentException("Tipo desconocido: $tipo"),
        };
    }
    
    public function eliminarEmpleado(int $id_empleado): array {
        $url = $this->getEliminacionEndpoint('empleado', $id_empleado);
        
        // No enviamos cuerpo JSON ya que el ID va en la URL como PathVariable
        $proceso = curl_init($url);
        curl_setopt_array($proceso, [
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json"
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
        $is_success = ($http_code === 200) && (trim($response) === "Empleado eliminado exitosamente");
        
        return [
            'success' => $is_success,
            'http_code' => $http_code,
            'data' => trim($response),
            'error' => !$is_success ? "HTTP Error: $http_code - $response" : null
        ];
    }
}
?>