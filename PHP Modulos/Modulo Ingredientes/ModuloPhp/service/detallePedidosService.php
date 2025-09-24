<?php
class DetallePedidosService {
    private $apiUrlBase = "http://localhost:8080/detalles-pedidos";

    // GET - Obtener todos los detalles
    public function obtenerDetalles() {
        $respuesta = @file_get_contents($this->apiUrlBase);
        if ($respuesta === false) return [];
        return json_decode($respuesta, true);
    }

    // POST - Crear nuevo detalle
    public function agregarDetalle($idPedido, $idProducto, $cantidadProducto, $precioUnitario, $subtotal) {
        $data_json = json_encode([
            "idPedido" => $idPedido,
            "idProducto" => $idProducto,
            "cantidadProducto" => $cantidadProducto,
            "precioUnitario" => $precioUnitario,
            "subtotal" => $subtotal
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($this->apiUrlBase);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $respuesta = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ["success" => ($http_code === 200 || $http_code === 201), "response" => $respuesta];
    }

    // PUT - Actualizar detalle
    public function actualizarDetalle($id, $idPedido, $idProducto, $cantidadProducto, $precioUnitario, $subtotal) {
        $data_json = json_encode([
            "idPedido" => $idPedido,
            "idProducto" => $idProducto,
            "cantidadProducto" => $cantidadProducto,
            "precioUnitario" => $precioUnitario,
            "subtotal" => $subtotal
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($this->apiUrlBase . "/" . $id);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $respuesta = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ["success" => ($http_code === 200), "response" => $respuesta];
    }

    // DELETE - Eliminar detalle
    public function eliminarDetalle($id) {
        $ch = curl_init($this->apiUrlBase . "/" . $id);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ["success" => ($http_code === 200), "response" => $respuesta];
    }
}
?>
