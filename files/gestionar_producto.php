<?php
include 'conexion.php';

// Clase para manejar el stock
class StockManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Descuenta stock de un producto
     */
    public function descontarStock($id_producto, $cantidad) {
        try {
            $this->pdo->beginTransaction();
            
            // Verificar stock actual del producto
            $stmt = $this->pdo->prepare("
                SELECT STOCK_ACTUAL, PRODUCTO_STOCK_MIN, NOMBRE_PRODUCTO, ACTIVO 
                FROM Productos 
                WHERE ID_PRODUCTO = ? AND ACTIVO = 1
                FOR UPDATE
            ");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                throw new Exception("Producto no encontrado o inactivo");
            }
            
            // Verificar si hay suficiente stock
            if ($producto['STOCK_ACTUAL'] < $cantidad) {
                throw new Exception("Stock insuficiente. Stock disponible: " . $producto['STOCK_ACTUAL']);
            }
            
            // Actualizar stock
            $nuevo_stock = $producto['STOCK_ACTUAL'] - $cantidad;
            $stmt = $this->pdo->prepare("
                UPDATE Productos 
                SET STOCK_ACTUAL = ? 
                WHERE ID_PRODUCTO = ?
            ");
            $stmt->execute([$nuevo_stock, $id_producto]);
            
            // Registrar movimiento en historial (opcional)
            $this->registrarMovimiento($id_producto, 'SALIDA', $cantidad, $producto['STOCK_ACTUAL'], $nuevo_stock, 'Venta/Pedido');
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Stock actualizado correctamente',
                'producto' => $producto['NOMBRE_PRODUCTO'],
                'stock_anterior' => $producto['STOCK_ACTUAL'],
                'stock_actual' => $nuevo_stock,
                'alerta_stock_minimo' => ($nuevo_stock <= $producto['PRODUCTO_STOCK_MIN'])
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Aumenta stock de un producto
     */
    public function aumentarStock($id_producto, $cantidad, $motivo = 'Reposición') {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("
                SELECT STOCK_ACTUAL, NOMBRE_PRODUCTO, ACTIVO 
                FROM Productos 
                WHERE ID_PRODUCTO = ?
                FOR UPDATE
            ");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                throw new Exception("Producto no encontrado");
            }
            
            $nuevo_stock = $producto['STOCK_ACTUAL'] + $cantidad;
            $stmt = $this->pdo->prepare("
                UPDATE Productos 
                SET STOCK_ACTUAL = ? 
                WHERE ID_PRODUCTO = ?
            ");
            $stmt->execute([$nuevo_stock, $id_producto]);
            
            $this->registrarMovimiento($id_producto, 'ENTRADA', $cantidad, $producto['STOCK_ACTUAL'], $nuevo_stock, $motivo);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Stock aumentado correctamente',
                'producto' => $producto['NOMBRE_PRODUCTO'],
                'stock_anterior' => $producto['STOCK_ACTUAL'],
                'stock_actual' => $nuevo_stock
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Registra movimiento de stock
     */
    private function registrarMovimiento($id_producto, $tipo, $cantidad, $stock_anterior, $stock_nuevo, $motivo) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO MovimientosStock (ID_PRODUCTO, TIPO_MOVIMIENTO, CANTIDAD, STOCK_ANTERIOR, STOCK_NUEVO, MOTIVO) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_producto, $tipo, $cantidad, $stock_anterior, $stock_nuevo, $motivo]);
        } catch (Exception $e) {
            // Si no existe la tabla de movimientos, no hacer nada
        }
    }
    
    /**
     * Obtiene información del stock
     */
    public function obtenerInfoStock($id_producto) {
        $stmt = $this->pdo->prepare("
            SELECT ID_PRODUCTO, NOMBRE_PRODUCTO, STOCK_ACTUAL, PRODUCTO_STOCK_MIN,
                   CASE 
                       WHEN STOCK_ACTUAL = 0 THEN 'SIN STOCK'
                       WHEN STOCK_ACTUAL <= PRODUCTO_STOCK_MIN THEN 'CRÍTICO'
                       WHEN STOCK_ACTUAL <= (PRODUCTO_STOCK_MIN * 2) THEN 'BAJO'
                       ELSE 'NORMAL'
                   END as ESTADO_STOCK
            FROM Productos 
            WHERE ID_PRODUCTO = ?
        ");
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Inicializar el gestor de stock
$stockManager = new StockManager($pdo_conexion);

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
            
            // Obtener información del stock
            $info_stock = $stockManager->obtenerInfoStock($id_producto);
            
            // Procesar acciones
            $mensaje = "";
            $tipo_mensaje = "";
            
            if (isset($_POST['accion'])) {
                switch ($_POST['accion']) {
                    case 'descontar_stock':
                        if (isset($_POST['cantidad']) && is_numeric($_POST['cantidad']) && $_POST['cantidad'] > 0) {
                            $cantidad = (int)$_POST['cantidad'];
                            $resultado = $stockManager->descontarStock($id_producto, $cantidad);
                            
                            if ($resultado['success']) {
                                $mensaje = "Stock descontado exitosamente. ";
                                $mensaje .= "Producto: " . $resultado['producto'] . ". ";
                                $mensaje .= "Stock anterior: " . $resultado['stock_anterior'] . ". ";
                                $mensaje .= "Stock actual: " . $resultado['stock_actual'] . ".";
                                
                                if ($resultado['alerta_stock_minimo']) {
                                    $mensaje .= " ⚠️ ALERTA: Stock por debajo del mínimo!";
                                }
                                
                                $tipo_mensaje = "success";
                            } else {
                                $mensaje = "Error al descontar stock: " . $resultado['message'];
                                $tipo_mensaje = "error";
                            }
                            
                            // Refrescar información del producto
                            $stmt_producto->execute();
                            $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
                            $info_stock = $stockManager->obtenerInfoStock($id_producto);
                        } else {
                            $mensaje = "Cantidad inválida para descontar.";
                            $tipo_mensaje = "error";
                        }
                        break;
                        
                    case 'aumentar_stock':
                        if (isset($_POST['cantidad']) && is_numeric($_POST['cantidad']) && $_POST['cantidad'] > 0) {
                            $cantidad = (int)$_POST['cantidad'];
                            $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : 'Reposición manual';
                            $resultado = $stockManager->aumentarStock($id_producto, $cantidad, $motivo);
                            
                            if ($resultado['success']) {
                                $mensaje = "Stock aumentado exitosamente. ";
                                $mensaje .= "Producto: " . $resultado['producto'] . ". ";
                                $mensaje .= "Stock anterior: " . $resultado['stock_anterior'] . ". ";
                                $mensaje .= "Stock actual: " . $resultado['stock_actual'] . ".";
                                $tipo_mensaje = "success";
                            } else {
                                $mensaje = "Error al aumentar stock: " . $resultado['message'];
                                $tipo_mensaje = "error";
                            }
                            
                            // Refrescar información del producto
                            $stmt_producto->execute();
                            $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
                            $info_stock = $stockManager->obtenerInfoStock($id_producto);
                        } else {
                            $mensaje = "Cantidad inválida para aumentar.";
                            $tipo_mensaje = "error";
                        }
                        break;
                        
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
                            $info_stock = $stockManager->obtenerInfoStock($id_producto);
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
        
        <!-- Información de Stock -->
        <?php if ($info_stock): ?>
            <div class="stock-info <?php 
                echo ($info_stock['ESTADO_STOCK'] == 'CRÍTICO' || $info_stock['ESTADO_STOCK'] == 'SIN STOCK') ? 'stock-critico' : 
                     ($info_stock['ESTADO_STOCK'] == 'BAJO' ? 'stock-bajo' : 'stock-normal'); 
            ?>">
                <h3>📦 Información de Stock</h3>
                <p><strong>Stock Actual:</strong> <?php echo $info_stock['STOCK_ACTUAL']; ?> unidades</p>
                <p><strong>Stock Mínimo:</strong> <?php echo $info_stock['PRODUCTO_STOCK_MIN']; ?> unidades</p>
                <p><strong>Estado:</strong> 
                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $info_stock['ESTADO_STOCK'])); ?>">
                        <?php echo $info_stock['ESTADO_STOCK']; ?>
                    </span>
                </p>
                
                <?php if ($info_stock['ESTADO_STOCK'] == 'CRÍTICO' || $info_stock['ESTADO_STOCK'] == 'SIN STOCK'): ?>
                    <p style="color: #721c24; font-weight: bold;">⚠️ ¡Atención! Stock crítico - Considerar reabastecer</p>
                <?php elseif ($info_stock['ESTADO_STOCK'] == 'BAJO'): ?>
                    <p style="color: #856404; font-weight: bold;">⚠️ Stock bajo - Planificar reabastecimiento</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Acciones de Stock -->
        <div class="stock-actions">
            <div class="stock-form">
                <h4>📉 Descontar Stock</h4>
                <form method="POST">
                    <input type="hidden" name="accion" value="descontar_stock">
                    <div class="form-group">
                        <label for="cantidad_descontar">Cantidad a descontar:</label>
                        <input type="number" id="cantidad_descontar" name="cantidad" min="1" 
                               max="<?php echo $info_stock['STOCK_ACTUAL'] ?? 0; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('¿Confirmar descuento de stock?');">
                        Descontar Stock
                    </button>
                </form>
            </div>
            
            <div class="stock-form">
                <h4>📈 Aumentar Stock</h4>
                <form method="POST">
                    <input type="hidden" name="accion" value="aumentar_stock">
                    <div class="form-group">
                        <label for="cantidad_aumentar">Cantidad a aumentar:</label>
                        <input type="number" id="cantidad_aumentar" name="cantidad" min="1" max="9999" required>
                    </div>
                    <div class="form-group">
                        <label for="motivo">Motivo:</label>
                        <select name="motivo" id="motivo">
                            <option value="Reposición manual">Reposición manual</option>
                            <option value="Compra nueva">Compra nueva</option>
                            <option value="Devolución">Devolución</option>
                            <option value="Corrección inventario">Corrección inventario</option>
                            <option value="Producción">Producción</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" 
                            onclick="return confirm('¿Confirmar aumento de stock?');">
                        Aumentar Stock
                    </button>
                </form>
            </div>
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
                    <h4>🔄 Reducir Stock Mínimo a 0</h4>
                    <p>Marca el producto como sin stock mínimo manteniendo el historial de pedidos.</p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="accion" value="reducir_stock">
                        <button type="submit" class="btn btn-warning" 
                                onclick="return confirm('¿Reducir el stock mínimo a 0? Esto cambiará el umbral de alerta.');">
                            Reducir Stock Mínimo
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
        <?php else: ?>
            <div class="alert alert-success">
                <h4>✅ Producto sin Referencias</h4>
                <p>Este producto no está siendo utilizado en ningún pedido. Se puede eliminar de forma segura.</p>
                <form method="POST" action="borrar_producto.php" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $producto['ID_PRODUCTO']; ?>">
                    <button type="submit" class="btn btn-primary"
                           onclick="return confirm('¿Confirmar eliminación del producto?');">
                        Eliminar Producto
                    </button>
                </form>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="productostabla.php" class="btn btn-secondary">Volver a la Lista</a>
            <a href="editar_producto.php?id=<?php echo $producto['ID_PRODUCTO']; ?>" class="btn btn-success">Editar Producto</a>
        </div>
        
        <!-- Opción de eliminación forzada -->
        <div style="text-align: center; margin-top: 20px; padding: 15px; background-color: #f8d7da; border-radius: 8px; border: 1px solid #f5c6cb;">
            <h4 style="color: #721c24; margin-top: 0;">⚠️ Zona Peligrosa</h4>
            <p style="color: #721c24; font-size: 14px;">
                La eliminación forzada ignora todas las restricciones y puede causar inconsistencias en la base de datos.
            </p>
            <a href="eliminar_forzado.php?id=<?php echo $producto['ID_PRODUCTO']; ?>" 
               class="btn" 
               style="background-color: #dc3545; color: white; border: 2px solid #dc3545;"
               onclick="return confirm('🚨 ELIMINACIÓN FORZADA 🚨\\n\\nEsto eliminará PERMANENTEMENTE el producto ignorando todas las referencias.\\n\\n⚠️ PUEDE CAUSAR INCONSISTENCIAS EN LA BASE DE DATOS ⚠️\\n\\n¿Estás ABSOLUTAMENTE SEGURO de proceder?');">
                🗑️ Forzar Eliminación
            </a>
        </div>
    </div>
</body>
</html>