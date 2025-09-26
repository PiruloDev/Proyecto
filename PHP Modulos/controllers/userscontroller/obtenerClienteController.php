<?php
require_once __DIR__ . '/../services/DetalleClienteService.php';
require_once __DIR__ . '/../services/CrearClienteService.php';
require_once __DIR__ . '/../services/ActualizarClienteService.php';

class ObtenerClienteController {
    private $detallesClientes;
    private $crearClientes;
    private $actualizarClientes;
    
    public function __construct() {
        $this->detallesClientes = new detallesUsusarios();
        $this->crearClientes = new CrearClienteService();
        $this->actualizarClientes = new ActualizarClienteService();
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
    
    public function actualizarCliente() {
        $mensaje = "";
        $cliente = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id_cliente = (int)$_GET['id'];
            $resultado = $this->detallesClientes->obtenerDatos('cliente');
            $usuarios = $resultado['data'] ?? [];
            
            foreach ($usuarios as $usuario) {
                if (isset($usuario['id']) && $usuario['id'] == $id_cliente) {
                    $cliente = $usuario;
                    break;
                }
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cliente = (int)($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            
            if ($id_cliente > 0 && (!empty($nombre) || !empty($email) || !empty($telefono) || !empty($contrasena))) {
                $datosActualizar = [];
                if (!empty($nombre)) $datosActualizar['nombre'] = $nombre;
                if (!empty($email)) $datosActualizar['email'] = $email;
                if (!empty($telefono)) $datosActualizar['telefono'] = $telefono;
                if (!empty($contrasena)) $datosActualizar['contrasena'] = $contrasena;
                
                $resultado = $this->actualizarClientes->actualizarCliente($id_cliente, $datosActualizar);
                
                if ($resultado['success']) {
                    $mensaje = "<p style='color:green;'>Cliente actualizado exitosamente.</p>";
                } else {
                    $mensaje = "<p style='color:red;'>Error al actualizar cliente: " . ($resultado['error'] ?? 'Error desconocido') . "</p>";
                }
            } else {
                $mensaje = "<p style='color:red;'>ID del cliente y al menos un campo son requeridos.</p>";
            }
        }
        
        require_once __DIR__ . '/../views/actualizar_cliente.php';
    }
    
    public function modificarCliente() {
        $mensaje = "";
        $cliente = null;
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id_cliente = (int)$_GET['id'];
            $resultado = $this->detallesClientes->obtenerDatos('cliente');
            $usuarios = $resultado['data'] ?? [];
            
            if ($id_cliente > 0 && $id_cliente <= count($usuarios)) {
                $cliente = $usuarios[$id_cliente - 1];
                $cliente['id'] = $id_cliente;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cliente = (int)($_POST['id'] ?? 0);
            $accion = $_POST['accion'] ?? '';
            
            if ($id_cliente > 0 && !empty($accion)) {
                $datosActualizar = [];
                
                switch ($accion) {
                    case 'desactivar':
                        $datosActualizar['nombre'] = '[DESACTIVADO]';
                        break;
                    case 'cambiar_nombre':
                        $nuevo_nombre = trim($_POST['nuevo_nombre'] ?? '');
                        if (!empty($nuevo_nombre)) {
                            $datosActualizar['nombre'] = $nuevo_nombre;
                        }
                        break;
                    case 'cambiar_email':
                        $nuevo_email = trim($_POST['nuevo_email'] ?? '');
                        if (!empty($nuevo_email)) {
                            $datosActualizar['email'] = $nuevo_email;
                        }
                        break;
                    case 'cambiar_telefono':
                        $nuevo_telefono = trim($_POST['nuevo_telefono'] ?? '');
                        if (!empty($nuevo_telefono)) {
                            $datosActualizar['telefono'] = $nuevo_telefono;
                        }
                        break;
                    case 'cambiar_contrasena':
                        $nueva_contrasena = trim($_POST['nueva_contrasena'] ?? '');
                        if (!empty($nueva_contrasena)) {
                            $datosActualizar['contrasena'] = $nueva_contrasena;
                        }
                        break;
                }
                
                if (!empty($datosActualizar)) {
                    $debug_info = "<p style='color:blue;'>Debug: ID=" . $id_cliente . ", Datos: " . json_encode($datosActualizar) . "</p>";
                    
                    $resultado = $this->actualizarClientes->actualizarCliente($id_cliente, $datosActualizar);
                    
                    $debug_response = "<p style='color:orange;'>Debug Response: " . json_encode($resultado) . "</p>";
                    
                    if ($resultado['success']) {
                        $mensaje = "<p style='color:green;'>Cliente modificado exitosamente.</p>" . $debug_info . $debug_response;
                        $resultadoClientes = $this->detallesClientes->obtenerDatos('cliente');
                        $usuarios = $resultadoClientes['data'] ?? [];
                        if ($id_cliente > 0 && $id_cliente <= count($usuarios)) {
                            $cliente = $usuarios[$id_cliente - 1];
                            $cliente['id'] = $id_cliente;
                        }
                    } else {
                        $mensaje = "<p style='color:red;'>Error al modificar cliente: " . ($resultado['error'] ?? 'Error desconocido') . "</p>" . $debug_info . $debug_response;
                    }
                } else {
                    $mensaje = "<p style='color:red;'>No se proporcionaron datos válidos para la modificación.</p>";
                }
            } else {
                $mensaje = "<p style='color:red;'>Operación cancelada o datos inválidos.</p>";
            }
        }
        
        require_once __DIR__ . '/../views/modificar_cliente.php';
    }
    
    public function manejoPeticionCliente() {
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'update':
                $this->actualizarCliente();
                break;
            case 'modify':
                $this->modificarCliente();
                break;
            default:
                $this->peticionCliente();
                break;
        }
    }
}
?>