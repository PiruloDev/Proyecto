<?php
session_start();

// Verificar que el usuario esté logueado y sea administrador
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once 'conexion.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Obtener los datos del POST
$producto_id = intval($_POST['producto_id'] ?? 0);
$accion = $_POST['accion'] ?? '';
$nuevo_valor = $_POST['nuevo_valor'] ?? '';

// Validar datos
if ($producto_id <= 0 || empty($accion)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

try {
    // Verificar que el producto existe
    $stmt = $conexion->prepare("SELECT ID_PRODUCTO, NOMBRE_PRODUCTO, PRECIO_PRODUCTO, PRODUCTO_STOCK_MIN FROM Productos WHERE ID_PRODUCTO = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    
    if (!$producto) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        exit();
    }
    
    $response = ['success' => false, 'message' => ''];
    
    switch ($accion) {
        case 'actualizar_precio':
            $nuevo_precio = floatval($nuevo_valor);
            if ($nuevo_precio <= 0) {
                $response['message'] = 'El precio debe ser mayor a 0';
                break;
            }
            
            $stmt = $conexion->prepare("UPDATE Productos SET PRECIO_PRODUCTO = ? WHERE ID_PRODUCTO = ?");
            $stmt->bind_param("di", $nuevo_precio, $producto_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Precio actualizado correctamente';
                $response['nuevo_precio'] = $nuevo_precio;
            } else {
                $response['message'] = 'Error al actualizar el precio';
            }
            break;
            
        case 'actualizar_stock':
            $nuevo_stock = intval($nuevo_valor);
            if ($nuevo_stock < 0) {
                $response['message'] = 'El stock no puede ser negativo';
                break;
            }
            
            $stmt = $conexion->prepare("UPDATE Productos SET PRODUCTO_STOCK_MIN = ? WHERE ID_PRODUCTO = ?");
            $stmt->bind_param("ii", $nuevo_stock, $producto_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Stock actualizado correctamente';
                $response['nuevo_stock'] = $nuevo_stock;
            } else {
                $response['message'] = 'Error al actualizar el stock';
            }
            break;
            
        case 'incrementar_stock':
            $incremento = intval($nuevo_valor);
            if ($incremento <= 0) {
                $response['message'] = 'El incremento debe ser mayor a 0';
                break;
            }
            
            $nuevo_stock = $producto['PRODUCTO_STOCK_MIN'] + $incremento;
            $stmt = $conexion->prepare("UPDATE Productos SET PRODUCTO_STOCK_MIN = ? WHERE ID_PRODUCTO = ?");
            $stmt->bind_param("ii", $nuevo_stock, $producto_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Stock incrementado en $incremento unidades";
                $response['nuevo_stock'] = $nuevo_stock;
            } else {
                $response['message'] = 'Error al incrementar el stock';
            }
            break;
            
        case 'decrementar_stock':
            $decremento = intval($nuevo_valor);
            if ($decremento <= 0) {
                $response['message'] = 'El decremento debe ser mayor a 0';
                break;
            }
            
            $nuevo_stock = $producto['PRODUCTO_STOCK_MIN'] - $decremento;
            if ($nuevo_stock < 0) {
                $response['message'] = 'El stock no puede ser negativo';
                break;
            }
            
            $stmt = $conexion->prepare("UPDATE Productos SET PRODUCTO_STOCK_MIN = ? WHERE ID_PRODUCTO = ?");
            $stmt->bind_param("ii", $nuevo_stock, $producto_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Stock decrementado en $decremento unidades";
                $response['nuevo_stock'] = $nuevo_stock;
            } else {
                $response['message'] = 'Error al decrementar el stock';
            }
            break;
            
        default:
            $response['message'] = 'Acción no válida';
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
?>
