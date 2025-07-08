<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['usuario_logueado']) || 
    $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || 
    $_SESSION['usuario_tipo'] !== 'admin') {
    
    http_response_code(403);
    exit('Acceso denegado');
}

// Obtener ID del pedido
$pedido_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($pedido_id == 0) {
    http_response_code(400);
    exit('ID de pedido inválido');
}

// Obtener información del pedido
$query_pedido = "SELECT p.*, c.NOMBRE_CLI as cliente_nombre, 
                        c.EMAIL_CLI as cliente_email, c.TELEFONO_CLI as cliente_telefono
                 FROM Pedidos p
                 JOIN Clientes c ON p.ID_CLIENTE = c.ID_CLIENTE
                 WHERE p.ID_PEDIDO = ?";
$stmt = mysqli_prepare($conexion, $query_pedido);
mysqli_stmt_bind_param($stmt, 'i', $pedido_id);
mysqli_stmt_execute($stmt);
$result_pedido = mysqli_stmt_get_result($stmt);
$pedido = mysqli_fetch_assoc($result_pedido);

if (!$pedido) {
    http_response_code(404);
    exit('Pedido no encontrado');
}

// Obtener detalles del pedido
$query_detalles = "SELECT dp.*, pr.NOMBRE_PRODUCTO as producto_nombre
                   FROM Detalle_Pedidos dp
                   JOIN Productos pr ON dp.ID_PRODUCTO = pr.ID_PRODUCTO
                   WHERE dp.ID_PEDIDO = ?";
$stmt_detalles = mysqli_prepare($conexion, $query_detalles);
mysqli_stmt_bind_param($stmt_detalles, 'i', $pedido_id);
mysqli_stmt_execute($stmt_detalles);
$result_detalles = mysqli_stmt_get_result($stmt_detalles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #<?php echo $pedido['ID_PEDIDO']; ?> - Panadería</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #bb9467;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #bb9467;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .pedido-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-section {
            flex: 1;
            margin-right: 20px;
        }
        .info-section:last-child {
            margin-right: 0;
        }
        .info-section h3 {
            color: #bb9467;
            border-bottom: 1px solid #bb9467;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-section p {
            margin: 5px 0;
        }
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .productos-table th,
        .productos-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .productos-table th {
            background-color: #bb9467;
            color: white;
        }
        .productos-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            background-color: #bb9467 !important;
            color: white;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🥖 Panadería Artesanal</h1>
        <p>Delicias frescas todos los días</p>
        <p>Teléfono: (555) 123-4567 | Email: info@panaderia.com</p>
    </div>

    <div class="pedido-info">
        <div class="info-section">
            <h3>Información del Cliente</h3>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['cliente_nombre']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['cliente_email']); ?></p>
            <?php if ($pedido['cliente_telefono']): ?>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['cliente_telefono']); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="info-section">
            <h3>Información del Pedido</h3>
            <p><strong>Número de Pedido:</strong> #<?php echo $pedido['ID_PEDIDO']; ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['FECHA_INGRESO'])); ?></p>
            <p><strong>Estado:</strong> 
                <?php
                // Obtener el estado actual del pedido
                $estado_query = "SELECT NOMBRE_ESTADO FROM Estado_Pedidos WHERE ID_ESTADO_PEDIDO = ?";
                $estado_stmt = mysqli_prepare($conexion, $estado_query);
                mysqli_stmt_bind_param($estado_stmt, 'i', $pedido['ID_ESTADO_PEDIDO']);
                mysqli_stmt_execute($estado_stmt);
                $estado_result = mysqli_stmt_get_result($estado_stmt);
                $estado_row = mysqli_fetch_assoc($estado_result);
                $estado_nombre = $estado_row['NOMBRE_ESTADO'] ?? 'Desconocido';
                echo htmlspecialchars($estado_nombre);
                ?>
            </p>
        </div>
    </div>

    <table class="productos-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_verificacion = 0;
            while($detalle = mysqli_fetch_assoc($result_detalles)): 
                $subtotal = $detalle['CANTIDAD_PRODUCTO'] * $detalle['PRECIO_UNITARIO'];
                $total_verificacion += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($detalle['producto_nombre']); ?></td>
                    <td><?php echo $detalle['CANTIDAD_PRODUCTO']; ?></td>
                    <td>$<?php echo number_format($detalle['PRECIO_UNITARIO'], 2); ?></td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>TOTAL:</strong></td>
                <td><strong>$<?php echo number_format($pedido['TOTAL_PRODUCTO'], 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>¡Gracias por su compra!</p>
        <p>Documento generado el <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="background: #bb9467; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            🖨️ Imprimir
        </button>
        <button onclick="window.close()" style="background: #666; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ❌ Cerrar
        </button>
    </div>

    <script>
        // Auto-imprimir al cargar la página
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        });
    </script>
</body>
</html>
