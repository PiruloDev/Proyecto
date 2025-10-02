<?php
// ¡Importante! session_start() NO debe estar aquí. Debe estar en el punto de entrada (ej: index.php)
require_once __DIR__ . '/../../services/inventarioservices/InventarioService.php';

class ProduccionController {
    private $service;

    public function __construct() {
        $this->service = new InventarioService();
    }

    /**
     * Punto de entrada principal para manejar las peticiones (GET, POST, PUT, PATCH, DELETE).
     */
    public function manejarPeticion() {
        $method = $_SERVER['REQUEST_METHOD'];
        $accion = $_GET['accion'] ?? null;

        // 1. Detección de método HTTP real o simulado (para PUT/PATCH/DELETE)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        // 2. Routing de la petición
        if ($method === 'POST' && $accion === 'registrar') {
            $this->registrar();
        } elseif ($method === 'PUT' && $accion === 'actualizar') {
            $this->actualizar();
        } elseif ($method === 'PATCH' && $accion === 'parcial') {
            $this->actualizarParcial();
        } elseif ($method === 'DELETE' && $accion === 'eliminar') {
            $this->eliminar();
        } else {
            // Maneja GET para mostrar el formulario o la receta
            $this->mostrarFormulario();
        }
    }

    /**
     * Muestra el formulario, el historial y la receta si se solicita.
     */
    public function mostrarFormulario() {
        $receta = null;
        // Obtiene el mensaje de la sesión (después de una redirección)
        $mensaje = $_SESSION['mensaje'] ?? null;
        unset($_SESSION['mensaje']); // Limpia el mensaje después de mostrarlo

        $idProducto = $_GET['idProducto'] ?? null;
        
        // Cargar historial de producción (usando el endpoint configurado como 'registrar')
        $historial = [];
        $resHistorial = $this->service->obtenerHistorial();
        if ($resHistorial['status'] === 200 && is_array($resHistorial['body'])) {
            $historial = $resHistorial['body'];
        }

        // Cargar receta si se solicita
        if (isset($_GET['accion']) && $_GET['accion'] === 'verReceta' && $idProducto) {
            $resultado = $this->service->obtenerReceta($idProducto);
            if ($resultado['status'] === 200 && is_array($resultado['body'])) {
                $receta = $resultado['body'];
            } else {
                // Mensaje de error si la receta no se encuentra
                $mensaje = "❌ Error {$resultado['status']}: " . ($resultado['body']['error'] ?? "Receta no encontrada o error de API.");
            }
        }

        require __DIR__ . '/../../views/produccion/produccion_form.php';
    }

    /**
     * Maneja el registro de nueva producción (POST).
     */
    private function registrar() {
        $idProducto = $_POST['idProducto'];
        $cantidad   = $_POST['cantidad'];

        // Llama al servicio para registrar la producción
        $resultado = $this->service->registrarProduccion($idProducto, $cantidad, []);

        // Éxito: 201 Created es el estándar para POST
        if ($resultado['status'] === 201) {
            $_SESSION['mensaje'] = "✅ Éxito: " . ($resultado['body']['mensaje'] ?? "Producción registrada con éxito.");
        } else {
            $_SESSION['mensaje'] = "❌ Error {$resultado['status']}: " . ($resultado['body']['error'] ?? "Error desconocido al registrar.");
        }
        // Redirigir (Patrón PRG)
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }

    /**
     * Maneja la actualización completa de un registro (PUT).
     */
    private function actualizar() {
        $idProduccion = $_POST['idProduccion'];
        // Crear el payload con los datos requeridos por la API
        $data = [
            'idProducto' => (int)$_POST['idProducto'],
            'cantidadProducida' => (int)$_POST['cantidad']
        ];

        $resultado = $this->service->actualizarProduccion($idProduccion, $data);
        
        // Éxito: 200 OK para actualización completa
        if ($resultado['status'] === 200) {
            $_SESSION['mensaje'] = "✅ Éxito: Producción ID **{$idProduccion}** actualizada completamente.";
        } else {
            $_SESSION['mensaje'] = "❌ Error {$resultado['status']}: " . ($resultado['body']['error'] ?? "Fallo al actualizar (PUT).");
        }
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }

    /**
     * Maneja la actualización parcial de un registro (PATCH).
     */
    private function actualizarParcial() {
        $idProduccion = $_POST['idProduccion'];
        $updates = [];

        // Solo se añade al payload si el campo tiene valor
        if (isset($_POST['cantidad']) && $_POST['cantidad'] !== '') {
            $updates['cantidadProducida'] = (int)$_POST['cantidad'];
        }
        
        if (empty($updates)) {
            $_SESSION['mensaje'] = "⚠️ Advertencia: No se proporcionaron campos válidos para actualizar.";
        } else {
            $resultado = $this->service->actualizarParcial($idProduccion, $updates);
            
            // Éxito: 200 OK para actualización parcial
            if ($resultado['status'] === 200) {
                $_SESSION['mensaje'] = "✅ Éxito: Producción ID **{$idProduccion}** modificada parcialmente.";
            } else {
                $_SESSION['mensaje'] = "❌ Error {$resultado['status']}: " . ($resultado['body']['error'] ?? "Fallo al actualizar parcial (PATCH).");
            }
        }
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }

    /**
     * Maneja la eliminación de un registro de producción (DELETE).
     */
    private function eliminar() {
        $idProduccion = $_POST['idProduccion'];

        $resultado = $this->service->eliminarProduccion($idProduccion);

        // Éxito: 204 No Content es el estándar para DELETE exitoso
        if ($resultado['status'] === 204) { 
            $_SESSION['mensaje'] = "🗑️ Éxito: Producción ID **{$idProduccion}** eliminada y reversada (si aplica).";
        } else {
            $_SESSION['mensaje'] = "❌ Error {$resultado['status']}: " . ($resultado['body']['error'] ?? "Fallo al eliminar (DELETE).");
        }
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }
}
?>
