<?php
require_once __DIR__ . '/../service/ingredientesService.php'; // Corregido: ruta y nombre

class ingredientesController {
    private $ingredientesService;
    
    public function __construct() {
        $this->ingredientesService = new ingredientesService();
    }
    
    public function manejarPeticion() {
        $mensaje = "";
        $accion = $_GET['accion'] ?? 'listar';
        
        switch($accion) {
            case 'agregar':
                $mensaje = $this->procesarAgregar();
                break;
            case 'actualizar':
                $mensaje = $this->procesarActualizar();
                break;
            case 'actualizarCantidad':
                $mensaje = $this->procesarActualizarCantidad();
                break;
            case 'eliminar':
                $mensaje = $this->procesarEliminar();
                break;
            case 'listar':
            default:
                // Solo mostrar la lista
                break;
        }
        
        // Obtener ingredientes para mostrar en la vista
        $ingredientes = $this->ingredientesService->obtenerIngredientes();
        
        // Pasar datos a la vista
        require __DIR__ . '/../vista/view.php';
    }
    
    private function procesarAgregar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "<p style='color:red;'>Método no permitido.</p>";
        }
        
        // Validar y limpiar datos
        $idProveedor = $this->validarEntero($_POST["idProveedor"] ?? '');
        $idCategoria = $this->validarEntero($_POST["idCategoria"] ?? '');
        $nombreIngrediente = trim($_POST["nombreIngrediente"] ?? '');
        $cantidadIngrediente = $this->validarEntero($_POST["cantidadIngrediente"] ?? '');
        $fechaVencimiento = trim($_POST["fechaVencimiento"] ?? '');
        $referenciaIngrediente = trim($_POST["referenciaIngrediente"] ?? '');
        $fechaEntregaIngrediente = trim($_POST["fechaEntregaIngrediente"] ?? '');
        
        // Validaciones mejoradas
        $errores = $this->validarDatosIngrediente(
            $idProveedor, $idCategoria, $nombreIngrediente, 
            $cantidadIngrediente, $fechaVencimiento, 
            $referenciaIngrediente, $fechaEntregaIngrediente
        );
        
        if (!empty($errores)) {
            return "<p style='color:red;'>Errores: " . implode(", ", $errores) . "</p>";
        }
        
        // Agregar ingrediente
        $resultado = $this->ingredientesService->agregarIngredientes(
            $idProveedor, $idCategoria, $nombreIngrediente, 
            $cantidadIngrediente, $fechaVencimiento, 
            $referenciaIngrediente, $fechaEntregaIngrediente
        );
        
        if ($resultado["success"]) {
            return "<p style='color:green;'>Ingrediente agregado correctamente.</p>";
        } else {
            return "<p style='color:red;'>Error al agregar ingrediente: " . 
                ($resultado["error"] ?? 'Error desconocido') . "</p>";
        }
    }
    
    private function procesarActualizar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "<p style='color:red;'>Método no permitido.</p>";
        }
        
        $id = $this->validarEntero($_POST["id"] ?? '');
        if ($id <= 0) {
            return "<p style='color:red;'>ID de ingrediente inválido.</p>";
        }
        
        // Validar y limpiar datos
        $idProveedor = $this->validarEntero($_POST["idProveedor"] ?? '');
        $idCategoria = $this->validarEntero($_POST["idCategoria"] ?? '');
        $nombreIngrediente = trim($_POST["nombreIngrediente"] ?? '');
        $cantidadIngrediente = $this->validarEntero($_POST["cantidadIngrediente"] ?? '');
        $fechaVencimiento = trim($_POST["fechaVencimiento"] ?? '');
        $referenciaIngrediente = trim($_POST["referenciaIngrediente"] ?? '');
        $fechaEntregaIngrediente = trim($_POST["fechaEntregaIngrediente"] ?? '');
        
        // Validaciones
        $errores = $this->validarDatosIngrediente(
            $idProveedor, $idCategoria, $nombreIngrediente, 
            $cantidadIngrediente, $fechaVencimiento, 
            $referenciaIngrediente, $fechaEntregaIngrediente
        );
        
        if (!empty($errores)) {
            return "<p style='color:red;'>Errores: " . implode(", ", $errores) . "</p>";
        }
        
        // Actualizar ingrediente
        $resultado = $this->ingredientesService->actualizarIngrediente(
            $id, $idProveedor, $idCategoria, $nombreIngrediente, 
            $cantidadIngrediente, $fechaVencimiento, 
            $referenciaIngrediente, $fechaEntregaIngrediente
        );
        
        if ($resultado["success"]) {
            return "<p style='color:green;'>Ingrediente actualizado correctamente.</p>";
        } else {
            return "<p style='color:red;'>Error al actualizar ingrediente: " . 
                   ($resultado["error"] ?? 'Error desconocido') . "</p>";
        }
    }
    
    private function procesarActualizarCantidad() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "<p style='color:red;'>Método no permitido.</p>";
        }
        
        $id = $this->validarEntero($_POST["id"] ?? '');
        $cantidadIngrediente = $this->validarEntero($_POST["cantidadIngrediente"] ?? '');
        
        if ($id <= 0) {
            return "<p style='color:red;'>ID de ingrediente inválido.</p>";
        }
        
        if ($cantidadIngrediente < 0) {
            return "<p style='color:red;'>La cantidad debe ser un número positivo.</p>";
        }
        
        // Actualizar cantidad
        $resultado = $this->ingredientesService->actualizarCantidad($id, $cantidadIngrediente);
        
        if ($resultado["success"]) {
            return "<p style='color:green;'>Cantidad actualizada correctamente.</p>";
        } else {
            return "<p style='color:red;'>Error al actualizar cantidad: " . 
                   ($resultado["error"] ?? 'Error desconocido') . "</p>";
        }
    }
    
    private function procesarEliminar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "<p style='color:red;'>Método no permitido.</p>";
        }
        
        $id = $this->validarEntero($_POST["id"] ?? '');
        
        if ($id <= 0) {
            return "<p style='color:red;'>ID de ingrediente inválido.</p>";
        }
        
        // Eliminar ingrediente
        $resultado = $this->ingredientesService->eliminarIngrediente($id);
        
        if ($resultado["success"]) {
            return "<p style='color:green;'>Ingrediente eliminado correctamente.</p>";
        } else {
            return "<p style='color:red;'>Error al eliminar ingrediente: " . 
                   ($resultado["error"] ?? 'Error desconocido') . "</p>";
        }
    }
    
    private function validarDatosIngrediente($idProveedor, $idCategoria, $nombreIngrediente, 
            $cantidadIngrediente, $fechaVencimiento, 
            $referenciaIngrediente, $fechaEntregaIngrediente) {
        $errores = [];
        
        if ($idProveedor <= 0) {
            $errores[] = "ID Proveedor debe ser un número positivo";
        }
        
        if ($idCategoria <= 0) {
            $errores[] = "ID Categoría debe ser un número positivo";
        }
        
        if (empty($nombreIngrediente)) {
            $errores[] = "El nombre del ingrediente es obligatorio";
        }
        
        if ($cantidadIngrediente < 0) {
            $errores[] = "La cantidad debe ser un número positivo";
        }
        
        if (!empty($fechaVencimiento) && !$this->validarFecha($fechaVencimiento)) {
            $errores[] = "Fecha de vencimiento inválida (formato: YYYY-MM-DD)";
        }
        
        if (!empty($fechaEntregaIngrediente) && !$this->validarFecha($fechaEntregaIngrediente)) {
            $errores[] = "Fecha de entrega inválida (formato: YYYY-MM-DD)";
        }
        
        if (empty($referenciaIngrediente)) {
            $errores[] = "La referencia del ingrediente es obligatoria";
        }
        
        return $errores;
    }
    
    private function validarEntero($valor) {
        $numero = filter_var($valor, FILTER_VALIDATE_INT);
        return $numero !== false ? $numero : 0;
    }
    
    private function validarFecha($fecha) {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }
    
    // Método para obtener ingredientes (útil para AJAX)
    public function obtenerIngredientesJson() {
        header('Content-Type: application/json');
        $ingredientes = $this->ingredientesService->obtenerIngredientes();
        
        if ($ingredientes === false) {
            echo json_encode(["error" => "No se pudieron obtener los ingredientes"]);
        } else {
            echo json_encode($ingredientes);
        }
        exit;
    }
}
?>