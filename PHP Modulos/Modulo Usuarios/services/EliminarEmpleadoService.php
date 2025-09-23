<?php 
require_once __DIR__ . '/config.php';

class eliminarEmpleado {
    public function getEliminacionEndpoint(string $tipo, int $id): string {
        return match ($tipo) {
            'empleado' => endpointEliminacion::empleado($id),
            default => throw new InvalidArgumentException("Tipo desconocido: $tipo"),
        };
    }
    
    public function eliminarEmpleado(string $tipo): array {
        echo "\n==============================\n";
        echo "=== ELIMINAR EMPLEADO ===\n";
        echo "==============================\n";
        
        echo "Ingrese el ID del empleado a eliminar: ";
        $id_empleado = (int)readline();
        
        $url = $this->getEliminacionEndpoint($tipo, $id_empleado);
        
        echo "\n Empleado seleccionado ID: $id_empleado\n";
        echo "\nEsta seguro que desea eliminar este empleado?\n";
        echo "1. Si, eliminar empleado\n";
        echo "2. No, cancelar operacion\n";
        echo "Seleccione una opcion (1-2): ";
        
        $confirmacion = readline();
        
        if ($confirmacion !== '1') {
            echo "Operacion cancelada.\n";
            return [
                'http_code' => 0,
                'data' => ['message' => 'Operacion cancelada por el usuario']
            ];
        }

        $datos_eliminar = [
            "id" => $id_empleado
        ];
        $data_json = json_encode($datos_eliminar);

        $proceso = curl_init($url);
        curl_setopt_array($proceso, [
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json",
                "Content-Length: " . strlen($data_json)
            ]
        ]);

        $response = curl_exec($proceso);
        $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
        curl_close($proceso);

        return [
            'http_code' => $http_code,
            'data' => json_decode($response, true)
        ];
    }
    
    public function mostrarEliminacion(string $tipo): void {
        echo "\n=== ELIMINACION DE EMPLEADO ===\n";
        
        $resultado = $this->eliminarEmpleado($tipo);
        
        // Si la operacion fue cancelada, no mostrar como error
        if ($resultado['http_code'] === 0) {
            echo "\n" . $resultado['data']['message'] . "\n";
            return;
        }
        
        echo "\nHTTP Code: " . $resultado['http_code'] . "\n";

        if ($resultado['http_code'] == 200 || $resultado['http_code'] == 204){
            echo "Empleado eliminado exitosamente.\n";
        } else {
            echo "Error al eliminar el empleado.\n";
        }
        
        if ($resultado['data'] !== null && !empty($resultado['data'])) {
            echo "\nRespuesta del servidor:\n";
            echo json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }
        echo "\n";
    }
}

// Crear instancia y ejecutar el programa
$eliminacion = new eliminarEmpleado();
$eliminacion->mostrarEliminacion('empleado');
?>