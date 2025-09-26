<?php
require_once __DIR__ . '/../../config/configUser.php';

class DetallesEmpleados {
    public function getDetalleEndpoint(string $tipo): string {
        return match ($tipo) {
            'empleado', 'empleados' => endpointDetalles::API_DETALLE_EMPLEADO,
            default => throw new InvalidArgumentException("Tipo desconocido: $tipo"),
        };
    }
    public function obtenerDatos(string $tipo): array {
        $url = $this->getDetalleEndpoint($tipo);
        
        // Agregar timestamp para evitar cache
        $url .= '?t=' . time();
    
        $proceso = curl_init();
        curl_setopt($proceso, CURLOPT_URL, $url);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_TIMEOUT, 30);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        ]);
        
        $response = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        curl_close($proceso);
        
        $dato = json_decode($response, true);

        return ['http_code' => $http_code,'data' => $dato];
    }
}
$detalles = new DetallesEmpleados();

?>