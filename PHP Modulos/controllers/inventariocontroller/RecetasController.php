<?php
// controllers/inventariocontroller/RecetasController.php

// Inclusión del servicio (2 niveles arriba para llegar a 'services/')
require_once __DIR__ . '/../../services/inventarioservices/RecetasService.php'; 

class RecetasController {
    
    private $recetasService;

    public function __construct() {
        $this->recetasService = new RecetasService();
    }
    
    /**
     * Maneja las peticiones GET para listar o ver una receta.
     */
    public function handleGetRequest() {
        header('Content-Type: application/json');
        
        $idProducto = $_GET['id'] ?? null;
        
        if ($idProducto !== null) {
            // /RecetasController.php?id=123 -> Obtener receta por ID
            $response = $this->recetasService->obtenerRecetaPorProducto((int)$idProducto);
        } else {
            // /RecetasController.php -> Obtener todas las recetas
            $response = $this->recetasService->obtenerTodasLasRecetas();
        }

        $this->sendResponse($response);
    }

    /**
     * Maneja las peticiones POST para crear una receta.
     */
    public function handlePostRequest() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Datos de entrada inválidos."]);
            return;
        }

        $response = $this->recetasService->crearReceta($data);
        $this->sendResponse($response);
    }

    /**
     * Maneja las peticiones PUT para actualizar una receta.
     */
    public function handlePutRequest() {
        header('Content-Type: application/json');
        $idProducto = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);

        if ($idProducto === null || !$data) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "ID de producto o datos de actualización faltantes."]);
            return;
        }

        $response = $this->recetasService->actualizarReceta((int)$idProducto, $data);
        $this->sendResponse($response);
    }
    
    /**
     * Maneja las peticiones DELETE para eliminar una receta.
     */
    public function handleDeleteRequest() {
        header('Content-Type: application/json');
        $idProducto = $_GET['id'] ?? null;

        if ($idProducto === null) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "ID de producto faltante para eliminar."]);
            return;
        }

        $response = $this->recetasService->eliminarReceta((int)$idProducto);
        $this->sendResponse($response);
    }
    
    /**
     * Función unificada para enviar la respuesta.
     */
    private function sendResponse(array $response) {
        if ($response['success']) {
            http_response_code($response['http_code'] ?? 200);
            echo json_encode($response['response']);
        } else {
            http_response_code($response['http_code'] ?? 500);
            echo json_encode(["success" => false, "error" => $response['error']]);
        }
    }
}

// Lógica de ruteo simple basada en el método HTTP
$controller = new RecetasController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $controller->handleGetRequest();
        break;
    case 'POST':
        $controller->handlePostRequest();
        break;
    case 'PUT':
        $controller->handlePutRequest();
        break;
    case 'DELETE':
        $controller->handleDeleteRequest();
        break;
    default:
        http_response_code(405); // Método no permitido
        echo json_encode(["success" => false, "error" => "Método HTTP no soportado"]);
        break;
}