<?php
class ReportesVentasService {
    private $apiUrl = "http://localhost:8080/reporte/ventas";
    private $apiUrlPost = "http://localhost:8080/agregar/venta";
    private $apiUrlPatch = "http://localhost:8080/actualizar/venta/{id}";
    private $apiUrlDelete = "http://localhost:8080/eliminar/venta/{id}";

    // ----------- GET: OBTENER LISTA DE VENTAS -----------
    public function obtenerVentas() {
        $respuesta = @file_get_contents($this->apiUrl);

        if ($respuesta === FALSE) {
            return [];
        }

        $json = json_decode($respuesta, true);

        if (!is_array($json)) {
            return [];
        }

        return $json;
    }

    // ----------- POST: AGREGAR VENTA -----------
    public function agregarVenta($ventaData) {
        $data_json = json_encode($ventaData);

        $proceso = curl_init($this->apiUrlPost);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_json)
        ]);

        $respuestapet = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

        if (curl_errno($proceso)) {
            $error = curl_error($proceso);
            curl_close($proceso);
            return ["success" => false, "error" => $error];
        }

        curl_close($proceso);

        return ($http_code === 200 || $http_code === 201)
            ? ["success" => true, "body" => $respuestapet]
            : ["success" => false, "error" => "HTTP $http_code", "body" => $respuestapet];
    }

    // ----------- PATCH: ACTUALIZAR VENTA -----------
    public function actualizarVenta($id, $ventaData) {
        $url = str_replace("{id}", $id, $this->apiUrlPatch);
        $data_json = json_encode($ventaData);

        $proceso = curl_init($url);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_json)
        ]);

        $respuesta = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

        if (curl_errno($proceso)) {
            $error = curl_error($proceso);
            curl_close($proceso);
            return ["success" => false, "error" => $error];
        }

        curl_close($proceso);

        return ($http_code === 200)
            ? ["success" => true, "body" => $respuesta]
            : ["success" => false, "error" => "HTTP $http_code", "body" => $respuesta];
    }

    // ----------- DELETE: ELIMINAR VENTA -----------
    public function eliminarVenta($id) {
        $url = str_replace("{id}", $id, $this->apiUrlDelete);

        $proceso = curl_init($url);
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

        return ($http_code >= 200 && $http_code < 300)
            ? ["success" => true, "body" => $respuesta]
            : ["success" => false, "error" => "HTTP $http_code", "body" => $respuesta];
    }
}
