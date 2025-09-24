<?php
class ingredientesService {
    
    // URLs corregidas según los endpoints de tu controller
    private $apiUrlGet  = "http://localhost:8080/ingredientes/lista";
    private $apiUrlPost = "http://localhost:8080/crearingrediente"; // Corregido
    private $apiUrlPut  = "http://localhost:8080/ingrediente/"; // Para actualizaciones
    private $apiUrlPatch = "http://localhost:8080/"; // Para actualizar cantidad
    private $apiUrlDelete = "http://localhost:8080/ingrediente/"; // Para eliminar
    
    public function obtenerIngredientes() {
    $proceso = curl_init($this->apiUrlGet);
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

    public function agregarIngredientes(
        $idProveedor,           // Corregido: sin guión bajo
        $idCategoria,           // Agregado: faltaba este campo
        $nombreIngrediente,     // Corregido: sin guión bajo
        $cantidadIngrediente,   // Corregido: sin guión bajo
        $fechaVencimiento,      // Corregido: sin guión bajo
        $referenciaIngrediente, // Corregido: nombre completo
        $fechaEntregaIngrediente // Corregido: nombre completo y sin coma final
    ) {
        $data_json = json_encode([
            "idProveedor"     => $idProveedor,           // Nombres corregidos
            "idCategoria"     => $idCategoria,           // Agregado
            "nombreIngrediente" => $nombreIngrediente,
            "cantidadIngrediente" => $cantidadIngrediente,
            "fechaVencimiento" => $fechaVencimiento,
            "referenciaIngrediente" => $referenciaIngrediente,
            "fechaEntregaIngrediente" => $fechaEntregaIngrediente
        ], JSON_UNESCAPED_UNICODE); // Corregido: sin flags:
        
        $proceso = curl_init($this->apiUrlPost);
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
    
    // Método adicional para actualizar ingrediente completo (PUT)
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
        $data_json = json_encode([
            "idProveedor"     => $idProveedor,
            "idCategoria"     => $idCategoria,
            "nombreIngrediente" => $nombreIngrediente,
            "cantidadIngrediente" => $cantidadIngrediente,
            "fechaVencimiento" => $fechaVencimiento,
            "referenciaIngrediente" => $referenciaIngrediente,
            "fechaEntregaIngrediente" => $fechaEntregaIngrediente
        ], JSON_UNESCAPED_UNICODE);
        
        $proceso = curl_init($this->apiUrlPut . $id);
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
    
    // Método para actualizar solo la cantidad (PATCH)
    public function actualizarCantidad($id, $cantidadIngrediente) {
        $data_json = json_encode([
            "cantidadIngrediente" => $cantidadIngrediente
        ], JSON_UNESCAPED_UNICODE);
        
        $proceso = curl_init($this->apiUrlPatch . $id . "/cantidad");
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
    
    // Método para eliminar ingrediente (DELETE)
    public function eliminarIngrediente($id) {
        $proceso = curl_init($this->apiUrlDelete . $id);
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
?>