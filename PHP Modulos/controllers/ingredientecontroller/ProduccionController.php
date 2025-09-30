<?php
// /app/Controllers/ProduccionController.php (Ejemplo de ruta en un framework MVC)

class ProduccionController {
    
    // URL base de tu API de Spring Boot
    private $apiBaseUrl = "http://localhost:8080/api"; 

    /**
     * Muestra la vista del formulario y carga los productos disponibles.
     */
    public function index() {
        // En un MVC real, aquí obtendrías una lista de productos
        // llamando a un endpoint GET de tu API para popular el <select>.
        
        $productos = $this->obtenerProductosDisponibles();
        
        // Cargar la vista con los datos
        require_once('../Views/produccion/produccion_form.php');
    }

    /**
     * Endpoint llamado por AJAX para obtener la receta de un producto (Spring Boot: RecetasService).
     */
    public function getReceta() {
        if (!isset($_GET['id_producto'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de producto no especificado.']);
            return;
        }

        $idProducto = (int)$_GET['id_producto'];
        $url = $this->apiBaseUrl . "/recetas/producto/" . $idProducto;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            header('Content-Type: application/json');
            echo $response; // Devuelve la lista de IngredienteDescontado
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener la receta desde la API.']);
        }
    }

    /**
     * Maneja el envío del formulario (POST) para registrar la producción (Spring Boot: ProduccionService).
     */
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /produccion'); // Redirigir si no es POST
            exit;
        }
        
        // 1. Recoger y validar datos
        $idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
        $cantidadProducida = filter_input(INPUT_POST, 'cantidad_producida', FILTER_VALIDATE_INT);
        $ingredientesPost = $_POST['ingredientes'] ?? []; // Array de ingredientes[ID] => Cantidad
        
        if (!$idProducto || !$cantidadProducida) {
            $_SESSION['error'] = 'Datos de producción inválidos.';
            header('Location: /produccion');
            exit;
        }

        // 2. Construir el DTO (ProduccionRequest) para la API de Spring Boot
        $ingredientesDescontados = [];
        foreach ($ingredientesPost as $idIngrediente => $cantidadUsada) {
            // Es crucial que PHP envíe la cantidad como string decimal para BigDecimal en Java
            $ingredientesDescontados[] = [
                'idIngrediente' => (int)$idIngrediente,
                'cantidadUsada' => (string)(float)$cantidadUsada // Convertir a float y luego a string para precisión JSON
            ];
        }
        
        $produccionRequest = [
            'idProducto' => $idProducto,
            'cantidadProducida' => $cantidadProducida,
            'ingredientesDescontados' => $ingredientesDescontados
        ];

        // 3. Llamada cURL al endpoint de registro
        $url = $this->apiBaseUrl . "/produccion"; // Asumiendo que tu endpoint POST es /api/produccion
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($produccionRequest));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // 4. Manejo de la Respuesta y redirección
        if ($httpCode >= 200 && $httpCode < 300) {
            // Éxito (HTTP 200 OK, 201 CREATED, etc.)
            $_SESSION['success'] = '✅ Producción registrada exitosamente. Stock actualizado.';
        } else {
            // Error (HTTP 400 Bad Request por falta de stock, o 500 Internal Server Error)
            $errorData = json_decode($response, true);
            $mensajeError = $errorData['message'] ?? $response ?? "Error desconocido (Código HTTP: $httpCode)";
            
            // Aquí capturas el error de 'Stock insuficiente' de tu ProduccionService
            $_SESSION['error'] = '❌ Error al registrar: ' . htmlspecialchars($mensajeError);
        }

        header('Location: /produccion'); // Redirigir de vuelta al formulario
        exit;
    }
    
    // Función de ejemplo para obtener productos (necesitas un endpoint GET /api/productos)
    private function obtenerProductosDisponibles() {
        // Lógica para llamar a /api/productos y devolver un array de objetos/arrays.
        // Aquí se simula un array:
        return [
            ['id' => 1, 'nombre' => 'Pan Campesino'],
            ['id' => 2, 'nombre' => 'Torta de Chocolate']
        ];
    }
}
?>