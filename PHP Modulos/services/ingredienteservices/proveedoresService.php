<?php
class proveedoresService {
    private $apiUrl = "http://localhost:8080/proveedores";

    public function obtenerProveedores() {
        $response = @file_get_contents($this->apiUrl);
        return $response ? json_decode($response, true) : false;
    }

    public function agregarProveedor($nombre, $telefono, $activo, $email, $direccion) {
        $data = [
            "nombreProv"   => $nombre,
            "telefonoProv" => $telefono,
            "activoProv"   => $activo,
            "emailProv"    => $email,
            "direccionProv"=> $direccion
        ];
        return $this->enviarRequest("POST", $this->apiUrl, $data);
    }

    public function actualizarProveedor($id, $nombre, $telefono, $activo, $email, $direccion) {
        $data = [
            "nombreProv"   => $nombre,
            "telefonoProv" => $telefono,
            "activoProv"   => $activo,
            "emailProv"    => $email,
            "direccionProv"=> $direccion
        ];
        return $this->enviarRequest("PUT", $this->apiUrl . "/" . $id, $data);
    }

    public function eliminarProveedor($id) {
        return $this->enviarRequest("DELETE", $this->apiUrl . "/" . $id);
    }

    private function enviarRequest($method, $url, $data = null) {
        $options = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => $method,
                "content" => $data ? json_encode($data) : null,
            ],
        ];
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);

        if ($result === false) {
            return ["success" => false, "error" => "No se pudo conectar con la API"];
        }

        return ["success" => true, "data" => json_decode($result, true)];
    }
}
?>
