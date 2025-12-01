<?php
header('Content-Type: application/json');
require_once 'conexion.php';

try {
    // Validar que se recibieron los datos por POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener el contenido JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        throw new Exception('ID del ingrediente no proporcionado');
    }
    
    $id_ingrediente = intval($input['id']);
    
    if ($id_ingrediente <= 0) {
        throw new Exception('ID del ingrediente inválido');
    }
    
    // Verificar que el ingrediente existe
    $stmt = $conexion->prepare("SELECT NOMBRE_INGREDIENTE FROM Ingredientes WHERE ID_INGREDIENTE = ?");
    $stmt->bind_param("i", $id_ingrediente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 0) {
        throw new Exception('El ingrediente no existe');
    }
    
    $ingrediente = $resultado->fetch_assoc();
    
    // Eliminar el ingrediente
    $stmt = $conexion->prepare("DELETE FROM Ingredientes WHERE ID_INGREDIENTE = ?");
    $stmt->bind_param("i", $id_ingrediente);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'mensaje' => 'Ingrediente "' . $ingrediente['NOMBRE_INGREDIENTE'] . '" eliminado exitosamente'
            ]);
        } else {
            throw new Exception('No se pudo eliminar el ingrediente');
        }
    } else {
        throw new Exception('Error al eliminar el ingrediente: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}

$conexion->close();
?>
