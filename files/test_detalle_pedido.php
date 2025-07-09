<?php
// Test directo para el detalle del pedido
session_start();

// Bypass de autenticación solo para testing
$_SESSION['usuario_logueado'] = true;
$_SESSION['usuario_tipo'] = 'empleado';
$_SESSION['usuario_nombre'] = 'Test Empleado';

require_once 'conexion.php';

echo "<h2>Test Detalle de Pedido</h2>";

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }
    
    echo "<h3>✅ Conexión a BD exitosa</h3>";
    
    // Verificar estructura de tablas
    echo "<h3>📋 Estructura de tablas:</h3>";
    
    // Verificar tabla Detalle_Pedidos
    $result = $conexion->query("DESCRIBE Detalle_Pedidos");
    if ($result) {
        echo "<strong>Tabla Detalle_Pedidos:</strong><br>";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']} ({$row['Type']})<br>";
        }
    }
    
    echo "<br>";
    
    // Verificar tabla Productos
    $result = $conexion->query("DESCRIBE Productos");
    if ($result) {
        echo "<strong>Tabla Productos:</strong><br>";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']} ({$row['Type']})<br>";
        }
    }
    
    echo "<br>";
    
    // Buscar un pedido existente
    $pedidos = $conexion->query("SELECT ID_PEDIDO FROM Pedidos LIMIT 5");
    if ($pedidos && $pedidos->num_rows > 0) {
        echo "<h3>🔍 Pedidos disponibles:</h3>";
        while ($pedido = $pedidos->fetch_assoc()) {
            echo "- Pedido ID: {$pedido['ID_PEDIDO']}<br>";
        }
        
        // Tomar el primer pedido para test
        $pedidos->data_seek(0);
        $primer_pedido = $pedidos->fetch_assoc();
        $test_pedido_id = $primer_pedido['ID_PEDIDO'];
        
        echo "<br><h3>🧪 Probando detalle del pedido #{$test_pedido_id}:</h3>";
        
        // Simular la llamada AJAX
        $_POST['pedido_id'] = $test_pedido_id;
        $input_data = json_encode(['pedido_id' => $test_pedido_id]);
        
        // Incluir el archivo AJAX directamente
        ob_start();
        
        // Simulamos la entrada JSON
        file_put_contents('php://temp', $input_data);
        
        // Llamamos al endpoint
        include 'obtener_detalle_pedido_ajax.php';
        
        $output = ob_get_clean();
        
        echo "<strong>Respuesta del endpoint:</strong><br>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
        
        // Intentar parsear como JSON
        $data = json_decode($output, true);
        if ($data) {
            echo "<strong>Datos parseados:</strong><br>";
            echo "- Success: " . ($data['success'] ? 'true' : 'false') . "<br>";
            if ($data['success']) {
                echo "- Pedido ID: " . $data['pedido']['id'] . "<br>";
                echo "- Cliente: " . $data['pedido']['cliente'] . "<br>";
                echo "- Total: $" . number_format($data['pedido']['total']) . "<br>";
                echo "- Productos: " . count($data['productos']) . "<br>";
                
                if (!empty($data['productos'])) {
                    echo "<br><strong>Productos del pedido:</strong><br>";
                    foreach ($data['productos'] as $producto) {
                        echo "  - {$producto['nombre']} (Cantidad: {$producto['cantidad']}, Precio: $" . number_format($producto['precio_unitario']) . ")<br>";
                    }
                }
            } else {
                echo "- Error: " . ($data['mensaje'] ?? 'No especificado') . "<br>";
            }
        }
        
    } else {
        echo "<h3>❌ No se encontraron pedidos en la base de datos</h3>";
    }
    
} catch (Exception $e) {
    echo "<h3>❌ Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
}

echo "<br><hr>";
echo "<a href='dashboard_empleado.php'>← Volver al Dashboard</a>";
?>
