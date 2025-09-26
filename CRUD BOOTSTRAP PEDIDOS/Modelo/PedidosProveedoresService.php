<?php

class PedidosProveedoresService {

    private $apiUrl = "http://localhost:8080/pedidosproveedores";

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
     * Obtiene todos los pedidos a proveedores.
     * @return array
     */
    public function obtenerPedidosProveedores(): array {
        $resultado = $this->executeCurlRequest($this->apiUrl, "GET");
        
        if (!$resultado['success']) {
            return [];
        }

        return $resultado['data'] ?? [];
    }

    /**
     * Agrega un nuevo pedido a proveedor.
     * @param int $id_proveedor
     * @param int $numero_pedido
     * @param string $fecha_pedido
     * @param string $estado_pedido
     * @return array
     */
    public function agregarPedidoProveedor(int $id_proveedor, int $numero_pedido, string $fecha_pedido, string $estado_pedido): array {
        $datos_post = [
            "id_PROVEEDOR"  => $id_proveedor,
            "numero_PEDIDO" => $numero_pedido,
            "fecha_PEDIDO"  => $fecha_pedido,
            "estado_PEDIDO" => $estado_pedido
        ];
        return $this->executeCurlRequest($this->apiUrl, "POST", $datos_post);
    }
    
    /**
     * Actualiza un pedido a proveedor existente.
     * @param int $id El ID del pedido a actualizar.
     * @param array $datos_pedido Un array con los datos del pedido.
     * @return array
     */
    public function actualizarPedidoProveedor(int $id, array $datos_pedido): array {
        $url = $this->apiUrl . '/' . $id;
        return $this->executeCurlRequest($url, "PUT", $datos_pedido);
    }

    /**
     * Elimina un pedido a proveedor por su ID.
     * @param int $id El ID del pedido a eliminar.
     * @return array
     */
    public function eliminarPedidoProveedor(int $id): array {
        $url = $this->apiUrl . '/' . $id;
        return $this->executeCurlRequest($url, "DELETE");
    }
}