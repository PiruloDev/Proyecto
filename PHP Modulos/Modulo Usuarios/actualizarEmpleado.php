<?php 
require_once __DIR__ . '/config.php';

class actualizarEmpleado {
    public function getActualizacionEndpoint(string $tipo, int $id): string {
        return match ($tipo) {
            'empleado' => endopointActualizacion::empleado($id),
            default => throw new InvalidArgumentException("Tipo desconocido: $tipo"),
        };
    }
    
    public function actualizarEmpleado(string $tipo): array {
        echo "\n==============================\n";
        echo "=== ACTUALIZAR EMPLEADO ===\n";
        echo "==============================\n";
        
        echo "Ingrese el ID del empleado a actualizar: ";
        $id_empleado = (int)readline();
        
        $url = $this->getActualizacionEndpoint($tipo, $id_empleado);
        
        echo "\nEmpleado/a a actualizar es: $id_empleado\n";
        echo "\n¿Qué desea actualizar?\n";
        echo "1. Nombre\n";
        echo "2. Email\n";
        echo "4. Contraseña\n";
        echo "5. Todos los campos\n";
        echo "Seleccione una opción (1-5): ";
        
        $opcion = readline();
        $datos_nuevos = array();
        
        switch ($opcion) {
            case '1':
                $nombre = readline("Ingrese el nuevo nombre: ");
                $datos_nuevos["nombre"] = $nombre;
                break;
                
            case '2':
                $email = readline("Ingrese el nuevo email: ");
                $datos_nuevos["email"] = $email;
                break;

            case '3':
                $contrasena = readline("Ingrese la nueva contraseña: ");
                $datos_nuevos["contrasena"] = $contrasena;
                break;
                
            case '4':
                $nombre = readline("Ingrese el nombre: ");
                $email = readline("Ingrese el email: ");
                $telefono = readline("Ingrese el teléfono: ");
                $contrasena = readline("Ingrese la contraseña: ");
                
                $datos_nuevos = array(
                    "nombre" => $nombre,
                    "email" => $email,
                    "contrasena" => $contrasena
                );
                break;
            default:
                throw new InvalidArgumentException("Opción inválida seleccionada");
        }

        $data_json = json_encode($datos_nuevos);
        
        $proceso = curl_init($url);
        curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "PATCH");
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
    
    public function mostrarActualizacion(string $tipo): void {
        echo "\n=== ACTUALIZACIÓN DE EMPLEADO ===\n";
        
        $resultado = $this->actualizarEmpleado($tipo);
        
        echo "\n HTTP Code: " . $resultado['http_code'] . "\n";

        if ($resultado['http_code'] == 200){
            echo "Empleado actualizado exitosamente.\n";
        } else {
            echo "Error al actualizar el empleado.\n";
        }
        
        if ($resultado['data'] !== null && !empty($resultado['data'])) {
            echo "Respuesta del servidor:\n";
            echo json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }
        echo "\n";
    }
}

$actualizacion = new actualizarEmpleado();
$actualizacion->mostrarActualizacion('empleado');
?>
