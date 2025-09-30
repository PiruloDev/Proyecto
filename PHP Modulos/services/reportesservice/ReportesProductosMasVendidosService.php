<?php
class ReportesProductosMasVendidosService {
    private $apiUrl = "http://localhost:8080/productos/mas-vendidos";

    public function obtenerProductosMasVendidos($limite = 10) {
        $url = $this->apiUrl . "?limite=" . $limite;
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        $respuesta = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($respuesta === false || $httpCode !== 200) {
            return [];
        }

        $json = json_decode($respuesta, true);
        return is_array($json) ? $json : [];
    }
}