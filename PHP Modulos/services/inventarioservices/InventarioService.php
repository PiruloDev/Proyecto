<?php
// services/inventarioservices/InventarioService.php

class InventarioService {
    private $config;

    public function __construct() {
        // Cargar configuración de rutas
        $this->config = require __DIR__ . '/../../config/configInventario.php';
    }

    /**
     * Llamada genérica a la API con cURL
     */
    private function callApi($method, $endpoint, $data = null) {
        $url = $this->config['inventario']['base_url'] . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return [
                'status' => 500,
                'body'   => ['error' => "Error de conexión con API: $error_msg"]
            ];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true);

        return [
            'status' => $httpCode,
            'body'   => $decoded ?: [
                'error' => "Respuesta inválida de la API",
                'raw'   => $response
            ]
        ];
    }

    /**
     * 🔹 GET: obtener receta de un producto
     */
    public function obtenerReceta($idProducto) {
        $endpoint = $this->config['inventario']['produccion']['receta'] . '/' . $idProducto;
        return $this->callApi('GET', $endpoint);
    }

    /**
     * 🔹 POST: registrar producción
     * Siempre enviamos ingredientesDescontados (aunque sea vacío)
     */
    public function registrarProduccion($idProducto, $cantidad, $ingredientes = []) {
        $endpoint = $this->config['inventario']['produccion']['registrar'];

        $data = [
            "idProducto" => (int)$idProducto,
            "cantidadProducida" => (int)$cantidad,
            "ingredientesDescontados" => $ingredientes // array vacío si no hay
        ];

        return $this->callApi('POST', $endpoint, $data);
    }

    public function actualizarProduccion($id, $data) {
    $endpoint = $this->config['inventario']['produccion']['registrar'] . '/' . $id;
    return $this->callApi('PUT', $endpoint, $data);
}

public function actualizarParcial($id, $data) {
    $endpoint = $this->config['inventario']['produccion']['registrar'] . '/' . $id;
    return $this->callApi('PATCH', $endpoint, $data);
}

public function eliminarProduccion($id) {
    $endpoint = $this->config['inventario']['produccion']['registrar'] . '/' . $id;
    return $this->callApi('DELETE', $endpoint);
}


    /**
     * 🔹 Método de prueba rápida
     */
    public function test() {
        return "✅ InventarioService cargado correctamente";
    }
}
