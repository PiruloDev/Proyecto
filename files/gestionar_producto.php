<?php
include 'conexion.php';

// Verificar si se recibió el ID del producto
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_producto = $_GET['id'];
    
    try {
        // Obtener información del producto
        $consulta_producto = "SELECT * FROM Productos WHERE ID_PRODUCTO = :id";
        $stmt_producto = $pdo_conexion->prepare($consulta_producto);
        $stmt_producto->bindParam(':id', $id_producto, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            // Verificar referencias en detalle_pedidos
            $consulta_referencias = "SELECT COUNT(*) as total FROM detalle_pedidos WHERE ID_PRODUCTO = :id";
            $stmt_referencias = $pdo_conexion->prepare($consulta_referencias);
            $stmt_referencias->bindParam(':id', $id_producto, PDO::PARAM_INT);
            $stmt_referencias->execute();
            $referencias = $stmt_referencias->fetch(PDO::FETCH_ASSOC);
            
            // Procesar acciones
            $mensaje = "";
            $tipo_mensaje = "";
            
            if (isset($_POST['accion'])) {
                switch ($_POST['accion']) {
                    case 'reducir_stock':
                        $consulta_update = "UPDATE Productos SET PRODUCTO_STOCK_MIN = 0 WHERE ID_PRODUCTO = :id";
                        $stmt_update = $pdo_conexion->prepare($consulta_update);
                        $stmt_update->bindParam(':id', $id_producto, PDO::PARAM_INT);
                        
                        if ($stmt_update->execute()) {
                            $mensaje = "Stock mínimo del producto reducido a 0. El producto seguirá visible pero marcado como sin stock.";
                            $tipo_mensaje = "success";
                            // Refrescar datos del producto
                            $stmt_producto->execute();
                            $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
                        } else {
                            $mensaje = "Error al actualizar el producto.";
                            $tipo_mensaje = "error";
                        }
                        break;
                        
                    case 'desactivar':
                        $consulta_update = "UPDATE Productos SET ACTIVO = 0 WHERE ID_PRODUCTO = :id";
                        $stmt_update = $pdo_conexion->prepare($consulta_update);
                        $stmt_update->bindParam(':id', $id_producto, PDO::PARAM_INT);
                        
                        if ($stmt_update->execute()) {
                            $mensaje = "Producto desactivado exitosamente. Ya no aparecerá en búsquedas pero conserva todas sus referencias.";
                            $tipo_mensaje = "success";
                            // Refrescar datos del producto
                            $stmt_producto->execute();
                            $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
                        } else {
                            $mensaje = "Error al desactivar el producto.";
                            $tipo_mensaje = "error";
                        }
                        break;
                        
                    case 'activar':
                        $consulta_update = "UPDATE Productos SET ACTIVO = 1 WHERE ID_PRODUCTO = :id";
                        $stmt_update = $pdo_conexion->prepare($consulta_update);
                        $stmt_update->bindParam(':id', $id_producto, PDO::PARAM_INT);
                        
                        if ($stmt_update->execute()) {
                            $mensaje = "Producto activado exitosamente. Ahora está disponible para nuevos pedidos.";
                            $tipo_mensaje = "success";
                            // Refrescar datos del producto
                            $stmt_producto->execute();
                            $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
                        } else {
                            $mensaje = "Error al activar el producto.";
                            $tipo_mensaje = "error";
                        }
                        break;
                        
                    case 'ver_pedidos':
                        // Esta acción se maneja en el HTML
                        break;
                }
            }
        } else {
            header("Location: productostabla.php");
            exit();
        }
        
    } catch (PDOException $e) {
        $mensaje = "Error en la base de datos: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
} else {
    header("Location: productostabla.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Producto - Panadería</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .producto-info {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
        
        .opciones {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .opcion {
            border: 1px solid #dee2e6;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        h1, h2, h3 {
            color: #333;
        }
        
        .referencias-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Producto</h1>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <div class="producto-info">
            <h3>Información del Producto</h3>
            <p><strong>ID:</strong> <?php echo htmlspecialchars($producto['ID_PRODUCTO']); ?></p>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($producto['NOMBRE_PRODUCTO']); ?></p>
            <p><strong>Precio:</strong> $<?php echo number_format($producto['PRECIO_PRODUCTO'], 2); ?></p>
            <p><strong>Stock Mínimo:</strong> <?php echo htmlspecialchars($producto['PRODUCTO_STOCK_MIN']); ?></p>
            <p><strong>Marca/Tipo:</strong> <?php echo htmlspecialchars($producto['TIPO_PRODUCTO_MARCA']); ?></p>
            <p><strong>Fecha de Vencimiento:</strong> <?php echo date('d/m/Y', strtotime($producto['FECHA_VENCIMIENTO_PRODUCTO'])); ?></p>
            <p><strong>Estado:</strong> 
                <?php if (isset($producto['ACTIVO']) && $producto['ACTIVO'] == 1): ?>
                    <span style="color: #28a745; font-weight: bold;">✓ Activo</span>
                <?php else: ?>
                    <span style="color: #dc3545; font-weight: bold;">✗ Inactivo</span>
                <?php endif; ?>
            </p>
        </div>
        
        <?php if ($referencias['total'] > 0): ?>
            <div class="referencias-info">
                <h4>⚠️ Producto con Referencias</h4>
                <p>Este producto está siendo utilizado en <strong><?php echo $referencias['total']; ?></strong> pedido(s). No se puede eliminar directamente.</p>
            </div>
            
            <h3>Opciones Disponibles</h3>
            <div class="opciones">
                <div class="opcion">
                    <?php if (isset($producto['ACTIVO']) && $producto['ACTIVO'] == 1): ?>
                        <h4>❌ Desactivar Producto</h4>
                        <p>Oculta el producto de búsquedas manteniendo todas las referencias intactas.</p>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="accion" value="desactivar">
                            <button type="submit" class="btn btn-warning" 
                                    onclick="return confirm('¿Desactivar este producto? Se ocultará de búsquedas pero mantendrá todas las referencias en pedidos.');">
                                Desactivar
                            </button>
                        </form>
                    <?php else: ?>
                        <h4>✅ Activar Producto</h4>
                        <p>Hace el producto visible y disponible para nuevos pedidos.</p>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="accion" value="activar">
                            <button type="submit" class="btn btn-success" 
                                    onclick="return confirm('¿Activar este producto? Estará disponible para nuevos pedidos.');">
                                Activar
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="opcion">
                    <h4>🔄 Reducir Stock a 0</h4>
                    <p>Marca el producto como sin stock manteniendo el historial de pedidos.</p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="accion" value="reducir_stock">
                        <button type="submit" class="btn btn-warning" 
                                onclick="return confirm('¿Reducir el stock mínimo a 0? Esto marcará el producto como no disponible.');">
                            Reducir Stock
                        </button>
                    </form>
                </div>
                
                <div class="opcion">
                    <h4>📋 Ver Pedidos Relacionados</h4>
                    <p>Consulta qué pedidos contienen este producto.</p>
                    <a href="ver_pedidos_producto.php?id=<?php echo $producto['ID_PRODUCTO']; ?>" class="btn btn-primary">
                        Ver Pedidos
                    </a>
                </div>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background-color: #e9ecef; border-radius: 8px;">
                <h4>💡 Recomendaciones</h4>
                <ul style="text-align: left;">
                    <li><strong>Para productos descontinuados:</strong> Use "Desactivar" en lugar de eliminar</li>
                    <li><strong>Para productos temporales:</strong> Desactive temporalmente</li>
                    <li><strong>Para errores de datos:</strong> Modifique la información en lugar de eliminar</li>
                    <li><strong>Para productos duplicados:</strong> Desactive el duplicado</li>
                    <li><strong>Para eliminación permanente:</strong> Use la opción "Forzar Eliminación" con precaución</li>
                </ul>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                <h4>✅ Producto sin Referencias</h4>
                <p>Este producto no está siendo utilizado en ningún pedido. Se puede eliminar de forma segura.</p>
                <a href="borrar_producto.php?id=<?php echo $producto['ID_PRODUCTO']; ?>" 
                   class="btn btn-primary"
                   onclick="return confirm('¿Confirmar eliminación del producto?');">
                    Eliminar Producto
                </a>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="productostabla.php" class="btn btn-secondary">Volver a la Lista</a>
            <a href="editar_producto.php?id=<?php echo $producto['ID_PRODUCTO']; ?>" class="btn btn-success">Editar Producto</a>
        </div>
        
        <!-- Opción de eliminación forzada (siempre disponible con advertencia) -->
        <div style="text-align: center; margin-top: 20px; padding: 15px; background-color: #f8d7da; border-radius: 8px; border: 1px solid #f5c6cb;">
            <h4 style="color: #721c24; margin-top: 0;">⚠️ Zona Peligrosa</h4>
            <p style="color: #721c24; font-size: 14px;">
                La eliminación forzada ignora todas las restricciones y puede causar inconsistencias en la base de datos.
            </p>
            <a href="eliminar_forzado.php?id=<?php echo $producto['ID_PRODUCTO']; ?>" 
               class="btn" 
               style="background-color: #dc3545; color: white; border: 2px solid #dc3545;"
               onclick="return confirm('🚨 ELIMINACIÓN FORZADA 🚨\\n\\nEsto eliminará PERMANENTEMENTE el producto ignorando todas las referencias.\\n\\n⚠️ PUEDE CAUSAR INCONSISTENCIAS EN LA BASE DE DATOS ⚠️\\n\\n📋 NOTA: En la siguiente página deberás esperar 5 segundos antes de poder continuar para asegurar que leas todas las advertencias.\\n\\n¿Estás ABSOLUTAMENTE SEGURO de proceder?');">
                🗑️ Forzar Eliminación
            </a>
        </div>
    </div>
</body>
</html>
