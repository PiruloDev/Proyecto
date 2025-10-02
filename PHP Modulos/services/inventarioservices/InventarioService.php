<?php
// services/inventarioservices/InventarioService.php

// Asegúrate de que esta ruta sea correcta para tu estructura de directorios
require_once __DIR__ . '/../../config/configInventario.php'; 

/**
 * Clase que actúa como cliente de la API para el módulo de Inventario y Producción.
 * Centraliza las llamadas a la API de Spring Boot mediante cURL.
 */
class InventarioService {
    private $config;

    public function __construct() {
        // Cargar configuración de rutas y URLs de la API
        $this->config = require __DIR__ . '/../../config/configInventario.php';
    }

    /**
     * Llamada genérica a la API con cURL (Maneja GET, POST, PUT, PATCH, DELETE).
     * @param string $method Método HTTP (GET, POST, PUT, DELETE, PATCH).
     * @param string $endpoint Ruta específica de la API (ej: /produccion, /produccion/1).
     * @param array|null $data Datos a enviar en el cuerpo (para POST/PUT/PATCH).
     * @return array Array con 'status' (código HTTP) y 'body' (respuesta decodificada).
     */
    private function callApi($method, $endpoint, $data = null) {
        // Construye la URL completa
        $baseUrl = $this->config['inventario']['base_url'];
        $url = $baseUrl . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Tiempo de espera de 10 segundos

        // Configuración para métodos que envían cuerpo (POST, PUT, PATCH)
        if (in_array($method, ['POST', 'PUT', 'PATCH']) && $data !== null) {
            $requestBody = json_encode($data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($requestBody)
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        }
        
        $response = curl_exec($ch);

        // Manejo de errores de conexión (ej: API apagada)
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return [
                'status' => 503, // Service Unavailable
                'body'   => ['error' => "Error de conexión con API: {$error_msg}. Verifique que Spring Boot esté activo."]
            ];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true);

        // Devuelve la respuesta, incluyendo el cuerpo si la decodificación falló
        return [
            'status' => $httpCode,
            'body'   => $decoded ?: ['error' => "Respuesta de API inválida o vacía.", 'raw' => $response]
        ];
    }

    // ===============================================
    // OPERACIONES DE PRODUCCIÓN (CRUD)
    // ===============================================

    /**
     * 🔹 GET: Obtener todo el historial de producción (Listar).
     */
    public function obtenerHistorial() {
        $endpoint = $this->config['inventario']['produccion']['registrar']; 
        return $this->callApi('GET', $endpoint);
    }
    
    /**
     * 🔹 GET: Obtener la receta de un producto específico.
     */
    public function obtenerReceta($idProducto) {
        $endpoint = $this->config['inventario']['produccion']['receta'] . '/' . $idProducto;
        return $this->callApi('GET', $endpoint);
    }

    /**
     * 🔹 POST: Registrar una nueva producción.
     */
    public function registrarProduccion($idProducto, $cantidad, $ingredientes = []) {
        $endpoint = $this->config['inventario']['produccion']['registrar'];

        $data = [
            "idProducto" => (int)$idProducto,
            "cantidadProducida" => (int)$cantidad,
            "ingredientesDescontados" => $ingredientes // array vacío o completo según necesidad
        ];

        return $this->callApi('POST', $endpoint, $data);
    }

    /**
     * 🔹 PUT: Actualizar completamente un registro de producción.
     */
    public function actualizarProduccion($id, $data) {
        $endpoint = $this->config['inventario']['produccion']['registrar'] . '/' . $id;
        return $this->callApi('PUT', $endpoint, $data);
    }

    /**
     * 🔹 PATCH: Actualizar parcialmente un registro de producción.
     */
    public function actualizarParcial($id, $data) {
        $endpoint = $this->config['inventario']['produccion']['registrar'] . '/' . $id;
        return $this->callApi('PATCH', $endpoint, $data);
    }

    /**
     * 🔹 DELETE: Eliminar un registro de producción.
     */
    public function eliminarProduccion($id) {
        $endpoint = $this->config['inventario']['produccion']['registrar'] . '/' . $id;
        return $this->callApi('DELETE', $endpoint);
    }
}
?>
