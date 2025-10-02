<?php

require_once __DIR__ . '/../../config/configPedidos.php';

class PedidosService {

    private $apiUrl;

    public function __construct() {
        $this->apiUrl = API_BASE_URL . ENDPOINT_PEDIDOS;
    }

    private function executeCurlRequest(string $url, string $method = "GET", array $data = []): array {
        $ch = curl_init($url);
        $data_json = '';

        if (!empty($data) && in_array($method, ["POST", "PUT"])) {
            $data_json = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_json)
            ]);
        }
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ["success" => false, "error" => "cURL Error: " . $error, "data" => null];
        }

        curl_close($ch);
        
        $responseData = ($http_code !== 204 && $response) ? json_decode($response, true) : null;

        if (in_array($http_code, [200, 201, 204])) {
            return ["success" => true, "data" => $responseData, "error" => null];
        } else {
   
            $apiError = $responseData['message'] ?? 'Unknown API error';
            return ["success" => false, "error" => "HTTP {$http_code}: {$apiError}", "data" => $responseData];
        }
    }

    // --- NUEVO MÉTODO PARA RECUPERAR UN SOLO PEDIDO ---
    public function obtenerPedidoPorId(int $id): ?array {
        $url = $this->apiUrl . '/' . $id;
        $resultado = $this->executeCurlRequest($url, "GET");

        if (!$resultado['success'] || !isset($resultado['data']) || empty($resultado['data'])) {
            return null;
        }

        // Asumiendo que la API devuelve un objeto/array de pedido directamente.
        return $resultado['data']; 
    }
    // ---------------------------------------------------

    public function obtenerPedidos(): array {
        $resultado = $this->executeCurlRequest($this->apiUrl, "GET");

        if (!$resultado['success']) {
            return [];
        }

        return $resultado['data'] ?? [];
    }

    public function agregarPedido(array $datos_pedido): array {
        return $this->executeCurlRequest($this->apiUrl, "POST", $datos_pedido);
    }

    public function actualizarPedido(int $id, array $datos_pedido): array {
        $url = $this->apiUrl . '/' . $id;
        return $this->executeCurlRequest($url, "PUT", $datos_pedido);
    }
    
    public function eliminarPedido(int $id): array {
        $url = $this->apiUrl . '/' . $id;
        return $this->executeCurlRequest($url, "DELETE");
    }
}