<?php
class ReportesUsuariosService {
    private $apiUrl = "http://localhost:8080/reporte/usuarios";

    // ----------- GET: OBTENER LISTA DE USUARIOS -----------
    public function obtenerUsuarios() {
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
}
