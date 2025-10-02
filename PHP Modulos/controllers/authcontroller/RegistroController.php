<?php
require_once __DIR__ . '/../../services/authservices/RegistroService.php';

/**
 * Controlador de registro de clientes
 * Maneja el proceso de registro de nuevos clientes
 */
class RegistroController {
    private $registroService;
    
    public function __construct() {
        $this->registroService = new RegistroService();
    }
    
    /**
     * Procesa el formulario de registro
     */
    public function procesarRegistro() {
        $mensaje = "";
        $tipoMensaje = "";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $contrasena = trim($_POST['contrasena'] ?? '');
            $confirmarContrasena = trim($_POST['confirmar_contrasena'] ?? '');
            
            // Validaciones del frontend
            if (empty($nombre) || empty($email) || empty($telefono) || empty($contrasena)) {
                $mensaje = "Todos los campos son requeridos.";
                $tipoMensaje = "error";
            } elseif (!$this->registroService->validarEmail($email)) {
                $mensaje = "El formato del email no es válido.";
                $tipoMensaje = "error";
            } elseif (!$this->registroService->validarTelefono($telefono)) {
                $mensaje = "El formato del teléfono no es válido.";
                $tipoMensaje = "error";
            } elseif (!$this->registroService->validarContrasena($contrasena)) {
                $mensaje = "La contraseña debe tener al menos 6 caracteres.";
                $tipoMensaje = "error";
            } elseif ($contrasena !== $confirmarContrasena) {
                $mensaje = "Las contraseñas no coinciden.";
                $tipoMensaje = "error";
            } else {
                // Preparar datos para el backend
                $datosCliente = [
                    'nombre' => $nombre,
                    'email' => $email,
                    'telefono' => $telefono,
                    'contrasena' => $contrasena
                ];
                
                // Intentar registro con el backend
                $resultadoRegistro = $this->registroService->registrarCliente($datosCliente);
                
                if ($resultadoRegistro['success']) {
                    // Registro exitoso - redirigir al login
                    header("Location: loginusers.php?registro=exitoso");
                    exit();
                } else {
                    // Error en el registro
                    $errorMsg = $resultadoRegistro['data']['mensaje'] ?? 'Error en el registro';
                    $mensaje = "Error: " . $errorMsg;
                    $tipoMensaje = "error";
                    
                    // Log para debugging
                    error_log("Error de registro - Email: $email, Código HTTP: " . $resultadoRegistro['http_code']);
                }
            }
        }
        
        // Mostrar la vista de registro con el mensaje
        require_once __DIR__ . '/../../views/authviews/registro.php';
    }
}
?>