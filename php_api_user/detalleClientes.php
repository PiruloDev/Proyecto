<?php
require_once __DIR__ . '/config.php';

class DetallesUsusarios {
    public function getDetalleEndpoint(string $tipo): string {
        return match ($tipo) {
            'cliente' => endpointDetalles::API_DETALLE_CLIENTE,
            default => throw new InvalidArgumentException("Tipo desconocido: $tipo"),
        };
    }
    
    public function obtenerDatos(string $tipo): array {
        $url = $this->getDetalleEndpoint($tipo);
    
        $proceso = curl_init();
        curl_setopt($proceso, CURLOPT_URL, $url);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_TIMEOUT, 30);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        curl_close($proceso);
        
        $dato = json_decode($response, true);

        return ['http_code' => $http_code,'data' => $dato];
    }
    public function mostrarDatos(string $tipo): void {
        echo "\n=== DATOS DE CLIENTES ===\n";
        echo "Endpoint: " . $this->getDetalleEndpoint($tipo) . "\n";
        
        $resultado = $this->obtenerDatos($tipo);
        
        echo "HTTP Code: " . $resultado['http_code'] . "\n";
        echo "Datos obtenidos:\n";
        echo json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        echo "\n";
        echo "El conteo total de clientes es: " . count($resultado['data']) . "\n";
    }
}
$detalles = new DetallesUsusarios();
$detalles->mostrarDatos('cliente');
?>