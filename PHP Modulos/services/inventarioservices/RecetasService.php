    <?php
    // services/inventarioservices/RecetasService.php

    // Inclusión del archivo de configuración (2 niveles arriba para llegar a 'config/')
    require_once __DIR__ . '/../../config/configRecetas.php'; 

    class RecetasService {
        
        // Función centralizada de utilidades para realizar peticiones CURL a la API Java
        private function realizarPeticionApi(string $url, string $method, array $data = null) {
            $ch = curl_init($url);
            
            // Configuración básica
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                // Añade aquí headers de Autorización si son necesarios
            ]);

            if ($data) {
                $data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            }
            
            $respuesta = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return ["success" => false, "error" => "Error de conexión cURL: " . $error];
            }
            
            $decoded_response = json_decode($respuesta, true);

            // Los códigos 2xx son éxito (200 OK, 201 Created, 204 No Content)
            if ($http_code >= 200 && $http_code < 300) {
                return ["success" => true, "response" => $decoded_response ?: $respuesta, "http_code" => $http_code];
            } else {
                // Error de API (4xx, 5xx)
                $error_msg = $decoded_response['error'] ?? $decoded_response['message'] ?? "Error HTTP $http_code en la API Java.";
                return ["success" => false, "error" => $error_msg, "http_code" => $http_code];
            }
        }

        /**
         * Obtiene todas las recetas (GET).
         */
        public function listarRecetas() {
            // Uso del config: endpointGetRecetas
            $apiUrl = endpointGetRecetas::API_GET_RECETAS_LISTA;
            $resultado = $this->realizarPeticionApi($apiUrl, "GET");
            
            return $resultado; // Retorna array con success, response, o error
        }
        
        /**
         * Obtiene una receta específica por ID de producto (GET /{id}).
         */
        public function obtenerRecetaPorProducto(int $id) {
            // Uso del config: endpointGetRecetas::recetaPorProducto(id)
            $apiUrl = endpointGetRecetas::recetaPorProducto($id);
            $resultado = $this->realizarPeticionApi($apiUrl, "GET");
            
            return $resultado;
        }

        /**
         * Crea una nueva receta (POST).
         */
        public function crearReceta(array $recetaData) {
            // Uso del config: endpointPostRecetas
            $apiUrl = endpointPostRecetas::API_CREAR_RECETA;
            return $this->realizarPeticionApi($apiUrl, "POST", $recetaData);
        }

        /**
         * Actualiza (reemplaza) una receta (PUT /{id}).
         */
        public function actualizarReceta(int $idProducto, array $recetaData) {
            // Uso del config: endpointPutRecetas::receta(id)
            $apiUrl = endpointPutRecetas::receta($idProducto); 
            return $this->realizarPeticionApi($apiUrl, "PUT", $recetaData);
        }
        
        /**
         * Elimina una receta (DELETE /{id}).
         */
        public function eliminarReceta(int $idProducto) {
            // Uso del config: endpointDeleteRecetas::receta(id)
            $apiUrl = endpointDeleteRecetas::receta($idProducto);
            // Petición DELETE sin cuerpo (body)
            return $this->realizarPeticionApi($apiUrl, "DELETE");
        }
    }