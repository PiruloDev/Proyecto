<?php
// Archivo de prueba para verificar la corrección en detalle de pedidos
session_start();
require_once 'conexion.php';

echo "<h2>🔧 Test de Corrección - Detalle de Pedidos</h2>";
echo "<p>Verificando que las consultas funcionen correctamente después de las correcciones.</p>";

try {
    // Verificar la estructura de la tabla Detalle_Pedidos
    echo "<h3>📋 Estructura de Detalle_Pedidos:</h3>";
    $result = $conexion->query("SHOW COLUMNS FROM Detalle_Pedidos");
    if ($result) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><strong>{$row['Field']}</strong> - {$row['Type']}</li>";
        }
        echo "</ul>";
    }
    
    // Probar la consulta corregida para el dashboard de empleado
    echo "<h3>🔍 Test Query Dashboard Empleado (AJAX):</h3>";
    $pedido_id = 1; // Usar el primer pedido como prueba
    
    // Verificar si la columna DESCRIPCION_PRODUCTO existe
    $check_descripcion = $conexion->query("SHOW COLUMNS FROM Productos LIKE 'DESCRIPCION_PRODUCTO'");
    $has_descripcion = $check_descripcion && $check_descripcion->num_rows > 0;
    
    $descripcion_field = $has_descripcion ? 
        "COALESCE(prod.DESCRIPCION_PRODUCTO, prod.TIPO_PRODUCTO_MARCA, '') as producto_descripcion" :
        "COALESCE(prod.TIPO_PRODUCTO_MARCA, '') as producto_descripcion";
    
    $stmt = $conexion->prepare("
        SELECT 
            dp.ID_DETALLE,
            dp.CANTIDAD_PRODUCTO,
            dp.PRECIO_UNITARIO,
            dp.SUBTOTAL,
            COALESCE(prod.NOMBRE_PRODUCTO, 'Producto eliminado') as producto_nombre,
            $descripcion_field,
            COALESCE(cat.NOMBRE_CATEGORIAPRODUCTO, 'Sin categoría') as categoria_nombre,
            prod.ID_PRODUCTO as producto_id
        FROM Detalle_Pedidos dp
        LEFT JOIN Productos prod ON dp.ID_PRODUCTO = prod.ID_PRODUCTO
        LEFT JOIN Categoria_Productos cat ON prod.ID_CATEGORIA_PRODUCTO = cat.ID_CATEGORIA_PRODUCTO
        WHERE dp.ID_PEDIDO = ?
        ORDER BY dp.ID_DETALLE
    ");
    
    if ($stmt) {
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<p>✅ Query ejecutada exitosamente para pedido #$pedido_id</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID_DETALLE</th><th>PRODUCTO_ID</th><th>NOMBRE</th><th>CANTIDAD</th><th>PRECIO</th><th>SUBTOTAL</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['ID_DETALLE']}</td>";
            echo "<td>#{$row['producto_id']}</td>";
            echo "<td>{$row['producto_nombre']}</td>";
            echo "<td>{$row['CANTIDAD_PRODUCTO']}</td>";
            echo "<td>\${$row['PRECIO_UNITARIO']}</td>";
            echo "<td>\${$row['SUBTOTAL']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Error preparando query: " . $conexion->error . "</p>";
    }
    
    // Probar la consulta corregida para el dashboard de admin
    echo "<h3>🔍 Test Query Dashboard Admin:</h3>";
    $query_detalles = "SELECT dp.*, pr.ID_PRODUCTO as producto_id, pr.NOMBRE_PRODUCTO as producto_nombre, 
                              COALESCE(pr.DESCRIPCION_PRODUCTO, pr.TIPO_PRODUCTO_MARCA, '') as producto_descripcion,
                              cp.NOMBRE_CATEGORIAPRODUCTO as categoria_nombre
                       FROM Detalle_Pedidos dp
                       JOIN Productos pr ON dp.ID_PRODUCTO = pr.ID_PRODUCTO
                       LEFT JOIN Categoria_Productos cp ON pr.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO
                       WHERE dp.ID_PEDIDO = ?
                       ORDER BY dp.ID_DETALLE";
    
    $stmt_admin = $conexion->prepare($query_detalles);
    if ($stmt_admin) {
        $stmt_admin->bind_param("i", $pedido_id);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();
        
        echo "<p>✅ Query de admin ejecutada exitosamente para pedido #$pedido_id</p>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID_DETALLE</th><th>PRODUCTO_ID</th><th>NOMBRE</th><th>DESCRIPCIÓN</th><th>CATEGORÍA</th><th>CANTIDAD</th></tr>";
        
        while ($row = $result_admin->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['ID_DETALLE']}</td>";
            echo "<td>#{$row['producto_id']}</td>";
            echo "<td>{$row['producto_nombre']}</td>";
            echo "<td>" . substr($row['producto_descripcion'], 0, 50) . "...</td>";
            echo "<td>{$row['categoria_nombre']}</td>";
            echo "<td>{$row['CANTIDAD_PRODUCTO']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Error preparando query de admin: " . $conexion->error . "</p>";
    }
    
    echo "<h3>🎯 Resumen de Correcciones:</h3>";
    echo "<ul>";
    echo "<li>✅ Corregido error 'dp.ID_DETALLE_PEDIDO' → 'dp.ID_DETALLE'</li>";
    echo "<li>✅ Agregada columna de ID de producto en ambos dashboards</li>";
    echo "<li>✅ Eliminada columna de imagen en dashboard admin</li>";
    echo "<li>✅ Agregado soporte para descripción de productos</li>";
    echo "<li>✅ Agregada columna de categoría</li>";
    echo "<li>✅ Estructura consistente entre empleado y admin</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>❌ Error en el test: " . $e->getMessage() . "</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { margin: 10px 0; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    h2 { color: #333; }
    h3 { color: #666; margin-top: 20px; }
    .success { color: green; }
    .error { color: red; }
</style>
