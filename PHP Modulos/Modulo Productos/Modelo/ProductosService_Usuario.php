<?php
require_once __DIR__ . '/../Configuracion/Config.php';

class ProductosService_Usuario {
    private $apiUrl = endpointDetalles::API_DETALLE_PRODUCTO;

    // Obtener productos
    public function obtenerProductos() {
        $respuesta = file_get_contents($this->apiUrl);
        if ($respuesta === FALSE) return false;
        return json_decode($respuesta, true);
    }

    // Crear producto
    public function crearProducto($nombre, $precio, $stock, $marca, $fechaVencimiento) {
        $datos = [
            "nombreProducto" => $nombre,
            "precio" => (float)$precio,
            "stockMinimo" => (int)$stock,
            "marcaProducto" => $marca,
            "fechaVencimiento" => $fechaVencimiento
        ];

        $data_json = json_encode($datos);

        $proceso = curl_init(endpointCreacion::API_CREAR_PRODUCTO);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_json)
        ]);

        $respuesta = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

        curl_close($proceso);

        return $http_code === 200 || $http_code === 201;
    }
}
