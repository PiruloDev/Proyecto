<?php
require_once __DIR__ . '/../../services/userservices/RegistroEmpleadoService.php';
require_once __DIR__ . '/../../services/userservices/EliminarEmpleadoService.php';
require_once __DIR__ . '/../../services/userservices/DetalleEmpleadoService.php';
require_once __DIR__ . '/../../services/userservices/ActualizarEmpleadoService.php';

class ObtenerEmpleadoController {
    private $detallesEmpleados;
    private $registroEmpleados;
    private $actualizarEmpleados;
    private $eliminarEmpleados;
    
    public function __construct() {
        $this->detallesEmpleados = new detallesEmpleados();
        $this->registroEmpleados = new RegistroEmpleadoService();
        $this->actualizarEmpleados = new ActualizarEmpleadoService();
        $this->eliminarEmpleados = new EliminarEmpleadoService();
    }
    public function peticionEmpleado() {
        $mensaje = "";
        
        if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'eliminado') {
            $mensaje = "<p style='color:green;'>Empleado eliminado exitosamente.</p>";
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            
            if (!empty($nombre) && !empty($email) && !empty($contrasena)) {
                $datosEmpleado = [
                    'nombre' => $nombre,
                    'email' => $email,
                    'contrasena' => $contrasena
                ];
                
                $resultadoCreacionEmpleado = $this->registroEmpleados->registrarEmpleado($datosEmpleado);
                
                if ($resultadoCreacionEmpleado['success']) {
                    $mensaje = "<p style='color:green;'>Empleado agregado exitosamente.</p>";
                } else {
                    $mensaje = "<p style='color:red;'>Error al agregar empleado: " . ($resultadoCreacionEmpleado['error'] ?? 'Error desconocido') . "</p>";
                }
            } else {
                $mensaje = "<p style='color:red;'>Todos los campos son requeridos.</p>";
            }
        }
        $resultado = $this->detallesEmpleados->obtenerDatos('empleado');
        $usuarios = $resultado['data'] ?? [];
        
        require_once __DIR__ . '/../../views/empleadoviews/index.php';
    }
    
    public function actualizarEmpleado() {
        $mensaje = "";
        $empleado = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id_empleado = (int)$_GET['id'];
            $resultado = $this->detallesEmpleados->obtenerDatos('empleado');
            $usuarios = $resultado['data'] ?? [];
            
            foreach ($usuarios as $usuario) {
                if (isset($usuario['id']) && $usuario['id'] == $id_empleado) {
                    $empleado = $usuario;
                    break;
                }
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_empleado = (int)($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            
            if ($id_empleado > 0 && (!empty($nombre) || !empty($email) || !empty($contrasena))) {
                $datosActualizar = [];
                if (!empty($nombre)) $datosActualizar['nombre'] = $nombre;
                if (!empty($email)) $datosActualizar['email'] = $email;
                if (!empty($contrasena)) $datosActualizar['contrasena'] = $contrasena;
                
                $resultado = $this->actualizarEmpleados->actualizarEmpleado($id_empleado, $datosActualizar);
                
                if ($resultado['success']) {
                    $mensaje = "<p style='color:green;'>Empleado actualizado exitosamente.</p>";
                } else {
                    $mensaje = "<p style='color:red;'>Error al actualizar empleado: " . ($resultado['error'] ?? 'Error desconocido') . "</p>";
                }
            } else {
                $mensaje = "<p style='color:red;'>ID del empleado y al menos un campo son requeridos.</p>";
            }
        }
        
        require_once __DIR__ . '/../../views/empleadoviews/actualizar_empleado.php';
    }
    
    public function modificarEmpleado() {
        $mensaje = "";
        $empleado = null;
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id_empleado = (int)$_GET['id'];
            $resultado = $this->detallesEmpleados->obtenerDatos('empleado');
            $usuarios = $resultado['data'] ?? [];
            
            if ($id_empleado > 0 && $id_empleado <= count($usuarios)) {
                $empleado = $usuarios[$id_empleado - 1];
                $empleado['id'] = $id_empleado;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_empleado = (int)($_POST['id'] ?? 0);
            $accion = $_POST['accion'] ?? '';
            
            if ($id_empleado > 0 && !empty($accion)) {
                $datosActualizar = [];
                
                switch ($accion) {
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
                    case 'cambiar_contrasena':
                        $nueva_contrasena = trim($_POST['nueva_contrasena'] ?? '');
                        if (!empty($nueva_contrasena)) {
                            $datosActualizar['contrasena'] = $nueva_contrasena;
                        }
                        break;
                }
                
                if (!empty($datosActualizar)) {
                    $resultado = $this->actualizarEmpleados->actualizarEmpleado($id_empleado, $datosActualizar);
                    
                    if ($resultado['success']) {
                        $mensaje = "<p style='color:green;'>Empleado modificado exitosamente.</p>";
                        $resultadoEmpleados = $this->detallesEmpleados->obtenerDatos('empleado');
                        $usuarios = $resultadoEmpleados['data'] ?? [];
                        if ($id_empleado > 0 && $id_empleado <= count($usuarios)) {
                            $empleado = $usuarios[$id_empleado - 1];
                            $empleado['id'] = $id_empleado;
                        }
                    } else {
                        $mensaje = "<p style='color:red;'>Error al modificar empleado: " . ($resultado['error'] ?? 'Error desconocido') . "</p>";
                    }
                } else {
                    $mensaje = "<p style='color:red;'>No se proporcionaron datos válidos para la modificación.</p>";
                }
            } else {
                $mensaje = "<p style='color:red;'>Operación cancelada o datos inválidos.</p>";
            }
        }
        require_once __DIR__ . '/../../views/empleadoviews/modificar_empleado.php';
    }
    public function eliminarEmpleado() {
        $mensaje = "";
        
        $id_empleado = 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id_empleado = (int)$_POST['id'];
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id_empleado = (int)$_GET['id'];
        }
        $empleado_eliminado_id = null;
        
        if ($id_empleado > 0) {
            $resultado = $this->eliminarEmpleados->eliminarEmpleado($id_empleado);
            
            if ($resultado['success']) {
                header("Location: /PHP%20Modulos/Empleadoindex.php?mensaje=eliminado");
                exit();
            } else {
                $mensaje = "<p style='color:red;'>Error al eliminar empleado: " . ($resultado['error'] ?? 'Error desconocido') . "</p>";
            }
        } else {
            $mensaje = "<p style='color:red;'>ID del empleado inválido o no proporcionado.</p>";
        }
        
        $resultado = $this->detallesEmpleados->obtenerDatos('empleado');
        $usuarios = $resultado['data'] ?? [];
        

        if ($empleado_eliminado_id !== null && !empty($usuarios)) {
            foreach ($usuarios as $usuario) {
                if (isset($usuario['Id:']) && $usuario['Id:'] != $empleado_eliminado_id) {
                    $usuarios_filtrados[] = $usuario;
                }
            }
        }
        
        require_once __DIR__ . '/../../views/empleadoviews/index.php';
    }
    
    public function manejoPeticionEmpleado() {
        $action = $_GET['action'] ?? 'list';
        
        switch ($action) {
            case 'update':
                $this->actualizarEmpleado();
                break;
            case 'modify':
                $this->modificarEmpleado();
                break;
            case 'delete':
                $this->eliminarEmpleado();
                break;
            default:
                $this->peticionEmpleado();
                break;
        }
    }
}
?>