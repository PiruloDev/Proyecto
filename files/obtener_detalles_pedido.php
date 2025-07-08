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
$query_detalles = "SELECT dp.*, pr.NOMBRE_PRODUCTO as producto_nombre, 
                          pr.TIPO_PRODUCTO_MARCA as producto_imagen
                   FROM Detalle_Pedidos dp
                   JOIN Productos pr ON dp.ID_PRODUCTO = pr.ID_PRODUCTO
                   WHERE dp.ID_PEDIDO = ?";
$stmt_detalles = mysqli_prepare($conexion, $query_detalles);
mysqli_stmt_bind_param($stmt_detalles, 'i', $pedido_id);
mysqli_stmt_execute($stmt_detalles);
$result_detalles = mysqli_stmt_get_result($stmt_detalles);
?>

<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Información del Cliente</h6>
        <div class="card">
            <div class="card-body">
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['cliente_nombre']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['cliente_email']); ?></p>
                <?php if ($pedido['cliente_telefono']): ?>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['cliente_telefono']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Información del Pedido</h6>
        <div class="card">
            <div class="card-body">
                <p><strong>ID Pedido:</strong> #<?php echo $pedido['ID_PEDIDO']; ?></p>
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
                    
                    // Mapear clase CSS según el estado
                    $status_class = '';
                    switch(strtolower($estado_nombre)) {
                        case 'pendiente':
                            $status_class = 'bg-warning';
                            break;
                        case 'en preparación':
                            $status_class = 'bg-info';
                            break;
                        case 'listo para entrega':
                            $status_class = 'bg-success';
                            break;
                        case 'entregado':
                            $status_class = 'bg-primary';
                            break;
                        case 'cancelado':
                            $status_class = 'bg-danger';
                            break;
                        default:
                            $status_class = 'bg-secondary';
                    }
                    ?>
                    <span class="badge <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($estado_nombre); ?>
                    </span>
                </p>
                <p><strong>Total:</strong> <span class="text-success fw-bold">$<?php echo number_format($pedido['TOTAL_PRODUCTO'], 2); ?></span></p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <h6 class="fw-bold mb-3">Productos Pedidos</h6>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Imagen</th>
                    <th>Cantidad</th>
                    <th>Precio Unit.</th>
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
                        <td>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 5px;">
                                <i class="fas fa-bread-slice text-muted"></i>
                            </div>
                        </td>
                        <td><span class="badge bg-primary"><?php echo $detalle['CANTIDAD_PRODUCTO']; ?></span></td>
                        <td>$<?php echo number_format($detalle['PRECIO_UNITARIO'], 2); ?></td>
                        <td><strong>$<?php echo number_format($subtotal, 2); ?></strong></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="table-success">
                    <th colspan="4" class="text-end">Total del Pedido:</th>
                    <th><strong>$<?php echo number_format($pedido['TOTAL_PRODUCTO'], 2); ?></strong></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-end gap-2">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="fas fa-times"></i> Cerrar
    </button>
    <button type="button" class="btn btn-primary" onclick="imprimirPedido(<?php echo $pedido['ID_PEDIDO']; ?>)">
        <i class="fas fa-print"></i> Imprimir
    </button>
</div>

<script>
function imprimirPedido(pedidoId) {
    // Abrir una ventana nueva para imprimir
    window.open(`imprimir_pedido.php?id=${pedidoId}`, '_blank', 'width=800,height=600');
}
</script>
