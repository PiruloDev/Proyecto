<?php
// Asegúrate de que esta ruta sea correcta para incluir tu archivo de configuración
require_once __DIR__ . '../../../config/configIngredientes.php'; 

class DetallePedidosService {
    
    // La propiedad $apiUrlBase ya no es necesaria, se usará la configuración.

    /**
     * GET - Obtener todos los detalles de pedidos.
     */
    public function obtenerDetalles() {
        // Usa la constante GET del config
        $url = endpointGet::API_GET_DETALLES_PEDIDOS;

        $respuesta = @file_get_contents($url);
        if ($respuesta === false) return [];
        return json_decode($respuesta, true);
    }

    /**
     * POST 
     */
    public function agregarDetalle($idPedido, $idProducto, $cantidadProducto, $precioUnitario, $subtotal) {
        // Usa la constante POST del config
        $url = endpointPost::API_CREAR_DETALLE_PEDIDO;

        $data_json = json_encode([
            "idPedido" => $idPedido,
            "idProducto" => $idProducto,
            "cantidadProducto" => $cantidadProducto,
            "precioUnitario" => $precioUnitario,
            "subtotal" => $subtotal
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $respuesta = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ["success" => ($http_code === 200 || $http_code === 201), "response" => $respuesta];
    }

    /**
     * PUT 
     */
    public function actualizarDetalle($id, $idPedido, $idProducto, $cantidadProducto, $precioUnitario, $subtotal) {
        // Usa el método estático PUT del config para construir la URL con el ID
        $url = endpointPut::detallePedido($id); 

        $data_json = json_encode([
            "idPedido" => $idPedido,
            "idProducto" => $idProducto,
            "cantidadProducto" => $cantidadProducto,
            "precioUnitario" => $precioUnitario,
            "subtotal" => $subtotal
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $respuesta = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ["success" => ($http_code === 200), "response" => $respuesta];
    }

    /**
     * DELETE 
     */
    public function eliminarDetalle($id) {
        // Usa el método estático DELETE del config para construir la URL con el ID
        $url = endpointDelete::detallePedido($id);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ["success" => ($http_code === 200), "response" => $respuesta];
    }
}
?>