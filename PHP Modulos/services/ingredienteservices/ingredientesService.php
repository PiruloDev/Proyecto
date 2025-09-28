<?php

// Asume que el archivo de configuración se llama 'EndpointConfig.php'
// y que contiene las clases endpointBase, endpointGet, endpointPost, etc.
require_once __DIR__ . '/../../config/configIngredientes.php';

class ingredientesService {
    
    // Las URLs ya NO se definen como propiedades privadas, 
    // ya que se obtendrán directamente del archivo de configuración.
    
    /**
     * Obtiene una lista de todos los ingredientes.
     * Utiliza la configuración: endpointGet::API_GET_INGREDIENTES_LISTA
     */
    public function obtenerIngredientes() {
        // Usamos la constante del archivo de configuración
        $apiUrl = endpointGet::API_GET_INGREDIENTES_LISTA;

        $proceso = curl_init($apiUrl);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

        if (curl_errno($proceso)) {
            curl_close($proceso);
            return false;
        }
        curl_close($proceso);

        return ($http_code === 200) ? json_decode($respuesta, true) : false;
    }

    /**
     * Agrega un nuevo ingrediente (POST).
     * Utiliza la configuración: endpointPost::API_CREAR_INGREDIENTE
     */
    public function agregarIngredientes(
        $idProveedor,           
        $idCategoria,           
        $nombreIngrediente,     
        $cantidadIngrediente,   
        $fechaVencimiento,      
        $referenciaIngrediente, 
        $fechaEntregaIngrediente 
    ) {
        // Usamos la constante del archivo de configuración
        $apiUrl = endpointPost::API_CREAR_INGREDIENTE;

        $data_json = json_encode([
            "idProveedor"           => $idProveedor,           
            "idCategoria"           => $idCategoria,           
            "nombreIngrediente"     => $nombreIngrediente,
            "cantidadIngrediente"   => $cantidadIngrediente,
            "fechaVencimiento"      => $fechaVencimiento,
            "referenciaIngrediente" => $referenciaIngrediente,
            "fechaEntregaIngrediente" => $fechaEntregaIngrediente
        ], JSON_UNESCAPED_UNICODE); 
        
        $proceso = curl_init($apiUrl);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        
        $respuesta = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        
        if (curl_errno($proceso)) {
            $error = curl_error($proceso);
            curl_close($proceso);
            return ["success" => false, "error" => $error];
        }
        
        curl_close($proceso);
        
        if ($http_code === 200 || $http_code === 201) {
            return ["success" => true, "response" => $respuesta];
        } else {
            return ["success" => false, "error" => "HTTP $http_code", "response" => $respuesta];
        }
    }
    
    /**
     * Método para actualizar ingrediente completo (PUT).
     * Utiliza la configuración: endpointPut::ingrediente($id)
     */
    public function actualizarIngrediente(
        $id,
        $idProveedor,
        $idCategoria,
        $nombreIngrediente,
        $cantidadIngrediente,
        $fechaVencimiento,
        $referenciaIngrediente,
        $fechaEntregaIngrediente
    ) {
        // Usamos la función estática para construir la URL con el ID
        $apiUrl = endpointPut::ingrediente($id); 

        $data_json = json_encode([
            "idProveedor"           => $idProveedor,
            "idCategoria"           => $idCategoria,
            "nombreIngrediente"     => $nombreIngrediente,
            "cantidadIngrediente"   => $cantidadIngrediente,
            "fechaVencimiento"      => $fechaVencimiento,
            "referenciaIngrediente" => $referenciaIngrediente,
            "fechaEntregaIngrediente" => $fechaEntregaIngrediente
        ], JSON_UNESCAPED_UNICODE);
        
        $proceso = curl_init($apiUrl);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        
        $respuesta = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        
        if (curl_errno($proceso)) {
            $error = curl_error($proceso);
            curl_close($proceso);
            return ["success" => false, "error" => $error];
        }
        
        curl_close($proceso);
        
        return [
            "success" => ($http_code === 200),
            "response" => $respuesta,
            "http_code" => $http_code
        ];
    }
    
    /**
     * Método para actualizar solo la cantidad (PATCH).
     * Utiliza la configuración: endpointPatch::cantidadIngrediente($id)
     */
    public function actualizarCantidad($id, $cantidadIngrediente) {
        // Usamos la función estática para construir la URL con el ID
        $apiUrl = endpointPatch::cantidadIngrediente($id);
        
        $data_json = json_encode([
            "cantidadIngrediente" => $cantidadIngrediente
        ], JSON_UNESCAPED_UNICODE);
        
        $proceso = curl_init($apiUrl);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        
        $respuesta = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        
        if (curl_errno($proceso)) {
            $error = curl_error($proceso);
            curl_close($proceso);
            return ["success" => false, "error" => $error];
        }
        
        curl_close($proceso);
        
        return [
            "success" => ($http_code === 200),
            "response" => $respuesta,
            "http_code" => $http_code
        ];
    }
    
    /**
     * Método para eliminar ingrediente (DELETE).
     * Utiliza la configuración: endpointDelete::ingrediente($id)
     */
    public function eliminarIngrediente($id) {
        // Usamos la función estática para construir la URL con el ID
        $apiUrl = endpointDelete::ingrediente($id);

        $proceso = curl_init($apiUrl);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        
        $respuesta = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        
        if (curl_errno($proceso)) {
            $error = curl_error($proceso);
            curl_close($proceso);
            return ["success" => false, "error" => $error];
        }
        
        curl_close($proceso);
        
        return [
            "success" => ($http_code === 200),
            "response" => $respuesta,
            "http_code" => $http_code
        ];
    }
}