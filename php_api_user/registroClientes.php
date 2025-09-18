<?php
require_once __DIR__ . '/config.php';

class RegistroClientes { 
    public function getCreacionEndpoint(string $tipo): string {
        return match ($tipo) {
            'cliente' => endpointCreacion::API_CREAR_CLIENTE,
            default => throw new InvalidArgumentException("Tipo desconocido: $tipo"),
        };
    }
    
    public function crearCliente(string $tipo): array {
        $url = $this->getCreacionEndpoint($tipo);

        echo "\n==============================\n";
        echo "=== REGISTRO DE CLIENTES ===\n";
        echo "==============================\n";

        $nombre = readline("Ingrese el nombre: ");
        $telefono = readline("Ingrese el telefono: ");
        $correo = readline("Ingrese el correo: ");
        $contrasena = readline("Ingrese la contraseña: ");

        $datos_nuevos = array(
            "nombre" => $nombre,
            "email" => $correo,
            "telefono" => $telefono,
            "contrasena" => $contrasena
        );

        $data_json = json_encode($datos_nuevos);
        
        $proceso = curl_init($url);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Content-Length: " . strlen($data_json)
        ));

        $response = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        curl_close($proceso);

        return [
            'http_code' => $http_code,
            'data' => json_decode($response, true)
        ];
    }
    
    public function mostrarRegistro(string $tipo): void {
        echo "\n=== REGISTRO DE NUEVO CLIENTE ===\n";
        echo "Endpoint: " . $this->getCreacionEndpoint($tipo) . "\n";
        
        $resultado = $this->crearCliente($tipo);
        
        echo "HTTP Code: " . $resultado['http_code'] . "\n";
        if ($resultado['http_code'] == 200){
            echo "Cliente registrado exitosamente.\n";
        } else {
            echo "Error al registrar el cliente.\n";
        }
        if ($resultado['data'] !== null && !empty($resultado['data'])) {
            echo "Respuesta del servidor:\n";
            echo json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }
        echo "\n";
    }
}

$registro = new RegistroClientes();
$registro->mostrarRegistro('cliente');
?>