<?php
require_once __DIR__ . '/../Configuracion/Config.php';

class ProductosService_Admin {
    public function obtener() {
        $res = file_get_contents(endpointDetalles::API_DETALLE_ADMIN);
        return $res ? json_decode($res, true) : [];
    }

    public function crear($data) {
        return $this->request(endpointCreacion::API_CREAR_ADMIN, "POST", $data);
    }

    public function actualizar($id, $data) {
        return $this->request(endpointActualizacion::admin($id), "PATCH", $data);
    }

    public function eliminar($id) {
        return $this->request(endpointEliminacion::admin($id), "DELETE");
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

        return ($code == 200 || $code == 201) ? ["success" => true] : ["success" => false, "error" => "HTTP $code"];
    }
}
