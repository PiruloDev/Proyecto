<?php
require_once __DIR__ . '/../services/DetalleClienteService.php';
require_once __DIR__ . '/../services/CrearClienteService.php';

class ObtenerClienteController {
    private $detallesClientes;
    private $crearClientes;
    
    public function __construct() {
        $this->detallesClientes = new detallesUsusarios();
        $this->crearClientes = new CrearClienteService();
    }
    public function peticionCliente() {
        $mensaje = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $contraseña = trim($_POST['contraseña'] ?? '');
            
            if (!empty($nombre) && !empty($email) && !empty($telefono) && !empty($contraseña)) {
                $datosCliente = [
                    'nombre' => $nombre,
                    'email' => $email,
                    'telefono' => $telefono,
                    'contrasena' => $contraseña
                ];
                
                $resultadoCreacion = $this->crearClientes->crearCliente($datosCliente);
                
                if ($resultadoCreacion['success']) {
                    $mensaje = "<p style='color:green;'>Cliente agregado exitosamente.</p>";
                }
            } else {
                $mensaje = "<p style='color:red;'>Todos los campos son requeridos.</p>";
            }
        }
        $resultado = $this->detallesClientes->obtenerDatos('cliente');
        $usuarios = $resultado['data'] ?? [];
        
        require_once __DIR__ . '/../views/index.php';
    }
}
?>