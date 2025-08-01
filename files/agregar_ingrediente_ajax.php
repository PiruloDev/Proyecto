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
    // Validar que se recibieron los datos por POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener y validar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $cantidad = intval($_POST['cantidad'] ?? 0);
    $precio_compra = floatval($_POST['precio_compra'] ?? 0);
    $categoria = intval($_POST['categoria'] ?? 0);
    $proveedor = intval($_POST['proveedor'] ?? 0);
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? '';
    $fecha_entrega = $_POST['fecha_entrega'] ?? '';
    $referencia = trim($_POST['referencia'] ?? '');
    
    // Validaciones
    if (empty($nombre)) {
        throw new Exception('El nombre del ingrediente es obligatorio');
    }
    
    if ($cantidad < 0) {
        throw new Exception('La cantidad no puede ser negativa');
    }
    
    if ($precio_compra < 0) {
        throw new Exception('El precio de compra no puede ser negativo');
    }
    
    if ($categoria <= 0) {
        throw new Exception('Debe seleccionar una categoría');
    }
    
    // El proveedor es opcional
    if ($proveedor <= 0) {
        $proveedor = null;
    }
    
    if (empty($fecha_vencimiento)) {
        throw new Exception('La fecha de vencimiento es obligatoria');
    }
    
    if (empty($fecha_entrega)) {
        throw new Exception('La fecha de entrega es obligatoria');
    }
    
    if (empty($referencia)) {
        throw new Exception('La referencia es obligatoria');
    }
    
    // Validar formato de fechas
    $fecha_venc_obj = DateTime::createFromFormat('Y-m-d', $fecha_vencimiento);
    $fecha_ent_obj = DateTime::createFromFormat('Y-m-d', $fecha_entrega);
    
    if (!$fecha_venc_obj || !$fecha_ent_obj) {
        throw new Exception('Formato de fecha inválido');
    }
    
    // Validar que la fecha de vencimiento sea futura
    $hoy = new DateTime();
    if ($fecha_venc_obj <= $hoy) {
        throw new Exception('La fecha de vencimiento debe ser futura');
    }
    
    // Verificar que la categoría existe
    $stmt = $conexion->prepare("SELECT ID_CATEGORIA FROM Categoria_Ingredientes WHERE ID_CATEGORIA = ?");
    $stmt->bind_param("i", $categoria);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('La categoría seleccionada no existe');
    }
    
    // Verificar que el proveedor existe y está activo (solo si se especificó)
    if ($proveedor !== null) {
        $stmt = $conexion->prepare("SELECT ID_PROVEEDOR FROM Proveedores WHERE ID_PROVEEDOR = ? AND ACTIVO_PROV = 1");
        $stmt->bind_param("i", $proveedor);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception('El proveedor seleccionado no existe o no está activo');
        }
    }
    
    // Insertar el ingrediente
    $stmt = $conexion->prepare("INSERT INTO Ingredientes (
        ID_PROVEEDOR, 
        ID_CATEGORIA, 
        NOMBRE_INGREDIENTE, 
        CANTIDAD_INGREDIENTE,
        PRECIO_COMPRA, 
        FECHA_VENCIMIENTO, 
        REFERENCIA_INGREDIENTE, 
        FECHA_ENTREGA_INGREDIENTE
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("iisidsss", 
        $proveedor, 
        $categoria, 
        $nombre, 
        $cantidad,
        $precio_compra, 
        $cantidad, 
        $fecha_vencimiento, 
        $referencia, 
        $fecha_entrega
    );
    
    if ($stmt->execute()) {
        $id_ingrediente = $conexion->insert_id;
        
        echo json_encode([
            'success' => true,
            'mensaje' => 'Ingrediente agregado exitosamente',
            'id' => $id_ingrediente
        ]);
    } else {
        throw new Exception('Error al insertar el ingrediente: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}

$conexion->close();
?>
