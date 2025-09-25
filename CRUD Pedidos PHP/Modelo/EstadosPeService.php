<?php

class EstadosPeService {

    private $apiUrl = "http://localhost:8080/estadosPedidos";

    /**
     * Método privado para ejecutar cualquier petición cURL (GET, POST, PUT, DELETE).
     * @param string $url La URL completa para la petición.
     * @param string $method El método HTTP (GET, POST, PUT, DELETE).
     * @param array $data Los datos a enviar en la petición (para POST/PUT).
     * @return array Retorna un array asociativo con el resultado de la petición.
     */
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

    /**
     * Obtiene todos los estados de pedido.
     * @return array
     */
    public function obtenerEstadosPedidos(): array {
        $resultado = $this->executeCurlRequest($this->apiUrl, "GET");

        if (!$resultado['success']) {
            return [];
        }

        return $resultado['data'] ?? [];
    }

    /**
     * Agrega un nuevo estado de pedido.
     * @param string $nombre_estado
     * @return array
     */
    public function agregarEstadoPedido(string $nombre_estado): array {
        $datos_post = ["nombre_ESTADO" => $nombre_estado];
        return $this->executeCurlRequest($this->apiUrl, "POST", $datos_post);
    }
    
    /**
     * Actualiza un estado de pedido existente.
     * @param int $id
     * @param string $nombre_estado
     * @return array
     */
    public function actualizarEstadoPedido(int $id, string $nombre_estado): array {
        $url = $this->apiUrl . '/' . $id;
        $datos_put = ["nombre_ESTADO" => $nombre_estado];
        return $this->executeCurlRequest($url, "PUT", $datos_put);
    }

    /**
     * Elimina un estado de pedido por su ID.
     * @param int $id
     * @return array
     */
    public function eliminarEstadoPedido(int $id): array {
        $url = $this->apiUrl . '/' . $id;
        return $this->executeCurlRequest($url, "DELETE");
    }
}