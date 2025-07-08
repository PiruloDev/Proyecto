<?php
session_start();

// Verificar autenticación y permisos
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    echo json_encode(['success' => false, 'mensaje' => 'Acceso no autorizado']);
    exit();
}

// Headers de respuesta JSON
header('Content-Type: application/json');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

try {
    // Conexión PDO
    $dsn = "mysql:host=localhost;dbname=proyectopanaderia;charset=utf8";
    $username = "root";
    $password = "";
    
    $pdo_conexion = new PDO($dsn, $username, $password);
    $pdo_conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener datos JSON del cuerpo de la petición
    $raw_input = file_get_contents('php://input');
    error_log("Raw input recibido para cliente: " . $raw_input);
    
    $input = json_decode($raw_input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
    if (!$input) {
        throw new Exception('Datos JSON inválidos o vacíos');
    }
    
    // Debug: Log de los datos recibidos
    error_log("Datos decodificados para cliente: " . print_r($input, true));
    
    // Validar que se recibieron los campos necesarios
    if (!isset($input['id'])) {
        throw new Exception('Campo "id" faltante en los datos JSON');
    }
    
    if (!isset($input['estado'])) {
        throw new Exception('Campo "estado" faltante en los datos JSON');
    }
    
    // Convertir y validar los datos
    $cliente_id = filter_var($input['id'], FILTER_VALIDATE_INT);
    $nuevo_estado = filter_var($input['estado'], FILTER_VALIDATE_INT);
    
    // Validaciones estrictas
    if ($cliente_id === false || $cliente_id <= 0) {
        throw new Exception('ID de cliente inválido: ' . json_encode($input['id']));
    }
    
    if ($nuevo_estado === false || !in_array($nuevo_estado, [0, 1], true)) {
        throw new Exception('Estado inválido: ' . json_encode($input['estado']));
    }
    
    error_log("Validaciones pasadas para cliente - ID: $cliente_id, Estado: $nuevo_estado");
    
    // Verificar que el cliente existe
    $consulta_verificar = "SELECT ID_CLIENTE, NOMBRE_CLI, ACTIVO_CLI FROM Clientes WHERE ID_CLIENTE = :cliente_id";
    $stmt_verificar = $pdo_conexion->prepare($consulta_verificar);
    $stmt_verificar->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
    $stmt_verificar->execute();
    
    $cliente = $stmt_verificar->fetch(PDO::FETCH_ASSOC);
    
    if (!$cliente) {
        throw new Exception("Cliente con ID $cliente_id no encontrado en la base de datos");
    }
    
    error_log("Cliente encontrado: " . print_r($cliente, true));
    
    // Actualizar el estado del cliente
    $consulta_actualizar = "UPDATE Clientes SET ACTIVO_CLI = :nuevo_estado WHERE ID_CLIENTE = :cliente_id";
    $stmt_actualizar = $pdo_conexion->prepare($consulta_actualizar);
    $stmt_actualizar->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
    $stmt_actualizar->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
    
    $update_result = $stmt_actualizar->execute();
    $affected_rows = $stmt_actualizar->rowCount();
    
    error_log("Resultado de la actualización de cliente - Éxito: " . ($update_result ? 'Sí' : 'No') . ", Filas afectadas: $affected_rows");
    
    if ($update_result && $affected_rows > 0) {
        $estado_texto = $nuevo_estado ? 'activo' : 'inactivo';
        $accion_texto = $nuevo_estado ? 'activado' : 'desactivado';
        
        echo json_encode([
            'success' => true,
            'mensaje' => "Cliente {$accion_texto} exitosamente",
            'cliente' => [
                'id' => $cliente_id,
                'nombre' => $cliente['NOMBRE_CLI'],
                'estado' => $nuevo_estado,
                'estado_texto' => $estado_texto
            ]
        ]);
    } else {
        throw new Exception('Error al actualizar el estado del cliente. Filas afectadas: ' . $affected_rows);
    }
    
} catch (Exception $e) {
    error_log("Error en toggle_estado_cliente.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>
