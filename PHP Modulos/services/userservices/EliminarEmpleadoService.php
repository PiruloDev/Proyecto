<?php 
require_once __DIR__ . '/../configuration/config.php';

class EliminarEmpleadoService {
    public function getEliminacionEndpoint(string $tipo, int $id): string {
        return match ($tipo) {
            'empleado' => endpointEliminacion::empleado($id),
            default => throw new InvalidArgumentException("Tipo desconocido: $tipo"),
        };
    }
    
    public function eliminarEmpleado(int $id_empleado): array {
        $url = $this->getEliminacionEndpoint('empleado', $id_empleado);
        
        $datos_eliminar = [
            "id" => $id_empleado
        ];
        $data_json = json_encode($datos_eliminar);

        $proceso = curl_init($url);
        curl_setopt_array($proceso, [
            CURLOPT_CUSTOMREQUEST => "DELETE",
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
        
        return [
            'success' => ($http_code === 200 || $http_code === 204),
            'http_code' => $http_code,
            'data' => $data,
            'error' => !($http_code === 200 || $http_code === 204) ? "HTTP Error: $http_code - $response" : null,
        ];
    }
}
?>