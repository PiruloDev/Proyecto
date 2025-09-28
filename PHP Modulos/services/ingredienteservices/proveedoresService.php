<?php
// Asegúrate de que esta ruta sea correcta para incluir tu archivo de configuración
require_once __DIR__ . '../../../config/configIngredientes.php'; 

class proveedoresService {
    // La propiedad $apiUrl ya no es necesaria.

    /**
     * Obtener todos los proveedores (GET).
     * Usa: endpointGet::API_GET_PROVEEDORES
     */
    public function obtenerProveedores() {
        // Usamos la constante GET del config
        $url = endpointGet::API_GET_PROVEEDORES;
        
        $response = @file_get_contents($url);
        
        return $response ? json_decode($response, true) : false;
    }

    /**
     * Agrega un nuevo proveedor (POST).
     * Usa: endpointPost::API_CREAR_PROVEEDOR
     */
    public function agregarProveedor($nombre, $telefono, $activo, $email, $direccion) {
        // Usamos la constante POST del config
        $url = endpointPost::API_CREAR_PROVEEDOR;

        $data = [
            "nombreProv"   => $nombre,
            "telefonoProv" => $telefono,
            "activoProv"   => $activo,
            "emailProv"    => $email,
            "direccionProv"=> $direccion
        ];
        // Pasamos la URL directamente a enviarRequest
        return $this->enviarRequest("POST", $url, $data);
    }

    /**
     * Actualiza un proveedor existente (PUT).
     * Usa: endpointPut::proveedor($id)
     */
    public function actualizarProveedor($id, $nombre, $telefono, $activo, $email, $direccion) {
        // Usamos el método estático PUT del config para construir la URL con el ID
        $url = endpointPut::proveedor($id);

        $data = [
            "nombreProv"   => $nombre,
            "telefonoProv" => $telefono,
            "activoProv"   => $activo,
            "emailProv"    => $email,
            "direccionProv"=> $direccion
        ];
        // Pasamos la URL construida a enviarRequest
        return $this->enviarRequest("PUT", $url, $data);
    }

    /**
     * Elimina un proveedor (DELETE).
     * Usa: endpointDelete::proveedor($id)
     */
    public function eliminarProveedor($id) {
        // Usamos el método estático DELETE del config para construir la URL con el ID
        $url = endpointDelete::proveedor($id);

        // Pasamos la URL construida a enviarRequest
        return $this->enviarRequest("DELETE", $url);
    }

    /**
     * Método privado general para enviar solicitudes usando stream_context.
     */
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