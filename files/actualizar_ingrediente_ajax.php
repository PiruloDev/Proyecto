<?php
session_start();

// Headers de seguridad
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Type: application/json; charset=utf-8');

// Verificación de autenticación
if (!isset($_SESSION['usuario_logueado']) || 
    $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || 
    $_SESSION['usuario_tipo'] !== 'admin') {
    
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'No autorizado'
    ]);
    exit();
}

require_once 'conexion.php';

try {
    // Verificar método de petición
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    // Verificar conexión
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }

    // Obtener datos JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception("Datos JSON inválidos");
    }

    // Validar datos requeridos
    if (!isset($input['id']) || !isset($input['campo']) || !isset($input['valor'])) {
        throw new Exception("Faltan datos requeridos: id, campo, valor");
    }

    $ingrediente_id = intval($input['id']);
    $campo = trim($input['campo']);
    $valor = trim($input['valor']);

    // Validar ID del ingrediente
    if ($ingrediente_id <= 0) {
        throw new Exception("ID de ingrediente inválido");
    }

    // Validar que el ingrediente existe
    $stmt_check = $conexion->prepare("SELECT ID_INGREDIENTE, NOMBRE_INGREDIENTE FROM Ingredientes WHERE ID_INGREDIENTE = ?");
    $stmt_check->bind_param("i", $ingrediente_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        throw new Exception("Ingrediente no encontrado");
    }
    
    $ingrediente_info = $result_check->fetch_assoc();

    // Validar y procesar según el campo a actualizar
    switch ($campo) {
        case 'stock':
        case 'cantidad':
            $nuevo_stock = intval($valor);
            if ($nuevo_stock < 0) {
                throw new Exception("El stock no puede ser negativo");
            }
            
            $sql = "UPDATE Ingredientes SET CANTIDAD_INGREDIENTE = ? WHERE ID_INGREDIENTE = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ii", $nuevo_stock, $ingrediente_id);
            
            $mensaje_exito = "Stock del ingrediente '{$ingrediente_info['NOMBRE_INGREDIENTE']}' actualizado a {$nuevo_stock}";
            break;

        case 'precio':
        case 'precio_compra':
            $nuevo_precio = floatval($valor);
            if ($nuevo_precio < 0) {
                throw new Exception("El precio no puede ser negativo");
            }
            
            $sql = "UPDATE Ingredientes SET PRECIO_COMPRA = ? WHERE ID_INGREDIENTE = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("di", $nuevo_precio, $ingrediente_id);
            
            $mensaje_exito = "Precio de compra del ingrediente '{$ingrediente_info['NOMBRE_INGREDIENTE']}' actualizado a $" . number_format($nuevo_precio, 2);
            break;

        default:
            throw new Exception("Campo no válido para actualización: {$campo}");
    }

    // Ejecutar la actualización
    if (!$stmt->execute()) {
        throw new Exception("Error al actualizar: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No se realizaron cambios. Verifique que el valor sea diferente al actual.");
    }

    // Registrar en logs (opcional - se puede implementar después)
    // log_cambio_ingrediente($ingrediente_id, $campo, $valor_anterior, $valor, $_SESSION['usuario_id']);

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'mensaje' => $mensaje_exito,
        'ingrediente_id' => $ingrediente_id,
        'campo_actualizado' => $campo,
        'nuevo_valor' => $valor
    ]);

} catch (Exception $e) {
    error_log("Error en actualizar_ingrediente_ajax.php: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
