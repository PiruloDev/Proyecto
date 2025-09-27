<?php
require_once __DIR__ . '/../../config/ConfigProductos.php';

class ProductosService {
    public function obtener() {
        $res = file_get_contents(endpointDetalles::API_DETALLE_PRODUCTO);
        return $res ? json_decode($res, true) : [];
    }

    public function crear($data) {
        return $this->request(endpointCreacion::API_CREAR_PRODUCTO, "POST", $data);
    }

    public function actualizar($id, $data) {
        return $this->request(endpointActualizacion::producto($id), "PATCH", $data);
    }

    public function eliminar($id) {
        return $this->request(endpointEliminacion::producto($id), "DELETE");
    }

    private function request($url, $method, $data = null) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code >= 200 && $code < 300) 
            ? ["success" => true, "body" => $res] 
            : ["success" => false, "error" => "HTTP $code", "body" => $res];
    }
}
