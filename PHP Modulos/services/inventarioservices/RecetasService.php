<?php
// C:\xampp\htdocs\REbase\PHP Modulos\services\inventarioservices\RecetasService.php

// Asegúrate de que esta ruta sea correcta para tu configRecetas.php
require_once __DIR__ . '/../../../config/configRecetas.php'; 

class RecetasService {

    /**
     * Función utilitaria para realizar llamadas REST usando cURL.
     */
    private function callApi(string $url, string $method = 'GET', array $data = null): array {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        if ($data !== null) {
            $json_data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => "Error de conexión con el backend: " . $error];
        }

        $responseData = json_decode($response, true);
        
        // Manejo de errores HTTP 4xx/5xx (que el backend reporta como JSON)
        if ($http_code >= 400) {
            // El backend Java devuelve 'error' o 'mensaje' en caso de fallo
            $errorMessage = $responseData['error'] ?? $responseData['mensaje'] ?? 'Error desconocido del servidor.';
            return ['success' => false, 'error' => "API Error ({$http_code}): " . $errorMessage];
        }
        
        // Éxito
        return ['success' => true, 'response' => $responseData, 'http_code' => $http_code];
    }

    // --- Métodos CRUD para el Controlador ---

    public function listarRecetas(): array {
        return $this->callApi(endpointGetRecetas::API_GET_RECETAS_LISTA, 'GET');
    }

    public function crearReceta(array $data): array {
        return $this->callApi(endpointPostRecetas::API_CREAR_RECETA, 'POST', $data);
    }

    public function eliminarReceta(int $idProducto): array {
        $url = endpointDeleteRecetas::receta($idProducto);
        return $this->callApi($url, 'DELETE');
    }

    // Nota: El método actualizarReceta se mantiene aquí si lo necesitas en el futuro
    public function actualizarReceta(int $idProducto, array $data): array {
        $url = endpointPutRecetas::receta($idProducto);
        return $this->callApi($url, 'PUT', $data);
    }
}